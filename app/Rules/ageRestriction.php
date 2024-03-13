<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ageRestriction implements Rule
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
     * @return bool
     */
    public function passes($attribute, $value)
    {
      return now()->diff($value)->y > 5;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
      return 'The :attribute must not be for a person with 5 years old below.';
    }
}
