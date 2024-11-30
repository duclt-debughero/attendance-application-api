<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\MstUserRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueUser implements ValidationRule
{
    private MstUserRepository $mstUserRepository;
    private ?string $emailAddress;

    public function __construct(MstUserRepository $mstUserRepository, ?string $emailAddress = null) {
        $this->mstUserRepository = $mstUserRepository;
        $this->emailAddress = $emailAddress;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! empty($this->emailAddress) && ! $this->mstUserRepository->isUniqueEmailAddress($this->emailAddress)) {
            $fail(ConfigUtil::getMessage('ICL043'));
        }
    }
}
