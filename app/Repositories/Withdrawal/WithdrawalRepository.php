<?php

namespace App\Repositories\Withdrawal;

use App\Interfaces\Withdrawal\WithdrawalRepositoryInterface;
use App\Mail\Withdrawals\WithdrawalRejected;
use App\Models\User\User;
use App\Models\Withdrawal\Withdrawal;
use App\Repositories\Wallet\WalletRepository;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WithdrawalRepository implements WithdrawalRepositoryInterface
{
    /**
     * @var Withdrawal
     */
    protected $withdrawal;

    /**
     * WithdrawalRepository constructor.
     *
     */
    public function __construct()
    {
        $this->withdrawal = new Withdrawal();
    }

    public function get($type = 'coin') {

        $withdrawal = Withdrawal::query();

        $withdrawal->with('currency');

        if($type == 'fiat') {
            $withdrawal->fiat();
        } else {
            $withdrawal->coin();
        }

        $withdrawal->orderBy('created_at', 'desc');

        $withdrawal->limit(10);

        return $withdrawal->get();
    }

    public function getReport($type = 'coin') {

        $withdrawal = Withdrawal::query();

        $withdrawal->filter(request()->only(['search', 'type']))->orderByLatest();

        $withdrawal->with(['currency', 'network', 'user']);

        if($type == 'fiat') {
            $withdrawal->fiat();
        } else {
            $withdrawal->coin();
        }

        return $withdrawal->paginate(50)->withQueryString();
    }

    public function getReportUser(User $user, $pagination = true) {

        $withdrawal = Withdrawal::query();

        $withdrawal->filterUser(request()->only(['currency','txn','status']))->orderByLatest();

        $withdrawal->with(['currency.file']);

        $withdrawal->where('user_id', $user->id);

        if(!$pagination) {
            $withdrawal->limit(15);
            return $withdrawal->get();
        }

        return $withdrawal->paginate(50)->withQueryString();
    }


    public function count() {

        $withdrawal = Withdrawal::query();

        return $withdrawal->count();
    }

    public function getWithdrawal($id) {
        return Withdrawal::with('currency')->whereId($id)->first();
    }

    public function store($data) {
        return $this->withdrawal->create($data);
    }

    public function update($withdrawal, $data) {
        return $withdrawal->update($data);
    }

    public function getBySource($source_id, $network) {
        return Withdrawal::with('currency')->where('source_id', $source_id)->where('network_id', $network)->first();
    }

    /**
     * Moderate Withdrawal
     */
    public function moderate($withdrawal, $action) {

        return DB::transaction(function() use ($withdrawal, $action) {

            if($withdrawal->status != WITHDRAWAL_WAITING_APPROVAL) return false;

            $wallet = (new WalletRepository())->getWalletByCurrency($withdrawal->user_id, $withdrawal->currency_id);
            $walletService = new WalletService();

            if($action == "approve") {

                $withdrawal->status = WITHDRAWAL_CONFIRMED_BY_SYSTEM;
                $withdrawal->save();

                $response = $walletService->withdrawCryptoConfirmed($withdrawal->fresh());

                if($response['status'] == STATUS_VALIDATION_ERROR) {

                    $withdrawal->status = WITHDRAWAL_FAILED;
                    $withdrawal->rejected_reason = $response['message'];
                    $withdrawal->update();

                    // Decrease from withdraw
                    $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');

                    // Increase wallet balance
                    $walletService->increase($wallet, $withdrawal->amount, 'wallet');

                } else {
                    $withdrawal->source_id = $response['source'];
                    $withdrawal->initial_raw = json_encode($response['message']);
                    $withdrawal->status = WITHDRAWAL_WAITING_PROVIDER_APPROVAL;
                    $withdrawal->update();
                }

            } else {

                $withdrawal->status = WITHDRAWAL_REJECTED;
                $withdrawal->rejected_reason = nl2br(request()->get('reason'));
                $withdrawal->save();

                // Decrease from withdraw
                $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');

                // Increase wallet balance
                $walletService->increase($wallet, $withdrawal->amount, 'wallet');

                // Notify user
                Mail::to($withdrawal->user)->send(new WithdrawalRejected($withdrawal->user, $withdrawal->amount, $withdrawal->currency->symbol, $withdrawal->rejected_reason));
            }

            return true;

        }, DB_REPEAT_AFTER_DEADLOCK);
    }


}
