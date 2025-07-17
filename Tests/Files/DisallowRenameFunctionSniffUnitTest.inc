<?php
// This file is for testing the DisallowRenameFunctionSniff.

rename('foo.txt', 'bar.txt'); // error

$func = 'rename';
$func('foo.txt', 'bar.txt'); // should not error (dynamic)

// Should not error:
copy('foo.txt', 'bar.txt');
