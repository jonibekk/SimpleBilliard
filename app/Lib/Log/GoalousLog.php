<?php
App::uses('AppUtil', 'Util');

class GoalousLog
{
    /**
     * skipping class name
     *
     * @var string[]
     */
    protected static $ignoreClasses = [
        self::class,
    ];

    /**
     * add skipping class name
     * @param string $className
     */
    public static function addIgnoreClass(string $className)
    {
        array_push(self::$ignoreClasses, $className);
    }

    /**
     * returns the called file name and file line
     *
     * @return array
     */
    private static function getFileAndLine(): array
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $function = null;
        $line = null;
        $callerTrace = null;
        foreach ($traces as $trace) {
            if (in_array($trace['class'], self::$ignoreClasses)) {
                $callerTrace = $trace;
                continue;
            }
            if (isset($callerTrace['file'])) {
                $function = basename($callerTrace['file']);
            }
            if (isset($callerTrace['line'])) {
                $line = intval($callerTrace['line']);
            }
            return [$function, $line];
        }
        return [null, null];
    }

    /**
     * build logging message format
     * @param       $message
     * @param array $values
     *
     * @return string
     */
    private static function buildMessageFormat($message, array $values = []): string
    {
        list($file, $line) = static::getFileAndLine();
        return sprintf('%s %s', $message, AppUtil::jsonOneLine(am(
            $values,
            [
                'extras' => [
                    'file' => $file,
                    'line' => $line,
                ]
            ]
        )));
    }

    public static function info($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::info(static::buildMessageFormat($message, $values), $scope);
    }

    public static function emergency($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::emergency(static::buildMessageFormat($message, $values), $scope);
    }

    public static function alert($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::alert(static::buildMessageFormat($message, $values), $scope);
    }

    public static function critical($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::critical(static::buildMessageFormat($message, $values), $scope);
    }

    public static function error($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::error(static::buildMessageFormat($message, $values), $scope);
    }

    public static function warning($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::warning(static::buildMessageFormat($message, $values), $scope);
    }

    public static function notice($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::notice(static::buildMessageFormat($message, $values), $scope);
    }

    public static function debug($message, array $values = [], array $scope = []): bool
    {
        return CakeLog::debug(static::buildMessageFormat($message, $values), $scope);
    }
}
