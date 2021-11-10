<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinCurrency implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function passes($attribute, $value, $parameters = [])
    {
        return $value >= $parameters[0];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
