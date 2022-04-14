<?php

namespace App\Http\Requests\Api\Wallet;

use App\Http\Requests\Api\Wallet\Rules\WalletNetworkRule;
use App\Http\Requests\Api\Wallet\Rules\WalletSymbolRule;
use Illuminate\Foundation\Http\FormRequest;

class GetAddressRequest extends FormRequest
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
            'symbol' => ['bail', 'required', new WalletSymbolRule()],
            'network' => ['bail', 'required', new WalletNetworkRule()],
        ];
    }
}
