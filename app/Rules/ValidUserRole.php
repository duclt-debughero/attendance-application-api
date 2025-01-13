<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\UserRoleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUserRole implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param UserRoleRepository $userRoleRepository
     * @param string|int|null $userRoleId
     */
    public function __construct(
        private UserRoleRepository $userRoleRepository,
        private $userRoleId = null,
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
        if (isset($this->userRoleId) && ! $this->userRoleRepository->findById($this->userRoleId)) {
            $fail(ConfigUtil::getMessage('ECL050', [':attribute']));
        }
    }
}
