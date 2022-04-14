<?php

namespace App\Http\Requests\Api\Order\Rules;

use App\Repositories\Market\MarketRepository;
use Illuminate\Contracts\Validation\Rule;

class OrderPriceRule implements Rule
{
    public $error = 'Invalid order price';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Request variables
        $market = request()->get('market');
        $type = request()->get('type');

        // Skip rule if order is market buy order
        if(order_is_market($type)) return true;

        if(!$value || !is_numeric($value) || (mb_strpos('e', (string)$value) !== false) || (mb_strpos('E', (string)$value) !== false) || math_compare($value, 0) < 1) {
            $this->error = "Invalid format of the price";
            return false;
        }

        // Get market by name
        $market = (new MarketRepository())->get($market);

        // Allowed decimal rule
        if(!math_decimal_validation($value, $market->quote_precision)) {
            $this->error = "Invalid format of the price";
            return false;
        }

        return $value && $value > 0;
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
