<?php

namespace App\Http\Requests\Web\Wallet\Rules;

use App\Models\FileUpload\FileUpload;
use App\Repositories\Currency\CurrencyRepository;
use Illuminate\Contracts\Validation\Rule;

class FiatDepositAmountRule implements Rule
{
    public $errorMessage = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $amount)
    {
        $currency = (new CurrencyRepository())->get(request()->get('currency_id'));

        if(math_compare($amount, $currency->min_deposit) < 0) {
            $this->errorMessage = 'Minimum deposit amount is ' . math_formatter($currency->min_deposit, $currency->decimals) . ' ' . $currency->symbol;
            return false;
        }

        if(math_compare($amount, $currency->max_deposit) > 0) {
            $this->errorMessage = 'Maximum deposit amount is ' . math_formatter($currency->max_deposit, $currency->decimals) . ' ' . $currency->symbol;
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
        return $this->errorMessage;
    }
}
