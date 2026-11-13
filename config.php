<?php
/**
 * Minimal .env reader for this legacy project (no external dependency).
 * Real environment variables take precedence over values in the .env file.
 */
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === '' || getenv($key) !== false) {
            continue;
        }
        if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

function app_env($key, $default = '')
{
    $value = getenv($key);
    if ($value === false || $value === null || $value === '') {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);
    }
    if ($value === false || $value === null) {
        return $default;
    }
    return trim((string) $value);
}

$appConfig = array(
    'app_name' => app_env('APP_NAME', 'Hospital Management System'),
    'app_env' => strtolower(app_env('APP_ENV', 'development')),
    'base_url' => app_env('APP_URL', 'http://localhost:8000'),
    'db_host' => app_env('DB_HOST', 'localhost'),
    'db_name' => app_env('DB_NAME', 'ohmsphp'),
    'db_user' => app_env('DB_USER', ''),
    'db_pass' => app_env('DB_PASS', ''),
);

function is_production_environment()
{
    global $appConfig;
    return isset($appConfig['app_env']) && $appConfig['app_env'] === 'production';
}
