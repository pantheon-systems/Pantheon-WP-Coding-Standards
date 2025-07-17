<?php
// Set the PHPCS standard directory for the test framework.
putenv('PHP_CODESNIFFER_STANDARD_DIRS=' . dirname(__DIR__, 2));

// Now include the PHPCS test bootstrap.
require_once dirname(__DIR__, 2) . '/vendor/squizlabs/php_codesniffer/tests/bootstrap.php';
