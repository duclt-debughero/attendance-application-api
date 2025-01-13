<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinLengthIf implements ValidationRule
{
    private string $dependentValue;
    private string $expectedValue;
    private MinLength $minLengthRule;

    /**
     * Create a new rule instance.
     *
     * @param string $dependentValue
     * @param string $expectedValue
     * @param int $min
     */
    public function __construct(string $dependentValue, string $expectedValue, int $min)
    {
        $this->dependentValue = $dependentValue;
        $this->expectedValue = $expectedValue;
        $this->minLengthRule = new MinLength($min);
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->dependentValue == $this->expectedValue) {
            $this->minLengthRule->validate($attribute, $value, $fail);
        }
    }
}
