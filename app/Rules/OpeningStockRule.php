<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Purchase;
class OpeningStockRule implements Rule
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
        $count=count($value);
        for ($i=0; $i<$count-1 ; $i++) { 
            $test=Purchase::find($value[$i]);
            if (count($test)>0) {
                return false;
            }else{
                return true;
            }
        }
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
