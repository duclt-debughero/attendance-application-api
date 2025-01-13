<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\MstUserRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueUser implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param MstUserRepository $mstUserRepository
     * @param string|null $emailAddress
     */
    public function __construct(
        private readonly MstUserRepository $mstUserRepository,
        private string|null $emailAddress = null,
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
        if (isset($this->emailAddress) && ! $this->mstUserRepository->isUniqueEmailAddress($this->emailAddress)) {
            $fail(ConfigUtil::getMessage('ICL043'));
        }
    }
}
