<?php

namespace Core\Helpers;

class Sanitizer
{

    /**
     * @param $value
     * @return string
     */
    public static function sanitize($value): string
    {
        return strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
    }
}
