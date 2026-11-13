<?php
/** Local demo preflight. Run with: php tools/preflight.php */
$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "FAIL: config.php was not found.\n");
    exit(1);
}
require $configPath;

$failed = false;
function report_check($label, $ok, $detail)
{
    global $failed;
    echo ($ok ? 'PASS' : 'FAIL') . ': ' . $label . ' — ' . $detail . PHP_EOL;
    if (!$ok) {
        $failed = true;
    }
}

report_check('PHP runtime', PHP_VERSION, 'PHP ' . PHP_VERSION . ' detected; legacy compatibility still needs manual testing.');
report_check('mysqli extension', extension_loaded('mysqli'), extension_loaded('mysqli') ? 'available' : 'install/enable mysqli');
report_check('local environment', !is_production_environment(), is_production_environment() ? 'APP_ENV is production; use this script only for local demo setup' : 'APP_ENV is not production');
report_check('database configuration', $appConfig['db_host'] !== '' && $appConfig['db_name'] !== '' && $appConfig['db_user'] !== '', 'host/database/user must be configured in .env');
report_check('demo schema file', is_file(dirname(__DIR__) . '/DATABASE FILE/ohmsphp.sql'), 'main SQL schema availability');
report_check('AI demo schema file', is_file(dirname(__DIR__) . '/DATABASE FILE/ai_features_schema.sql'), 'AI SQL schema availability');

if (extension_loaded('mysqli') && $appConfig['db_host'] !== '' && $appConfig['db_name'] !== '' && $appConfig['db_user'] !== '') {
    mysqli_report(MYSQLI_REPORT_OFF);
    $connection = @mysqli_connect($appConfig['db_host'], $appConfig['db_user'], $appConfig['db_pass'], $appConfig['db_name']);
    report_check('database connection', $connection !== false, $connection ? 'connected' : 'could not connect; verify local MySQL, database, and .env settings');
    if ($connection) {
        mysqli_set_charset($connection, 'utf8mb4');
        $result = @mysqli_query($connection, 'SHOW TABLES');
        report_check('database tables', $result !== false && mysqli_num_rows($result) > 0, 'import both SQL schema files into the demo database');
        mysqli_close($connection);
    }
}

if ($failed) {
    echo PHP_EOL . 'Preflight found issues. Resolve them before starting the local demo.' . PHP_EOL;
    exit(1);
}

echo PHP_EOL . 'Basic checks passed. This does not test the app pages or certify security/readiness.' . PHP_EOL;
