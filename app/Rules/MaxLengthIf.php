<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLengthIf implements ValidationRule
{
    private MaxLength $maxLengthRule;

    /**
     * Create a new rule instance.
     *
     * @param string $dependentValue
     * @param string $expectedValue
     * @param int $max
     */
    public function __construct(
        private $dependentValue,
        private $expectedValue,
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
        $this->maxLengthRule = new MaxLength($this->max);

        // Check if the dependent value is equal to the expected value
        if ($this->dependentValue == $this->expectedValue) {
            $this->maxLengthRule->validate($attribute, $value, $fail);
        }
    }
}
