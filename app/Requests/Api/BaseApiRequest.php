<?php

namespace App\Requests\Api;

use App\Enums\ApiCodeNo;
use App\Libs\{ApiBusUtil, ConfigUtil};
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Validation error messages.
     *
     * @return array
     */
    public function messages() {
        return [
            'required' => ConfigUtil::getMessage('ECL001', [':attribute']),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator) {
        $isRequiredFailed = false;

        // Check required
        $failedRules = $validator->failed();
        foreach ($failedRules as $rules) {
            if (array_key_exists('Required', $rules)) {
                $isRequiredFailed = true;
                break;
            }
        }

        $errorResponse = $isRequiredFailed
            ? ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::REQUIRED_PARAMETER)
            : ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::VALIDATE_PARAMETER);

        throw new HttpResponseException($errorResponse);
    }
}
