<?php

namespace App\Repositories\Wallet;

use App\Interfaces\Wallet\WalletRepositoryInterface;
use App\Models\Currency\Currency;
use App\Models\Wallet\Wallet;
use App\Models\Wallet\WalletAddress;
use App\Models\Withdrawal\Withdrawal;
use App\Services\PaymentGateways\Coin\Coinpayments\Api\CoinpaymentsGateway;
use App\Services\PaymentGateways\Coin\Ethereum\Api\EthereumGateway;
use App\Services\Wallet\WalletService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WalletRepository implements WalletRepositoryInterface
{
    public function getWallets($user_id) {
        return Wallet::where('user_id', $user_id)->get();
    }

    public function getWallet($id) {
        return Wallet::find($id);
    }

    public function getWalletByCurrency($user_id, $currency, $lock = true) {
        $wallet = Wallet::query();

        $wallet->where('user_id', $user_id)->where('currency_id', $currency);

        if($lock) {
            $wallet->lockForUpdate();
        }

        return $wallet->first();
    }

    public function getWalletByAddress($address, $payment_id, $network, $currency) {

        $walletAddress = WalletAddress::query();

        $walletAddress->whereAddress($address);

        $walletAddress->where('network_id', $network);

        if($payment_id) {
            $walletAddress->where('payment_id', $payment_id);
        }

        if(!$walletAddress->exists()) return null;

        return Wallet::whereId($walletAddress->first()->wallet_id)->where('currency_id', $currency)->first();
    }

    public function getWalletAddress($wallet, $currency, $network) {

        try {
            $walletAddress = WalletAddress::where('wallet_id', $wallet->id)->first();

            $address = $paymentId = null;

            if (!$walletAddress) {
                switch ($network->slug) {
                    case "coinpayments":
                        $generatedAddress = (new CoinpaymentsGateway())->createAddress($currency->alt_symbol);
                        $address = $generatedAddress['address'];
                        $paymentId = $generatedAddress['dest_tag'];
                        break;
                    case "eth":
                        $generatedAddress = (new EthereumGateway())->createEthAddress();
                        $address = $generatedAddress['address'];
                        break;
                    case "erc20":
                        $generatedAddress = (new EthereumGateway())->createErcAddress();
                        $address = $generatedAddress['address'];
                        break;
                }

                $walletAddress = new WalletAddress();
                $walletAddress->address = $address;
                $walletAddress->payment_id = $paymentId;
                $walletAddress->wallet_id = $wallet->id;
                $walletAddress->network_id = $network->id;
                $walletAddress->save();
            }

            return $walletAddress;

        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    public function withdrawCrypto(Withdrawal $withdrawal) {

        $response = null;

        $amountAfterFee = math_sub($withdrawal->amount, $withdrawal->fee);

        try {
            switch ($withdrawal->network->slug) {
                case "coinpayments":
                    $response = (new CoinpaymentsGateway())->withdraw($withdrawal->address, $withdrawal->payment_id, $amountAfterFee, $withdrawal->currency);
                    break;
                case "eth":
                    $response = (new EthereumGateway())->withdraw('eth', $withdrawal->address, $amountAfterFee);
                    break;
                case "erc20":
                    $response = (new EthereumGateway())->withdraw('erc', $withdrawal->address, $amountAfterFee, $withdrawal->currency->contract);
                    break;
            }

            return $response;

        } catch (\Exception $e) {
            Log::error($e);
            return [
                'source' => null,
                'status' => STATUS_VALIDATION_ERROR,
                'message' => 'withdraw_failed'
            ];
        }
    }

    public function store($data)
    {
        return Wallet::insert($data);
    }

    public function getReport() {

        $wallet = Wallet::query();

        $wallet->filter(request()->only(['search','type']))->orderByLatest();

        $wallet->with(['currency', 'user', 'address']);

        return $wallet->paginate(50)->withQueryString();
    }
}
