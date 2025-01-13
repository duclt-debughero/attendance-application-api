<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileExtension implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param string $label
     * @param array $extensions
     */
    public function __construct(
        private string $label,
        private array $extensions,
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
        $checkFileExtension = in_array(strtolower($value->getClientOriginalExtension()), $this->extensions);

        if (! $checkFileExtension) {
            $fail(ConfigUtil::getMessage('ECL018', [$this->label]));
        }
    }
}
