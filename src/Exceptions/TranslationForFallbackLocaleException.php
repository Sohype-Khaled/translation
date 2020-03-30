<?php

namespace Codtail\Translation\Exceptions;

use Exception;
use Illuminate\Support\Facades\Config;

class TranslationForFallbackLocaleException extends Exception
{
    public static function error()
    {
        $fallback = Config::get('translation.fallback_locale');
        return new static("Cannot set translation for fallback local: $fallback");
    }
}
