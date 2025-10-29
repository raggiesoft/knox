<?php
/**
 * Handles all the installation related tasks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Install' ) ) {
	/**
	 * Handles all the installation related tasks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Install extends \WP_Dark_Mode\Ultimate\Base {

		/**
		 * Get dependencies
		 *
		 * @since 5.0.0
		 * @return object
		 */
		public function get_dependency() {
			return Dependency::get_instance();
		}

		/**
		 * Register actions
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function actions() {
			// Register activation hook.
			register_activation_hook( WP_DARK_MODE_ULTIMATE_FILE, array( $this, 'activate' ) );

			// Print dependencies error.
			add_action( 'admin_notices', array( $this, 'print_dependency_errors' ) );
		}

		/**
		 * Register filters
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function filters() {

			// Plugin action links.
			// add_filter( 'plugin_action_links_' . plugin_basename( WP_DARK_MODE_ULTIMATE_FILE ), array( $this, 'plugin_action_links' ) );
		}

		/**
		 * Runs on plugin activation
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function activate() {
			$this->check_compatibilities();
		}

		/**
		 * Runs on plugin deactivation
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function deactivate() {}

		/**
		 * Checks requirements for plugin activation
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function check_compatibilities() {

			$dependency = $this->get_dependency();

			// Checks PHP Compatibility.
			if ( ! $dependency->is_php_compatible() ) {

				// Throw an error in the WordPress admin console.
				$this->print_error(
					sprintf(
						__( '<strong>WP Dark Mode Ultimate %s</strong> requires PHP version %s or greater. Your current PHP version is %s.', 'wp-dark-mode' ),
						WP_DARK_MODE_ULTIMATE_VERSION,
						$dependency->minimum_php_version,
						PHP_VERSION
					)
				);

				// Deactivate the plugin.
				deactivate_plugins( WP_DARK_MODE_ULTIMATE_FILE );

				return false;
			}

			// Checks WordPress Compatibility.
			if ( ! $dependency->is_wp_compatible() ) {

				// Throw an error in the WordPress admin console.
				$this->print_error(
					sprintf(
						/* translators: %s: WordPress version */
						__( '<strong>WP Dark Mode Ultimate %s</strong> requires WordPress version %s or greater. Your current WordPress version is %s.', 'wp-dark-mode' ),
						WP_DARK_MODE_ULTIMATE_VERSION,
						$dependency->minimum_wp_version,
						get_bloginfo( 'version' )
					)
				);

				// Deactivate the plugin.
				deactivate_plugins( WP_DARK_MODE_ULTIMATE_FILE );

				return false;
			}

			return true;
		}

		/**
		 * Prints a notice if dependencies are not met
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function print_dependency_errors() {

			$dependency = $this->get_dependency();


			// Checks wp dark mode free plugin file exists or not.
			if ( ! $dependency->is_wp_dark_mode_installed() ) {

				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						/* translators: %s: WordPress version */
						__( '<strong>WP Dark Mode Ultimate</strong> requires <strong>WP Dark Mode</strong> to be installed and activated.', 'wp-dark-mode' ),
						''
					)
				);

				return;
			}

			// Checks wp dark mode free plugin is active or not.
			if ( ! $dependency->is_wp_dark_mode_active() ) {
				
				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						/* translators: %s: WordPress version */
						__( '<strong>WP Dark Mode Ultimate</strong> requires <strong>WP Dark Mode</strong> to be activated.', 'wp-dark-mode' ),
						''
					)
				);

				return;
			}

			// Checks wp dark mode free plugin version is compatible or not.
			if ( ! $dependency->is_wp_dark_mode_compatible() ) {
				
				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						/* translators: %s: WordPress version */
						__( '<strong>WP Dark Mode Ultimate %s</strong> requires <strong>WP Dark Mode</strong> version <strong>%s or greater</strong>.', 'wp-dark-mode' ),
						WP_DARK_MODE_ULTIMATE_VERSION,
						$dependency->minimum_wp_dark_mode_free_version
					)
				);

				return;
			}
		}

		/**
		 * Prints an error notice
		 *
		 * @since 5.0.0
		 * @param string $message Error message.
		 * @return void
		 */
		public function print_error( $message ) {
			// Print notice.
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
		}

		/**
		 * Adds plugin action links
		 *
		 * @since 5.0.0
		 * @param array $links Plugin action links.
		 * @return array
		 */
		public function plugin_action_links( $links ) {

			$is_ultimate = apply_filters( 'wp_dark_mode_is_ultimate', false );

			// Add settings link to first.
			array_unshift(
				$links,
				sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'admin.php?page=wp-dark-mode-ultimate-license' ),
					wp_sprintf( '%s License', $is_ultimate ? 'Manage' : 'Activate' )
				)
			);

			return $links;
		}
	}

	// Instantiate the class.
	Install::init();
}