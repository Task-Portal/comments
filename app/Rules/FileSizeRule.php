<?php

namespace App\Rules;

use Illuminate\Support\Facades\File;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileSizeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (is_array($value)) {
            foreach ($value as $file) {
                if ( $file->getMimeType() === 'text/plain' && $file->getSize() > 100000) {
                    $fail('The :attribute must not larger than 100KB');
                }
                elseif (!in_array($file->getMimeType(), ['image/jpeg', 'image/gif', 'image/png', 'text/plain'])) {
                    $fail("There should be only types png, jpeg, txt and gif");
                }
            }
        }



    }
}
