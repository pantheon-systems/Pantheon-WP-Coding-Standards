<?php
/**
 * Verifies that multiline comments are not used.
 *
 * @package Pantheon-WP-Minimum
 */

namespace Pantheon_WP_Minimum\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Verifies that multiline comments are not used.
 */
class DisallowMultilineSlashCommentSniff implements Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [ T_COMMENT ];
	}

	/**
		* Processes this test, when one of its tokens is encountered.
		*
		* @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
		* @param int                         $stackPtr  The position of the current token in the
		*                                               stack passed in $tokens.
		*
		* @return void
		*/
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// We are only interested in single-line comments.
		if ( '//' !== substr( $tokens[ $stackPtr ]['content'], 0, 2 ) ) {
			return;
		}

		$nextComment = $phpcsFile->findNext( T_COMMENT, $stackPtr + 1 );
		if ( false === $nextComment ) {
			return;
		}

		// Check if the next comment is on the next line.
		if ( $tokens[ $nextComment ]['line'] === $tokens[ $stackPtr ]['line'] + 1 ) {
			// Check if the next comment is also a single-line comment.
			if ( '//' === substr( $tokens[ $nextComment ]['content'], 0, 2 ) ) {
				$error = 'Multiline comments should use /* ... */ syntax, not multiple // comments.';
				$phpcsFile->addWarning( $error, $stackPtr, 'Found' );
			}
		}
	}
}