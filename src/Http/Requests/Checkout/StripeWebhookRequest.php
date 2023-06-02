<?php

namespace Raw\Webshop\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class StripeWebhookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            "id" => "required",
            "object" => "required",
            "data" => "required",
            "type" => "required"
        ];
    }
}