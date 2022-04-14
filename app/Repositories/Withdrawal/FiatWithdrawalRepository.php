<?php

namespace App\Repositories\Withdrawal;

use App\Interfaces\Withdrawal\WithdrawalRepositoryInterface;
use App\Mail\Withdrawals\AdminWithdrawalReceived;
use App\Mail\Withdrawals\WithdrawalConfirmed;
use App\Mail\Withdrawals\WithdrawalRejected;
use App\Models\User\User;
use App\Models\Withdrawal\FiatWithdrawal;
use App\Repositories\Wallet\WalletRepository;
use App\Services\Currency\CurrencyService;
use App\Services\Wallet\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Setting;

class FiatWithdrawalRepository implements WithdrawalRepositoryInterface
{
    /**
     * @var FiatWithdrawal
     */
    protected $withdrawal;

    /**
     * FiatWithdrawalRepository constructor.
     *
     */
    public function __construct()
    {
        $this->withdrawal = new FiatWithdrawal();
    }

    public function get() {

        $withdrawal = FiatWithdrawal::query();

        $withdrawal->with('currency');

        $withdrawal->orderBy('created_at', 'desc');

        $withdrawal->limit(10);

        return $withdrawal->get();
    }

    public function getReport() {

        $withdrawal = FiatWithdrawal::query();

        $withdrawal->filter(request()->only(['search']))->orderByLatest();

        $withdrawal->with(['currency', 'user', 'country']);

        return $withdrawal->paginate(50)->withQueryString();
    }

    public function getReportUser(User $user, $pagination = true) {

        $withdrawal = FiatWithdrawal::query();

        $withdrawal->filterUser(request()->only(['currency', 'status']))->orderByLatest();

        $withdrawal->with(['currency']);

        $withdrawal->where('user_id', $user->id);

        if(!$pagination) {
            $withdrawal->limit(15);
            return $withdrawal->get();
        }

        return $withdrawal->paginate(50)->withQueryString();
    }


    public function count() {

        $withdrawal = FiatWithdrawal::query();

        return $withdrawal->count();
    }

    public function getWithdrawal($id) {
        return FiatWithdrawal::with('currency')->whereId($id)->first();
    }

    public function store($data) {
        return $this->withdrawal->create($data);
    }

    public function update($withdrawal, $data) {
        return $withdrawal->update($data);
    }

    /**
     * Moderate Fiat Deposit
     */
    public function moderate($withdrawal, $action) {

        DB::transaction(function () use ($withdrawal, $action) {

            $walletRepository = new WalletRepository();
            $walletService = new WalletService();
            $currencyService = new CurrencyService();

            $wallet = $walletRepository->getWalletByCurrency($withdrawal->user_id, $withdrawal->currency_id);

            if($action == "approve") {
                $withdrawal->status = FIAT_WITHDRAWAL_CONFIRMED;
                $withdrawal->approved_at = Carbon::now();
                $withdrawal->note = nl2br(request()->get('note'));

                // Decrease user wallet
                $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');

                $currencyService->decrease($withdrawal->currency, $withdrawal->amount);
                $withdrawal->currency->wallet_balance_updated_at = Carbon::now();
                $withdrawal->currency->update();

                Mail::to($withdrawal->user)->send(new WithdrawalConfirmed($withdrawal->user, math_formatter($withdrawal->amount, $withdrawal->currency->decimals), $withdrawal->currency->symbol, $withdrawal->note));

            } else {
                $withdrawal->status = FIAT_WITHDRAWAL_REJECTED;
                $withdrawal->rejected_at = Carbon::now();
                $withdrawal->rejected_reason = nl2br(request()->get('reason'));

                // Decrease user wallet
                $walletService->increase($wallet, $withdrawal->amount, 'wallet');
                $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');

                Mail::to($withdrawal->user)->send(new WithdrawalRejected($withdrawal->user, math_formatter($withdrawal->amount, $withdrawal->currency->decimals), $withdrawal->currency->symbol, $withdrawal->rejected_reason));
            }

            $withdrawal->save();

        }, DB_REPEAT_AFTER_DEADLOCK);
    }

    public function processWithdraw($data, $currency) {

        return DB::transaction(function () use ($data, $currency) {

            $this->store($data);

            $wallet = (new WalletRepository())->getWalletByCurrency($data['user_id'], $currency->id, true);

            (new WalletService())->decrease($wallet, $data['amount'], 'wallet');
            (new WalletService())->increase($wallet, $data['amount'], 'withdraw');

            // Admin Email Notification
            $adminEmail = Setting::get('notification.admin_email', false);
            $notificationAllowed = Setting::get('notification.fiat_withdrawals', false);

            if($adminEmail && $notificationAllowed) {
                $route = route('admin.reports.withdrawals.fiat') . "?search=" . $data['withdrawal_id'];
                Mail::to($adminEmail)->send(new AdminWithdrawalReceived(math_formatter($data['amount'], $currency->decimals), $currency->symbol, $route));
            }
            // END Admin Email Notification

            return true;

        }, DB_REPEAT_AFTER_DEADLOCK);
    }
}
