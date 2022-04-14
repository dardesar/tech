<?php

namespace App\Http\Requests\Web\Wallet;

use App\Http\Requests\Web\Wallet\Rules\FiatWithdrawAmountRule;
use App\Http\Requests\Web\Wallet\Rules\FiatWithdrawCurrencyRule;
use Illuminate\Foundation\Http\FormRequest;

class FiatWithdrawFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency_id' => ['bail','required', 'integer', 'numeric', new FiatWithdrawCurrencyRule()],
            'name' => ['required', 'max:255'],
            'country_id' => ['required', 'integer', 'numeric', 'exists:countries,id'],
            'iban' => ['required', 'max:255'],
            'swift' => ['required', 'max:255'],
            'ifsc' => ['nullable', 'max:255'],
            'address' => ['required', 'max:255'],
            'account_holder_name' => ['required', 'max:255'],
            'account_holder_address' => ['required', 'max:255'],
            'amount' => ['bail','required', 'numeric', new FiatWithdrawAmountRule()],
        ];
    }
}
