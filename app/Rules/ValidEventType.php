<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use App\Repositories\EventTypeRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEventType implements ValidationRule
{
    private EventTypeRepository $eventTypeRepository;
    private string|int $eventTypeId;

    public function __construct(EventTypeRepository $eventTypeRepository, $eventTypeId) {
        $this->eventTypeRepository = $eventTypeRepository;
        $this->eventTypeId = $eventTypeId;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! $this->eventTypeRepository->findById($this->eventTypeId)) {
            $fail(ConfigUtil::getMessage('ECL050', [':attribute']));
        }
    }
}
