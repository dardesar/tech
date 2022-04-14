<?php

namespace App\Http\Requests\Web\User;

use App\Http\Requests\Web\User\Rules\DocumentsUploadRule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class KycFormRequest extends FormRequest
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
            'first_name' => ['bail', 'required', 'min:1', 'max:60'],
            'last_name' => ['bail', 'required', 'min:1', 'max:60'],
            'middle_name' => ['bail', 'max:60'],
            'country_id' => ['required', 'integer', 'numeric', 'exists:countries,id'],
            'document_type' => ['bail', 'required', Rule::in(['id', 'passport', 'driver_license', 'residence_permit'])],
            'documents' => ['bail', 'required', new DocumentsUploadRule()],
            'selfie_id' => ['bail', 'required', 'numeric', 'exists:file_uploads,id'],
        ];
    }
}
