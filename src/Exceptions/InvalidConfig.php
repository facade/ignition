<?php

namespace Facade\Ignition\Exceptions;

use Exception;
use Monolog\Logger;

class InvalidConfig extends Exception
{
    public static function  invalidLogLevel(string $logLevel)
    {
        $validLogLevels = array_map(function(string $level) {
            return strtolower($level);
        }, array_keys(Logger::getLevels()));

        $validLogLevelsString = implode(',', $validLogLevels);

        return "You specify an invalid log level `{$logLevel}`. Valid log levels are {$validLogLevelsString}.";
    }

}

