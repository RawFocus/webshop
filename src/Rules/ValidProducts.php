<?php

namespace Raw\Webshop\Rules;

use Webshop;

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
        foreach ($value as $productData)
        {
            $product = Webshop::findProductByUuid($productData["uuid"]);
            if (!$product || $product->stock - $productData["quantity"] <= 0)
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
