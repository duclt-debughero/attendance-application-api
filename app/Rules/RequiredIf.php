<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiredIf implements ValidationRule
{

    /**
     * Create a new rule instance.
     *
     * @param string $dependentValue
     * @param string $expectedValue
     * @return void
     */
    public function __construct(
        private string $dependentValue,
        private string $expectedValue,
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
        if ($this->dependentValue == $this->expectedValue) {
            $requiredRule = new Required();
            $requiredRule->validate(':attribute', $value, $fail);
        }
    }
}
