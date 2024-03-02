<?php

namespace Macocci7\PhpHistogram\Helpers;

use Macocci7\PhpHistogram\Traits\JudgeTrait;
use Nette\Neon\Neon;

/**
 * Config operator.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Config
{
    use JudgeTrait;

    /**
     * @var mixed[] $conf
     */
    private static mixed $conf = [];

    /**
     * loads config from a file
     * @return  void
     */
    public static function load()
    {
        $class = self::class();
        $cl = self::className($class);
        $path = __DIR__ . '/../../conf/' . $cl . '.neon';
        self::$conf[$class] = Neon::decodeFile($path);
    }

    /**
     * returns the fully qualified class name of the caller
     * @return  string
     */
    public static function class()
    {
        return debug_backtrace()[2]['class'];
    }

    /**
     * returns just the class name splitted parent namespace
     * @param   string  $class
     * @return  string
     */
    public static function className(string $class)
    {
        $pos = strrpos($class, '\\');
        if ($pos) {
            return substr($class, $pos + 1);
        }
        return $class;
    }

    /**
     * returns config data
     * @param   string  $key = null
     * @return  mixed
     */
    public static function get(?string $key = null)
    {
        // get fully qualified class name of the caller
        $class = self::class();
        if (!self::$conf[$class]) {
            return null;
        }
        if (is_null($key)) {
            return self::$conf[$class];
        }
        $keys = explode('.', $key);
        $conf = self::$conf[$class];
        foreach ($keys as $k) {
            if (!isset($conf[$k])) {
                return null;
            }
            $conf = $conf[$k];
        }
        return $conf;
    }

    /**
     * judges if $input is valid or not
     * @param   mixed   $input
     * @param   string  $def
     * @return  bool
     */
    public static function isValid(mixed $input, string $def)
    {
        if (strcmp('int', $def) === 0) {
            return is_int($input);
        } elseif (strcmp('float', $def) === 0) {
            return is_float($input);
        } elseif (strcmp('string', $def) === 0) {
            return is_string($input);
        } elseif (strcmp('bool', $def) === 0) {
            return is_bool($input);
        } elseif (strcmp('array', $def) === 0) {
            return is_array($input);
        } elseif (strcmp('number', $def) === 0) {
            return self::isNumber($input);
        } elseif (strcmp('colorCode', $def) === 0) {
            return self::isColorCode($input);
        }
        return false;
    }
}
