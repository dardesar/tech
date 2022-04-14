<?php

namespace App\Http\Requests\Web\Network;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class NetworkFormRequest extends FormRequest
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
            'id' => ['required', 'exists:networks'],
            'name' => ['bail', 'required', 'max:50'],
            'slug' => ['bail', 'required', 'max:50'],
            'status' => ['required', 'boolean'],
        ];
    }
}
