<?php

namespace Codtail\Translation\Exceptions;

use Exception;

class PropertyNotTranslatableException extends Exception
{
    public static function error($property, $model)
    {
        $translatable = implode(', ', $model->getTranslatableProperties());
        return new static("Property \"$property\" is not translatable, translatable properties are: $translatable");
    }
}
