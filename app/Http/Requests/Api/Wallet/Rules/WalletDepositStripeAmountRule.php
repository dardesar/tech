<?php

namespace App\Http\Requests\Api\Wallet\Rules;

use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Contracts\Validation\Rule;

class WalletDepositStripeAmountRule implements Rule
{
    private $message;
    private $currencyRepository;

    public function __construct()
    {
        $this->currencyRepository = new CurrencyRepository();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  string $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $currency = $this->currencyRepository->get(request()->get('currency_id'));

        if(!$currency || !$currency->deposit_status) {
            $this->message = "Invalid currency or deposits are not allowed";
            return false;
        }

        if(math_compare($value, $currency->min_deposit) < 0) {
            $this->message = 'Minimum deposit amount is ' . math_formatter($currency->min_deposit, $currency->decimals) . ' ' . $currency->symbol;
            return false;
        }

        if($currency->max_deposit > 0 && math_compare($value, $currency->max_deposit) > 0) {
            $this->message = 'Maximum deposit amount is ' . math_formatter($currency->max_deposit, $currency->decimals) . ' ' . $currency->symbol;
            return false;
        }

        if($value <= 0) {
            $this->message = "Invalid amount";
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
        return $this->message;
    }
}
