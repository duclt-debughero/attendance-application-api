<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLength implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param int $max
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
        $value = str_replace("\r\n", "\n", $value);
        $currentLength = mb_strlen($value);

        if (! ($currentLength <= $this->max)) {
            $fail(ConfigUtil::getMessage('ECL002', [':attribute', $this->max, $currentLength]));
        }
    }
}
