<?php

namespace Macocci7\PhpHistogram\Traits;

trait JudgeTrait
{
    /**
     * judges if the param is number
     * @param   mixed   $item
     * @return  bool
     */
    public static function isNumber(mixed $item): bool
    {
        return is_int($item) || is_float($item);
    }

    /**
     * judges if the param is in '#rrggbb' format or not
     * @param   mixed  $item
     * @return  bool
     */
    public static function isColorCode(mixed $item): bool
    {
        if (!is_string($item)) {
            return false;
        }
        return preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $item) ? true : false;
    }
}
