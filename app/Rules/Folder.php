<?php

namespace App\Rules;

use Closure;
use App\Models\ArchiveFormat;
use Illuminate\Contracts\Validation\ValidationRule;

class Folder implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // make sure the folder uploaded is in archiveformats extension
        $archiveFormats = ArchiveFormat::select('extension')->get();
        $folder = $value->getClientOriginalExtension();
        if (!$archiveFormats->contains('extension', $folder)) {
            $fail("The $attribute must be a folder with archive formats.");
        }
    }
}
