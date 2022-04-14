<?php

namespace App\Http\Requests\Api\Order\Rules;

use App\Models\Order\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderTypeRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return order_allowed_types($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid order type';
    }
}
