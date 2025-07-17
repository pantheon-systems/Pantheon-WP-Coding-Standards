<?php
// Set the PHPCS standard directory for the test framework.
putenv('PHP_CODESNIFFER_STANDARD_DIRS=' . __DIR__);

// Now include the PHPCS test bootstrap.
require_once __DIR__ . '/vendor/squizlabs/php_codesniffer/tests/bootstrap.php';
