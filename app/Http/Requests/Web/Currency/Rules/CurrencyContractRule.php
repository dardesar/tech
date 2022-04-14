<?php

namespace App\Http\Requests\Web\Currency\Rules;

use App\Models\Currency\Currency;
use App\Models\Network\Network;
use App\Services\PaymentGateways\Coin\Coinpayments\Model\CoinpaymentsCurrency;
use Illuminate\Contracts\Validation\Rule;

class CurrencyContractRule implements Rule
{
    public $errorMessage = "";

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $contract)
    {
        $id = request()->get('id', false);
        $networks = request()->get('networks', false);

        if(!in_array(NETWORK_ERC, $networks)) {
            return true;
        }

        if(!$contract && in_array(NETWORK_ERC, $networks)) {
            $this->errorMessage = 'ERC-20 Contract is required';
            return false;
        }

        if($contract && Currency::where('contract', $contract)->where('id', '<>', $id)->first()) {
            $this->errorMessage = 'This ERC-20 Contract has already taken by other currency';
            return false;
        }

        if(mb_strlen($contract) != 42) {
            $this->errorMessage = 'Exact length of the contract field is 42 chars';
            return false;
        }

        if(mb_substr($contract, 0, 2) != "0x") {
            $this->errorMessage = 'Valid contract field should start with 0x';
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
