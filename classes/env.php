<?php

class Env
{
    private static $loaded = false;
    private static $values = [];

    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $envPath = $path ?: dirname(__DIR__) . '/.env';
        if (!file_exists($envPath)) {
            self::$loaded = true;
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            self::$loaded = true;
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (
                strlen($value) >= 2 &&
                (($value[0] === '"' && substr($value, -1) === '"') ||
                ($value[0] === "'" && substr($value, -1) === "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$values[$key] = $value;
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv($key . '=' . $value);
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }
}

