<?php
/**
 * Plugin name: WP Dark Mode Ultimate
 * Plugin URI: https://wppool.dev/wp-dark-mode
 * Description: Unlocks all the premium features of WP Dark Mode, including the ability to create multiple dark mode switches, custom dark mode switchers, and more.
 * Version: 4.0.13
 * Author: WPPOOL
 * Author URI: https://wppool.dev
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-dark-mode-ultimate
 * Domain Path: /languages
 *
 * @package WP Dark Mode
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

// Bail if WP_DARK_MODE_ULTIMATE_VERSION defined.
if ( defined( 'WP_DARK_MODE_ULTIMATE_VERSION' ) ) {
	return;
}

// Check if WP_DARK_MODE_ULTIMATE_VERSION defined.
if ( ! defined( 'WP_DARK_MODE_ULTIMATE_VERSION' ) ) {
	define( 'WP_DARK_MODE_ULTIMATE_FILE', __FILE__ );
	define( 'WP_DARK_MODE_ULTIMATE_VERSION', '4.0.13' );

	/**
	 * Loads the boot file.
	 *
	 * @since 5.0.0
	 */
	require_once __DIR__ . '/includes/class-boot.php';
}

/**
 * Manipulating any codebase will NOT be supported.
 */
