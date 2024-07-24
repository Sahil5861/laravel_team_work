<?php
// App\Rules\ImageRequiredWithoutUpload.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageRequiredWithoutUpload implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        // Check if there is no image uploaded and no previous image stored
        return request()->file('image') || request()->filled('old_image');
    }

    public function message()
    {
        return 'The :attribute field is required when no image is uploaded.';
    }
}
