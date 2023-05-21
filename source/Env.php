<?php

namespace Chat;

class Env
{
    public const PRODUCTION = 'production';

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key) ?: $_ENV[$key] ?? $default;

        if ($value === false || $value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }

        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
