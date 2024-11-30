<?php

namespace App\Extensions;

use Carbon\Carbon;

class CarbonExtension extends Carbon
{
    /**
     * Default format for DateTime
     *
     * Ex: formatDefaultDateTime() => 2024/06/01 12:00
     *
     * @return string The formatted date.
     */
    public function formatDefaultDateTime() {
        return $this->format('Y/m/d H:i:s');
    }

    /**
     * Default format for Date
     *
     * Ex: formatDefaultDate() => 2024/06/01
     *
     * @return string The formatted date.
     */
    public function formatDefaultDate() {
        return $this->format('Y/m/d');
    }
}
