<?php

namespace App\Rules;

use App\Libs\{
    ConfigUtil,
    ValueUtil,
};
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValueList implements ValidationRule
{
    private string $key;

    /**
     * Create a new rule instance.
     *
     * @param string $key
     * @return void
     */
    public function __construct(string $key) {
        $this->key = $key;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! array_key_exists((int) $value, ValueUtil::getList($this->key))) {
            $keys = array_keys(ValueUtil::getList($this->key));
            $valueList = implode(', ', $keys);

            $fail(ConfigUtil::getMessage('ECL068', [':attribute', $valueList]));
        }
    }
}
