<?php

namespace App\Requests\Api\EventType;

use App\Libs\{
    ConfigUtil,
    ValueUtil,
};
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    FileEncoding,
    FileExtension,
};

class ImportCsvRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $maxFileSize = 1024 * ValueUtil::get('common.max_upload_file_size');
        return [
            'import_csv_file' => [
                'required',
                "max:{$maxFileSize}",
                new FileExtension('CSV', ['csv']),
                new FileEncoding('UTF-8', 'UTF-8'),
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
            'import_csv_file' => 'import_csv_file',
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
            'import_csv_file.max' => ConfigUtil::getMessage('ECL020', [
                $this->attributes()['import_csv_file'],
                "10MB",
            ]),
        ]);
    }
}
