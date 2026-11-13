<?php

function app_env($key, $default = '')
{
    $value = getenv($key);

    if ($value === false || $value === null || $value === '') {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);
    }

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return trim((string) $value);
}

$appConfig = array(
    'app_name' => app_env('APP_NAME', 'Hospital Management System'),
    'app_env' => strtolower(app_env('APP_ENV', 'development')),
    'base_url' => app_env('APP_URL', 'http://localhost'),
    'db_host' => app_env('DB_HOST', 'localhost'),
    'db_name' => app_env('DB_NAME', 'ohmsphp'),
    'db_user' => app_env('DB_USER', 'root'),
    'db_pass' => app_env('DB_PASS', ''),
);

function is_production_environment()
{
    global $appConfig;
    return isset($appConfig['app_env']) && $appConfig['app_env'] === 'production';
}
