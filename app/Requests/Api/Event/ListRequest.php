<?php

namespace App\Requests\Api\Event;

use App\Libs\ConfigUtil;
use App\Requests\Api\BaseApiRequest;
use App\Rules\DateTimeComparison;
use App\Rules\FormatDate;
use Illuminate\Http\Request;

class ListRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request) {
        return [
            'event_start_time_from' => [
                'nullable',
                new FormatDate('Y/m/d'),
            ],
            'event_start_time_to' => [
                'nullable',
                new FormatDate('Y/m/d'),
                new DateTimeComparison(
                    'event_start_time_from',
                    'event_start_time_to',
                    $request->event_start_time_from,
                    $request->event_start_time_to,
                    'Y/m/d',
                    'lte',
                ),
            ],
            'event_end_time_from' => [
                'nullable',
                new FormatDate('Y/m/d'),
            ],
            'event_end_time_to' => [
                'nullable',
                new FormatDate('Y/m/d'),
                new DateTimeComparison(
                    'event_end_time_from',
                    'event_end_time_to',
                    $request->event_end_time_from,
                    $request->event_end_time_to,
                    'Y/m/d',
                    'lte',
                ),
            ],
        ];
    }

    /**
     * Retrieves the attributes of the user model.
     *
     * @return array An associative array with the keys:
     */
    public function attributes() {
        return [
            'event_start_time_from' => 'event_start_time_from',
            'event_start_time_to' => 'event_start_time_to',
            'event_end_time_from' => 'event_end_time_from',
            'event_end_time_to' => 'event_end_time_to',
        ];
    }

    /**
     * Validation error messages.
     *
     * @return array
     */
    public function messages() {
        $parentMessage = parent::messages();

        return array_merge($parentMessage, [
            'event_start_time_to.after_or_equal' => ConfigUtil::getMessage('ECL026', ['event_start_time_to', 'event_start_time_from']),
            'event_end_time_to.after_or_equal' => ConfigUtil::getMessage('ECL026', ['event_end_time_to', 'event_end_time_from']),
        ]);
    }
}
