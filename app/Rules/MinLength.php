<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinLength implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param int $min
     */
    public function __construct(
        private int $min,
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

        if (! ($currentLength >= $this->min)) {
            $fail(ConfigUtil::getMessage('ECL003', [':attribute', $this->min, $currentLength]));
        }
    }
}
