<?php

namespace App\Http\Requests\Api\Stripe;

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