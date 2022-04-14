<?php

namespace App\Http\Requests\Web\Wallet\Rules;

use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Contracts\Validation\Rule;

class FiatWithdrawAmountRule implements Rule
{
    private $message;
    private $currencyRepository;
    private $walletRepository;

    public function __construct()
    {
        $this->currencyRepository = new CurrencyRepository();
        $this->walletRepository = new WalletRepository();
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
        $user = auth()->user();

        $currency = $this->currencyRepository->get(request()->get('currency_id'));
        $wallet = $this->walletRepository->getWalletByCurrency($user->id, $currency->id, false);

        if(!$currency || !$wallet) {
            $this->message = 'Invalid currency';
            return false;
        }

        if(math_compare($value, $currency->min_withdraw) < 0) {
            $this->message = 'Minimum withdrawal amount is ' . $currency->min_withdraw . ' ' . $currency->symbol;
            return false;
        }

        if($currency->max_withdraw > 0 && math_compare($value, $currency->max_withdraw) > 0) {
            $this->message = 'Maximum withdrawal amount is ' . $currency->max_withdraw . ' ' . $currency->symbol;
            return false;
        }

        if($value <= 0) {
            $this->message = "Invalid amount";
            return false;
        }

        if (!$wallet) {
            return false;
        }

        if($wallet->balance_in_wallet < $value) {
            $this->message = 'Insufficient balance';
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
