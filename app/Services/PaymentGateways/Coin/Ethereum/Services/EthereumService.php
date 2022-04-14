<?php

namespace App\Services\PaymentGateways\Coin\Ethereum\Services;

use App\Events\DepositUpdated;
use App\Events\WithdrawalUpdated;
use App\Mail\Withdrawals\AdminWithdrawalReceived;
use App\Mail\Withdrawals\WithdrawalConfirmed;
use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Deposit\DepositRepository;
use App\Repositories\Network\NetworkRepository;
use App\Repositories\Wallet\WalletRepository;
use App\Repositories\Withdrawal\WithdrawalRepository;
use App\Services\Currency\CurrencyService;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Setting;

class EthereumService {

    private $_request = null;

    public function verifyCallback() {

        // Request instance
        $this->_request = request();

        try {

            /*
             * Validate request and data
             */
            if ($this->_request->get('hash') !== md5(config('app.url') . '123123123')) {
                throw new \Exception('invalid_request');
            }

            /*
            * END Validate request and data
            */

            return true;

        } catch (\Exception $e) {

            Log::error($e);

            return false;
        }
    }

    public function handleCallback() {

        // Request instance
        $this->_request = request();

        // Get ipn type
        $this->type = $this->_request->get('ipn_type');

        switch ($this->type) {
            case "eth.deposit":
                return $this->handleDeposit('eth');
            case "erc.deposit":
                return $this->handleDeposit('erc20');
            case "eth.withdrawal" :
                return $this->handleWithdraw('eth');
            case "erc.withdrawal":
                return $this->handleWithdraw('erc20');
        }

        return false;
    }

    public function handleDeposit($type) {

        return DB::transaction(function () use ($type) {

            $source = request()->get('deposit_id', null);

            if($type == "eth") {
                $symbol = 'ETH';
            } else {
                $symbol = request()->get('symbol', null);
            }

            if(!$source) return false;

            $depositRepository = new DepositRepository();

            $network = (new NetworkRepository())->getIdBySlug($type);

            $deposit = $depositRepository->getBySource($source, $network->id);

            $amount = request()->get('amount');

            $status = DEPOSIT_PENDING;

            if($type == "eth") {
                $currency = (new CurrencyRepository())->getCurrencyBySymbol($symbol, 'coin', false);
            } else {
                $currency = (new CurrencyRepository())->getCurrencyByContract(request()->get('contract'), 'coin', false);
            }

            if(!$currency) return false;

            $wallet = (new WalletRepository())->getWalletByAddress(request()->get('address'), null, $network->id, $currency->id);

            /*
             * If deposit not found
             */
            if (!$deposit) {

                if(math_compare($amount, $currency->min_deposit) < 0) {
                    $status = DEPOSIT_IGNORED;
                }

                $data = [
                    'deposit_id' => generate_uuid(),
                    'txn' => request()->get('txn'),
                    'source_id' => request()->get('deposit_id'),
                    'currency_id' => $currency->id,
                    'type' => 'coin',
                    'network_id' => $network->id,
                    'amount' => $amount,
                    'network_fee' => request()->get('fee', 0),
                    'address' => request()->get('address'),
                    'user_id' => $wallet->user_id,
                    'confirms' => request()->get('confirms', 0),
                    'status' => $status,
                    'initial_raw' => json_encode(request()->all())
                ];

                $storedDeposit = $depositRepository->store($data);

                $deposit = $depositRepository->getDeposit($storedDeposit->id);

                // Calculate system fee
                $systemFee = (new CurrencyService())->calculateSystemFee('deposit', $currency, $deposit->amount);

                // Store system fee
                $deposit->system_fee = $systemFee;
                $deposit->update();

                if($status != DEPOSIT_IGNORED) {
                    event(new DepositUpdated($deposit->fresh(), 'received'));
                }

                return true;
            }

        }, DB_REPEAT_AFTER_DEADLOCK);
    }

    public function handleWithdraw($type) {

        return DB::transaction(function () use ($type) {

            $source = request()->get('withdraw_id');

            $withdrawalRepository = new WithdrawalRepository();

            $network = (new NetworkRepository())->getIdBySlug($type);

            $withdrawal = $withdrawalRepository->getBySource($source, $network->id);

            if(!$withdrawal) return false;

            $status = request()->get('status');

            $walletService = new WalletService();

            $wallet = (new WalletRepository())->getWalletByCurrency($withdrawal->user_id, $withdrawal->currency->id);

            if ($status == ETHEREUM_WITHDRAW_CONFIRMED && $withdrawal->status == WITHDRAWAL_WAITING_PROVIDER_APPROVAL) {

                $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');

                $withdrawal->txn = request()->get('txn');
                $withdrawal->status = WITHDRAWAL_CONFIRMED_BY_PROVIDER;
                $withdrawal->raw = json_encode(request()->all());
                $withdrawal->update();

                // Notify user
                Mail::to($withdrawal->user)->send(new WithdrawalConfirmed($withdrawal->user, $withdrawal->amount, $withdrawal->currency->symbol, $withdrawal->txn));

                // Admin Email Notification
                $adminEmail = Setting::get('notification.admin_email', false);
                $notificationAllowed = Setting::get('notification.crypto_withdrawals', false);

                if($adminEmail && $notificationAllowed) {
                    $route = route('admin.reports.withdrawals') . "?search=" . $withdrawal->withdrawal_id;
                    Mail::to($adminEmail)->send(new AdminWithdrawalReceived($withdrawal->amount, $withdrawal->currency->symbol, $route));
                }
                // END Admin Email Notification

            } elseif ($status < ETHEREUM_WITHDRAW_FAILED) {

                $walletService->decrease($wallet, $withdrawal->amount, 'withdraw');
                $walletService->increase($wallet, $withdrawal->amount);

                $withdrawal->status = WITHDRAWAL_FAILED;
                $withdrawal->raw = json_encode(request()->all());
                $withdrawal->update();
            }

            event(new WithdrawalUpdated($withdrawal));

            return true;

        }, DB_REPEAT_AFTER_DEADLOCK);
    }
}
