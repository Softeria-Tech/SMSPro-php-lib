#!/usr/bin/php -q
<?php

// Ensure the script is run with PHP 8.1 or higher
if (version_compare(PHP_VERSION, '8.1', '<')) {
    exit('The SMSPRO SMS Library requires PHP version 8.1 or higher.' . PHP_EOL);
}

// Include the necessary autoload file
$autoloadPaths = [
    dirname(__DIR__) . '/vendor/autoload.php',         // Downloaded installation
    dirname(__DIR__, 3) . '/autoload.php',              // Composer installation
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

if (!class_exists(Smspro\Sms\Console\Runner::class)) {
    exit('Unable to load Smspro SMS Library.' . PHP_EOL);
}

// Create and run the Runner instance
try {
    $runner = new Smspro\Sms\Console\Runner();
    $runner->run($argv);
} catch (Exception $exception) {
    exit('Error: ' . $exception->getMessage() . PHP_EOL);
}

exit(0);
