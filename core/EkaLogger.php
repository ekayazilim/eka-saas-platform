<?php

namespace Core;

class EkaLogger
{
    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    private static function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : json_encode($context, JSON_UNESCAPED_UNICODE);
        $logMessage = "[{$date}] {$level}: {$message} {$contextStr}" . PHP_EOL;
        
        $logFile = STORAGE_PATH . '/logs/app.log';
        if (!file_exists(STORAGE_PATH . '/logs')) {
            mkdir(STORAGE_PATH . '/logs', 0777, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
