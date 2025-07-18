<?php
/**
 * Disallow usage of the rename() function.
 *
 * @expectedError[Pantheon-WP-Minimum] Pantheon_WP_Minimum.Files.DisallowRenameFunction.RenameFunctionDisallowed
 * @expectedError[Pantheon-WP] Pantheon_WP_Minimum.Files.DisallowRenameFunction.RenameFunctionDisallowed
 *
 * @package Pantheon-WP-Coding-Standards
 */

rename( 'foo.txt', 'bar.txt' ); // error.

$func = 'rename';
$func( 'foo.txt', 'bar.txt' ); // should not error (dynamic).

// Should not error.
copy( 'foo.txt', 'bar.txt' );
