<?php

namespace Raw\Webshop\Requests;

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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "products" => "required",
            "address_street" => "required",
            "address_country" => "required",
            "address_postal_code" => "required",
            "address_city" => "required",
        ];
    }
}
