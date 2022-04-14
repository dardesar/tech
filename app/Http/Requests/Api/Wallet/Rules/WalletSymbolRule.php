<?php

namespace App\Http\Requests\Api\Wallet\Rules;

use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Network\NetworkRepository;
use Illuminate\Contracts\Validation\Rule;

class WalletSymbolRule implements Rule
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository, $error;

    public function __construct()
    {
        $this->currencyRepository = new CurrencyRepository();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  string $uuid
     * @return bool
     */
    public function passes($attribute, $symbol)
    {
        $currency = $this->currencyRepository->getCurrencyBySymbol($symbol);

        if(!$currency || $currency->type !== 'coin') {
            $this->error = 'Invalid Currency';
            return false;
        }

        if(!$currency->withdraw_status) {
            $this->error = 'Withdrawal operation was disabled for this currency';
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
