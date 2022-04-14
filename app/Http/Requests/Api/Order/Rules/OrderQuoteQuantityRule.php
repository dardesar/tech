<?php

namespace App\Http\Requests\Api\Order\Rules;

use App\Repositories\Market\MarketRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Contracts\Validation\Rule;
use Auth;

class OrderQuoteQuantityRule implements Rule
{
    /**
     * @var MarketRepository
     * @var WalletRepository
     * @var OrderRepository
     */
    private $marketRepository, $orderRepository, $walletRepository, $error;

    public function __construct()
    {
        $this->marketRepository = new MarketRepository();
        $this->walletRepository = new WalletRepository();
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  numeric $quantity
     * @return bool
     */
    public function passes($attribute, $quantity)
    {
        // Request variables
        $market = request()->get('market');
        $type = request()->get('type');
        $side = request()->get('side');

        if(!order_is_buy_market($type, $side)) {
            return true;
        }

        if(!$quantity || !is_numeric($quantity) || (mb_strpos('e', (string)$quantity) !== false) || (mb_strpos('E', (string)$quantity) !== false) || math_compare($quantity, 0) < 1) {
            $this->error = "Invalid format of the price";
            return false;
        }

        // User id
        $user = Auth::user()->id;

        // Get market by name
        $market = $this->marketRepository->get($market);

        // Check if order quantity follows min trade value rule
        if($market->min_trade_value > 0 && math_compare($quantity, $market->min_trade_value) < 0) {
            $this->error = "Minimum allowed trade value is " . $market->min_trade_value;
            return false;
        }

        // Check if order quantity follows max trade value rule
        if($market->max_trade_value > 0 && math_compare($market->max_trade_value, $quantity) < 0) {
            $this->error = "Maximum allowed trade value is " . $market->max_trade_value;
            return false;
        }

        // Allowed decimal rule
        if(!math_decimal_validation($quantity, $market->quote_precision)) {
            $this->error = "Invalid format of the amount";
            return false;
        }

        // Invalidate if there is no order to fill quick order
        $matchedOrder = $this->orderRepository->getMatchedOrder($type, $side, $market->id, false);

        if(!$matchedOrder) {
            $this->error = 'No Matched Order';
            return false;
        }

        // Get user wallet and its balance
        $wallet = $this->walletRepository->getWalletByCurrency($user, $market->quote_currency_id);

        if(!$wallet || math_sub($wallet->balance_in_wallet, $quantity) < 0) {
            $this->error = 'Insufficient balance';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
