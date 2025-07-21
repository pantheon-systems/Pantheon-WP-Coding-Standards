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
		self::getConsecutiveSingleLineComments( $phpcsFile, $stackPtr );
	}

	/**
	 * Checks for consecutive single-line comments and reports them.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int  $stackPtr  The position of the current token in the stack.
	 * @param string $error    The type of error to report ('warning' or 'error').
	 *
	 * @return void
	 */
	public static function getConsecutiveSingleLineComments( File $phpcsFile, $stackPtr, $error = 'warning' ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		// We are only interested in single-line comments.
		if ( '//' !== substr( $token['content'], 0, 2 ) ) {
			return;
		}

		// If the comment is not on a line by itself, it's not part of a multi-line comment block.
		$prev = $phpcsFile->findPrevious( T_WHITESPACE, $stackPtr - 1, null, true );
		if ( false !== $prev && $tokens[ $prev ]['line'] === $token['line'] ) {
			return;
		}

		// Check the previous line to see if we are already in a block of non-inline comments.
		$prevLine = $token['line'] - 1;
		$prevCommentPtr = $phpcsFile->findPrevious( T_COMMENT, $stackPtr - 1, null, false, null, true );
		if ( false !== $prevCommentPtr && $tokens[ $prevCommentPtr ]['line'] === $prevLine ) {
			$prevCommentToken = $tokens[ $prevCommentPtr ];
			if ( '//' === substr( $prevCommentToken['content'], 0, 2 ) ) {
				// Was the previous comment also a non-inline comment?
				$prev = $phpcsFile->findPrevious( T_WHITESPACE, $prevCommentPtr - 1, null, true );
				if ( false === $prev || $tokens[ $prev ]['line'] !== $prevCommentToken['line'] ) {
					// Yes, it was. So we are in the middle of a block.
					return;
				}
			}
		}

		// This is the start of a block. Now, count how many lines it spans.
		$lineCount = 1;
		$nextComment = $stackPtr;
		while ( true ) {
			$nextComment = $phpcsFile->findNext( T_COMMENT, $nextComment + 1, null, false, null, true );
			if ( false === $nextComment || $tokens[ $nextComment ]['line'] !== ( $token['line'] + $lineCount ) ) {
				break; // The block has ended.
			}

			// If the next comment is not on a line by itself, it's an inline comment, so the block ends here.
			$prev = $phpcsFile->findPrevious( T_WHITESPACE, $nextComment - 1, null, true );
			if ( false !== $prev && $tokens[ $prev ]['line'] === $tokens[ $nextComment ]['line'] ) {
				break;
			}

			$lineCount++;
		}

		if ( $lineCount > 1 ) {
			$message = 'A block of %s consecutive single-line comments was found starting on this line. Please use a /* ... */ block instead.';
			$data    = [ $lineCount ];
			if ( 'warning' === $error ) {
				$phpcsFile->addWarning( $message, $stackPtr, 'Found', $data );
			} else {
				$phpcsFile->addError( $message, $stackPtr, 'Found', $data );
			}
		}
	}
}