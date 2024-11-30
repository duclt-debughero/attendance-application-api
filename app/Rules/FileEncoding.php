<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileEncoding implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param string $label
     * @param string $encoding
     * @return void
     */
    public function __construct(
        private string $label,
        private string $encoding,
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $contents = file_get_contents($value->getRealPath());
        $checkFileEncoding = mb_check_encoding($contents, $this->encoding);

        if (! $checkFileEncoding) {
            $fail(ConfigUtil::getMessage('ECL060', [$this->label]));
        }
    }
}
