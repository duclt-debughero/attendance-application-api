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
     * @return void
     */
    public function __construct(
        private int $max,
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
        $parts = explode(".", $value);
        $currentLength = strlen($parts[0]);

        if (! ($currentLength <= $this->max)) {
            $fail(ConfigUtil::getMessage('ECL002', [':attribute'.'integer value', $this->max, $currentLength]));
        }
    }
}
