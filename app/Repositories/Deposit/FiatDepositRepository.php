<?php

namespace App\Repositories\Deposit;

use App\Interfaces\Deposit\DepositRepositoryInterface;
use App\Mail\Deposits\DepositReceived;
use App\Mail\Deposits\DepositRejected;
use App\Models\Deposit\FiatDeposit;
use App\Models\User\User;
use App\Repositories\Wallet\WalletRepository;
use App\Services\Currency\CurrencyService;
use App\Services\Wallet\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FiatDepositRepository implements DepositRepositoryInterface
{
    /**
     * @var FiatDeposit
     */
    protected $deposit;

    /**
     * FiatDepositRepository constructor.
     *
     */
    public function __construct()
    {
        $this->deposit = new FiatDeposit();
    }

    public function get($type = null) {

        $deposit = FiatDeposit::query();

        $deposit->with('currency');

        if($type == 'bank') {
            $deposit->bank();
        } elseif($type == "cc") {
            $deposit->cc();
        }

        $deposit->orderBy('created_at', 'desc');

        $deposit->limit(10);

        return $deposit->get();
    }

    public function getReport($type = null) {

        $deposit = FiatDeposit::query();

        $deposit->filter(request()->only(['search', 'type']))->orderByLatest();

        $deposit->with(['currency', 'user', 'receipt']);

        if($type == 'bank') {
            $deposit->cc();
        } elseif($type == 'cc') {
            $deposit->bank();
        }

        return $deposit->paginate(50)->withQueryString();
    }

    public function getReportUser(User $user, $type = 'all', $pagination = true) {

        $deposit = FiatDeposit::query();

        $deposit->filterUser(request()->only(['currency', 'status']))->orderByLatest();

        $deposit->with(['currency']);

        if($type == 'cc') {
            $deposit->cc();
        } elseif($type == "bank") {
            $deposit->bank();
        }

        $deposit->where('user_id', $user->id);

        if(!$pagination) {
            $deposit->limit(15);
            return $deposit->get();
        }

        return $deposit->paginate(50)->withQueryString();
    }

    public function count() {

        $deposit = FiatDeposit::query();

        return $deposit->count();
    }

    public function getDeposit($id) {
        return FiatDeposit::with('currency')->whereId($id)->first();
    }

    public function store($data) {
        return $this->deposit->create($data);
    }

    public function update($deposit, $data) {
        return $deposit->update($data);
    }

    /**
     * Moderate Fiat Deposit
     */
    public function moderate($deposit, $action) {

        DB::transaction(function () use ($deposit, $action) {

            $walletRepository = new WalletRepository();
            $walletService = new WalletService();
            $currencyService = new CurrencyService();

            if($action == "approve") {
                $deposit->status = FIAT_DEPOSIT_CONFIRMED;
                $deposit->approved_at = Carbon::now();

                $currencyService->increase($deposit->currency, $deposit->amount);
                $deposit->currency->wallet_balance_updated_at = Carbon::now();
                $deposit->currency->update();

                // Calculate fee
                $fee = math_percentage($deposit->amount, $deposit->currency->deposit_fee);
                $amount = math_sub($deposit->amount, $fee);

                // Increase user wallet
                $wallet = $walletRepository->getWalletByCurrency($deposit->user_id, $deposit->currency_id);
                $walletService->increase($wallet, $amount);

                Mail::to($deposit->user)->send(new DepositReceived($deposit->user, math_formatter($amount, $deposit->currency->decimals), $deposit->currency->symbol));

            } else {
                $deposit->status = FIAT_DEPOSIT_REJECTED;
                $deposit->rejected_at = Carbon::now();
                $deposit->rejected_reason = nl2br(request()->get('reason'));

                Mail::to($deposit->user)->send(new DepositRejected($deposit->user, math_formatter($deposit->amount, $deposit->currency->decimals), $deposit->currency->symbol, $deposit->rejected_reason));
            }

            $deposit->save();

        }, DB_REPEAT_AFTER_DEADLOCK);
    }
}
