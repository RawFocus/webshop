<?php

namespace RawFocus\Webshop\Http\Requests\Checkout;

use RawFocus\Webshop\Rules\ValidProducts;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            "products" => ["required", new ValidProducts],
            "street" => "required",
            "postal_code" => "required",
            "city" => "required",
            "country" => "required",
        ];
    }
}
