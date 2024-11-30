<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MailRfc implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (
            ! (
                preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $value)
                && preg_match('/^[a-zA-Z0-9~`!@#$%^&*()-_=+<>?,.\/:;"\'{}]*$/', $value)
            )
        ) {
            $fail(ConfigUtil::getMessage('ECL007'));
        }
    }
}
