<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLengthIf implements ValidationRule
{
    private string $dependentValue;
    private string $expectedValue;
    private MaxLength $maxLengthRule;

    /**
     * Create a new rule instance.
     *
     * @param string $dependentValue
     * @param string $expectedValue
     * @param int $max 
     * @return void
     */
    public function __construct(string $dependentValue, string $expectedValue, int $max)
    {
        $this->dependentValue = $dependentValue;
        $this->expectedValue = $expectedValue;
        $this->maxLengthRule = new MaxLength($max);
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
            $this->maxLengthRule->validate($attribute, $value, $fail);
        }
    }
}