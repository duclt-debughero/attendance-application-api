<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinLengthIf implements ValidationRule
{
    private MinLength $minLengthRule;

    /**
     * Create a new rule instance.
     *
     * @param string $dependentValue
     * @param string $expectedValue
     * @param int $min
     */
    public function __construct(
        private $dependentValue,
        private $expectedValue,
        private $min,
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
        $this->minLengthRule = new MinLength($this->min);

        // Check if the dependent value is equal to the expected value
        if ($this->dependentValue == $this->expectedValue) {
            $this->minLengthRule->validate($attribute, $value, $fail);
        }
    }
}
