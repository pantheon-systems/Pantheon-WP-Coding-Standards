<?php
/**
 * Disallow usage of the rename() function.
 *
 * @package PantheonWP\Sniffs\Files
 */

namespace Pantheon_WP_Minimum\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowRenameFunctionSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_STRING];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        // Check for function call to rename()
        if (strtolower($token['content']) === 'rename') {
            $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if ($next !== false && $tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
                $phpcsFile->addError(
                    'The rename() function is disallowed on Pantheon due to file system restrictions.',
                    $stackPtr,
                    'RenameFunctionDisallowed'
                );
            }
        }
    }
}
