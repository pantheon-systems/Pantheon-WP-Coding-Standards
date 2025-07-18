<?php
/**
 * Disallow usage of multiline slash comments.
 *
 * @expectedError Pantheon_WP_Minimum.Commenting.DisallowMultilineSlashComment.Found
 * @expectedWarning Pantheon_WP_Minimum.Commenting.DisallowMultilineSlashComment.LongLine
 *
 * @package Pantheon-WP-Coding-Standards
 */

// This comment should fail with a warning.
// Multiline comments should use /* ... */ syntax, not multiple // comments.
// What's with the life preserver? Doc, she's beautiful. She's crazy about me. Look at this,
// look what she wrote me, Doc. That says it all. Doc, you're my only hope. Oh, uh, hey you,
// get your damn hands off her. Do you really think I oughta swear? Well, now we gotta sneak
// this back into my laboratory, we've gotta get you home. Jennifer.

/*
 * This comment should not fail but should trigger an info to break the comment up. What's with the life preserver? Doc, she's beautiful. She's crazy about me. Look at this, look what she wrote me, Doc. That says it all. Doc, you're my only hope. Oh, uh, hey you, get your damn hands off her. Do you really think I oughta swear? Well, now we gotta sneak this back into my laboratory, we've gotta get you home. Jennifer.
 */

/*
 * This comment is ideal. It uses the correct syntax and is not too long.
 * What's with the life preserver? Doc, she's beautiful. She's crazy about me.
 * Look at this, look what she wrote me, Doc. That says it all. Doc, you're my 
 * only hope. Oh, uh, hey you, get your damn hands off her. Do you really think 
 * I oughta swear? Well, now we gotta sneak this back into my laboratory, we've 
 * gotta get you home. Jennifer.
 */
