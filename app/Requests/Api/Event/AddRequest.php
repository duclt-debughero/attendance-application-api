<?php

namespace App\Requests\Api\Event;

use App\Libs\ConfigUtil;
use App\Repositories\EventTypeRepository;
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    DateTimeComparison,
    MaxLength,
    Numeric,
    ValidEventType,
};
use App\Rules\FormatDate;
use Illuminate\Http\Request;

class AddRequest extends BaseApiRequest
{
    public function __construct(
        private EventTypeRepository $eventTypeRepository,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request) {
        $rules = [
            'event_name' => [
                'required',
                new MaxLength(255),
            ],
            'event_start_time' => [
                'required',
                new FormatDate('Y/m/d H:i'),
            ],
            'event_end_time' => [
                'required',
                new FormatDate('Y/m/d H:i'),
                new DateTimeComparison('event_start_time', 'event_end_time', $request->event_start_time, $request->event_end_time, 'Y/m/d H:i', '<'),
            ],
            'location' => [
                'nullable',
                new MaxLength(255),
            ],
            'event_type_id' => [
                'nullable',
                new Numeric(),
                new ValidEventType($this->eventTypeRepository, $request->event_type_id),
            ],
        ];

        return $rules;
    }

    /**
     * Retrieves the attributes of the user model.
     *
     * @return array An associative array with the keys:
     */
    public function attributes() {
        return [
            'event_name' => 'event_name',
            'event_start_time' => 'event_start_time',
            'event_end_time' => 'event_end_time',
            'location' => 'location',
            'event_type_id' => 'event_type_id',
        ];
    }
}
