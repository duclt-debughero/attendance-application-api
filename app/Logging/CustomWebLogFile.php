<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;

class CustomWebLogFile
{
    public const FORMAT = "[%datetime%] %level_name% %extra.ip% %extra.session_id% %extra.user_id%: %message% %context%\n";

    public function __invoke($logger) {
        $webProcessor = new WebProcessor();
        $customProcessor = new CustomProcessor();
        $lineFormatter = new LineFormatter(static::FORMAT, 'Y-m-d H:i:s', true, true);

        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor($webProcessor);
            $handler->pushProcessor($customProcessor);
            $handler->setFormatter($lineFormatter);
        }
    }
}
