<?php
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $con = mysqli_connect(
        $appConfig['db_host'],
        $appConfig['db_user'],
        $appConfig['db_pass'],
        $appConfig['db_name']
    );

    if (!$con) {
        throw new RuntimeException('Database connection failed.');
    }

    mysqli_set_charset($con, 'utf8mb4');
} catch (Exception $e) {
    error_log('Database connection error: ' . $e->getMessage());

    if (is_production_environment()) {
        http_response_code(500);
        die('Database connection error. Please contact the system administrator.');
    }

    die('Database connection error: ' . $e->getMessage());
}
