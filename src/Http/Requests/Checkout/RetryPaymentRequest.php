<?php

namespace RawFocus\Webshop\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class RetryPaymentRequest extends FormRequest
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
            "order_uuid" => "required|exists:orders,uuid",
        ];
    }
}
