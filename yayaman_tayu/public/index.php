<?php


use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * DEFAULT TIMEZONE (safe fallback)
 *---------------------------------------------------------------
 */
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
$pathsFile = FCPATH . '../app/Config/Paths.php';
if (! is_file($pathsFile)) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo 'Application paths file not found: ' . $pathsFile . PHP_EOL;
    echo 'Please ensure your "app" folder is located at: ' . realpath(FCPATH . '../app') . PHP_EOL;
    exit(1);
}
require $pathsFile;

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
if (! isset($paths->systemDirectory) || ! is_file($paths->systemDirectory . '/Boot.php')) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo 'CodeIgniter system bootstrap not found. Check Config/Paths.php systemDirectory setting.' . PHP_EOL;
    exit(1);
}

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));