<?php

namespace Raw\Webshop\Rules;

use WebshopProducts;

use Illuminate\Contracts\Validation\Rule;

class ValidProducts implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($value as $data)
        {
            $product = WebshopProducts::findByUuid($data["product"]["uuid"]);
            if (!$product || $product->stock - $data["quantity"] <= 0)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("validation.product_not_available");
    }
}
