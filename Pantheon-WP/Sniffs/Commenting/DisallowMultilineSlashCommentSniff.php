<?php
/**
 * Verifies that multiline comments are not used.
 *
 * @package Pantheon-WP
 */

namespace Pantheon_WP\Sniffs\Commenting;

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
		$token  = $tokens[ $stackPtr ];

		// Check for consecutive single-line comments.
		if ( '//' === substr( $token['content'], 0, 2 ) ) {
			$nextComment = $phpcsFile->findNext( T_COMMENT, $stackPtr + 1 );
			if ( false !== $nextComment && $tokens[ $nextComment ]['line'] === $token['line'] + 1 ) {
				if ( '//' === substr( $tokens[ $nextComment ]['content'], 0, 2 ) ) {
					$error = 'Multiline comments should use /* ... */ syntax, not multiple // comments.';
					$phpcsFile->addError( $error, $stackPtr, 'Found' );
				}
			}
			return;
		}

		// Check for long lines within block comments.
		if ( substr( $token['content'], 0, 2 ) === '/*' ) {
			// First, check the line of the starting token itself.
			if ( $tokens[$stackPtr]['length'] > 80 ) {
				$warning = 'For readability, it is recommended to break long comment lines into multiple lines.';
				$phpcsFile->addWarning( $warning, $stackPtr, 'LongLine' );
				return; // Only one warning per comment block.
			}

			// Then, iterate through subsequent tokens on following lines.
			for ( $i = ( $stackPtr + 1 ); $i < count( $tokens ); $i++ ) {
				// Stop if we are no longer in a comment.
				if ( $tokens[$i]['code'] !== T_COMMENT ) {
					break;
				}

				// Stop if the line doesn't start with a star, indicating the end of the block.
				if ( strpos( trim( $tokens[$i]['content'] ), '*' ) !== 0 ) {
					break;
				}

				if ( $tokens[$i]['length'] > 80 ) {
					$warning = 'For readability, it is recommended to break long comment lines into multiple lines.';
					$phpcsFile->addWarning( $warning, $i, 'LongLine' );
					return; // Only one warning per comment block.
				}
			}
		}
	}
}