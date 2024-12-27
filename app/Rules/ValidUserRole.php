<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\UserRoleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUserRole implements ValidationRule
{
    private UserRoleRepository $userRoleRepository;
    private string|int $userRoleId;

    public function __construct(UserRoleRepository $userRoleRepository, $userRoleId) {
        $this->userRoleRepository = $userRoleRepository;
        $this->userRoleId = $userRoleId;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! $this->userRoleRepository->findById($this->userRoleId)) {
            $fail(ConfigUtil::getMessage('ECL050', [':attribute']));
        }
    }
}
