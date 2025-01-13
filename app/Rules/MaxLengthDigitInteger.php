<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLengthDigitInteger implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param int $max
     */
    public function __construct(
        private $max,
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
        if (isset($value)) {
            $parts = explode('.', $value);

            // Check if there is an integer part
            if (count($parts) > 1) {
                $currentLength = strlen($parts[0]);
                if (! ($currentLength <= $this->max)) {
                    $fail(ConfigUtil::getMessage('ECL002', [':attribute'.'integer value', $this->max, $currentLength]));
                }
            }
        }
    }
}
