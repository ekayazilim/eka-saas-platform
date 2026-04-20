<?php

namespace Core;

class EkaTenant
{
    public static function getCurrent()
    {
        return $_SESSION['tenant'] ?? null;
    }

    public static function id()
    {
        return $_SESSION['tenant']['id'] ?? null;
    }

    public static function set(array $tenant): void
    {
        $_SESSION['tenant'] = $tenant;
    }

    public static function clear(): void
    {
        unset($_SESSION['tenant']);
    }
}
