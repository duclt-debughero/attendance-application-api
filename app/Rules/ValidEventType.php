<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\EventTypeRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEventType implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param EventTypeRepository $eventTypeRepository
     * @param string|int|null $eventTypeId
     */
    public function __construct(
        private readonly EventTypeRepository $eventTypeRepository,
        private string|int|null $eventTypeId = null,
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
        if (isset($this->eventTypeId) && ! $this->eventTypeRepository->findById($this->eventTypeId)) {
            $fail(ConfigUtil::getMessage('ECL050', [':attribute']));
        }
    }
}
