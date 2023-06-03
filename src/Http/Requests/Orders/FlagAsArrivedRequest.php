<?php

namespace Raw\Webshop\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class FlagAsArrivedRequest extends FormRequest
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
            "uuid" => "required|exists:orders,uuid",
        ];
    }
}
