<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomApiLogFile
{
    public const FORMAT = "[%datetime%]: %message% %context%\n";

    public function __invoke($logger) {
        $lineFormatter = new LineFormatter(static::FORMAT, 'Y-m-d H:i:s', false, true);
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($lineFormatter);
        }
    }
}
