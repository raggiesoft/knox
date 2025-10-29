<?php
/**
 * Checks all the dependencies for WP Dark Mode Ultimate
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );


if ( ! class_exists( __NAMESPACE__ . 'Dependency' ) ) {

	/**
	 * Checks all the dependencies for WP Dark Mode Ultimate
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Dependency extends \WP_Dark_Mode\Ultimate\Base {

		/**
		 * Minimum PHP version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_php_version = '7.0';

		/**
		 * Minimum WordPress version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_wp_version = '5.0';

		/**
		 * WP Dark Mode Free plugin file
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $wp_dark_mode_free_file = 'wp-dark-mode/plugin.php';

		/**
		 * Minimum WP Dark Mode Free version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_wp_dark_mode_free_version = '5.0.0';


		/**
		 * Checks if PHP version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_php_compatible() {
			// Check if PHP version is compatible.
			return ! version_compare( PHP_VERSION, $this->minimum_php_version, '<' );
		}

		/**
		 * Checks if WordPress version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_compatible() {
			// Check if WordPress version is compatible.
			return ! version_compare( get_bloginfo( 'version' ), $this->minimum_wp_version, '<' );
		}

		/**
		 * Checks if WP Dark Mode Free plugin is installed
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_installed() {
			// Check if WP Dark Mode Free plugin is installed.
			return file_exists( WP_PLUGIN_DIR . '/' . $this->wp_dark_mode_free_file );
		}

		/**
		 * Checks if WP Dark Mode Free plugin is active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_active() {
			// $active_plugins = get_option( 'active_plugins', array() );
			// return in_array( $this->wp_dark_mode_free_file, $active_plugins, true );

			// Load is_plugin_active function.
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Check if WP Dark Mode Free plugin is active.
			return is_plugin_active( $this->wp_dark_mode_free_file );
		}

		/**
		 * Checks if WP Dark Mode Free plugin version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_compatible() {

			$wp_dark_mode_version  = get_option( 'wp_dark_mode_version' );

			if ( ! $wp_dark_mode_version ) {
				return false;
			}

			return version_compare(
				$wp_dark_mode_version,
				$this->minimum_wp_dark_mode_free_version,
				'>='
			);
		}

		/**
		 * Checks if all the dependencies are met
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_compatible() {
			return $this->is_php_compatible() &&
					$this->is_wp_compatible() &&
					$this->is_wp_dark_mode_installed() &&
					$this->is_wp_dark_mode_active() &&
					$this->is_wp_dark_mode_compatible();
		}
	}

}
