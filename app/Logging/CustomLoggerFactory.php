<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

class CustomLoggerFactory
{
    public const FORMAT = "[%datetime%] %level_name% %extra.ip% %extra.session_id% %extra.user_id%: %message% %context%\n";

    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config) {
        $logger = new Logger('custom');

        $minLevel = $config['min_level'] ?? null;
        $maxLevel = $config['max_level'] ?? null;

        // Calculate Monolog level constants from string level names.
        $minLevelCode = $minLevel ? Logger::toMonologLevel($minLevel) : null;
        $maxLevelCode = $maxLevel ? Logger::toMonologLevel($maxLevel) : null;

        // Determine the log level from the environment
        $envLogLevel = strtolower($config['level'] ?? 'debug');
        $logLevelCode = Logger::toMonologLevel($envLogLevel);

        $handler = new StreamHandler($config['with']['stream'], $logLevelCode);
        $webProcessor = new WebProcessor();
        $customProcessor = new CustomProcessor();
        $handler->pushProcessor($webProcessor);
        $handler->pushProcessor($customProcessor);
        $lineFormatter = new LineFormatter(static::FORMAT, 'Y-m-d H:i:s', false, true);
        $handler->setFormatter($lineFormatter);

        // Apply minimum and maximum level restrictions
        if (isset($minLevelCode) && $minLevelCode->isHigherThan($logLevelCode)) {
            $handler->setLevel($minLevelCode);
        }

        if (isset($maxLevelCode) && $maxLevelCode->isLowerThan($logLevelCode)) {
            $handler->pushProcessor(function ($record) use ($maxLevelCode) {
                $recordLevel = Logger::toMonologLevel($record['level']);
                if ($recordLevel->isHigherThan($maxLevelCode)) {
                    return false; // Filter out the log entry
                }

                return $record;
            });
        }

        $logger->pushHandler($handler);

        return $logger;
    }
}
