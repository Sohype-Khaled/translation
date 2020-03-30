<?php

namespace Codtail\Translation\Exceptions;

use Exception;

class InvalidLocaleException extends Exception
{
    public static function error($locale)
    {
        return new static("Locale $locale dose not exist in locales array in config/translation.php");
    }
}

