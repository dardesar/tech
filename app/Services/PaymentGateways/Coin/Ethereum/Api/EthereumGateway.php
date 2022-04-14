<?php
namespace App\Services\PaymentGateways\Coin\Ethereum\Api;

use App\Interfaces\PaymentsGateways\Coin\CoinGatewayInterface;
use App\Models\CoinpaymentsCoin;
use Illuminate\Support\Facades\Log;
use Setting;

class EthereumGateway implements CoinGatewayInterface
{
    /**
     * Used to call request to Coinpayments API
     *
     * @param $uri
     * @param array $params
     * @return false|mixed
     */
    public function request($uri, $params = [])
    {
        try {
            return get_ethereum_request($uri, $params);
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * Used to generate address wallet per currency
     * @return array
     */
    public function createEthAddress() {

        // Get new wallet address
        $wallet = $this->request('node/wallet/store', ['type' => 'eth']);

        // If new wallet created return wallet
        return $wallet;
    }

    /**
     * Used to generate address wallet per currency
     * @return array
     */
    public function createErcAddress() {

        // Get new wallet address
        $wallet = $this->request('node/wallet/store', ['type' => 'erc']);

        // If new wallet created return wallet
        return $wallet;
    }

    /**
     * Used to get network confirmations by hash+
     * @return array
     */
    public function getNetworkConfirmations($hash, $type) {
        // Get network confirmation by hash
        return $this->request('node/blockchain/confirmations', ['hash' => $hash, 'type' => $type]);
    }

    /**
     * Used to get eth/erc balance
     * @return array
     */
    public function getBalance($address, $contract = null) {
        return $this->request('node/wallet/balance', [
            'address' => $address,
            'contract' => $contract
        ]);

    }

    /**
     * Used to generate data to withdraw
     * @param $address
     * @param $amount
     * @return array
     */
    public function withdraw($type, $address, $amount, $contract = '')
    {
        $params = [
            'type' => $type,
            'address' => $address,
            'amount' => $amount,
            'contract' => $contract,
        ];

        $response = $this->request('node/wallet/withdraw', $params);

        $source = '';
        $message = $response['message'] ?? '';

        if(!isset($response['status']) || $response['status'] != STATUS_OK) {
            $status = STATUS_VALIDATION_ERROR;
        } else {
            $source = $response['message'];
            $status = STATUS_OK;
        }

        return [
            'status' => $status,
            'source' => $source,
            'message' => $message,
        ];
    }

    public function registerSettings() {
        return $this->request('node/wallet/register', Setting::get('ethereum'));
    }

    public function registerContract($contract) {
        return $this->request('node/contract/register', ['contract' => $contract]);
    }

    public function ping() {
        return $this->request('node/ping');
    }
}
