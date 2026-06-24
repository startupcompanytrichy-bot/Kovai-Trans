<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting(string $key, $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('setSetting')) {
    function setSetting(string $key, $value): void
    {
        Setting::setValue($key, $value);
    }
}
