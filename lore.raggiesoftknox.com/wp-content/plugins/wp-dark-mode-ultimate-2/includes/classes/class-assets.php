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

if ( ! class_exists( __NAMESPACE__ . 'Assets' ) ) {
	/**
	 * Handles all the installation related tasks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Assets extends \WP_Dark_Mode\Ultimate\Base {

		/**
		 * Register actions
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function actions() {
			// Enqueue scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Modify script tag.
			add_filter( 'script_loader_tag', array( $this, 'modify_script_tag' ), 10, 3 );

			add_filter('wp_dark_mode_json', array($this, 'add_dark_mode_settings'));


			// Options on Ajax for Cache-free integration.
			add_action( 'wp_ajax_wp_dark_mode_options', array( $this, 'ajax_options' ) );
			add_action( 'wp_ajax_nopriv_wp_dark_mode_options', array( $this, 'ajax_options' ) );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wp-dark-mode-ultimate', WP_DARK_MODE_ULTIMATE_ASSETS . 'js/wp-dark-mode-ultimate.min.js', array( 'jquery' ), WP_DARK_MODE_ULTIMATE_VERSION, true );
		}

		/**
		 * Modify script tag
		 *
		 * @since 5.0.0
		 * @param string $tag    Script tag.
		 * @param string $handle Script handle.
		 * @param string $src    Script source.
		 * @return string
		 */
		public function modify_script_tag( $tag, $handle, $src ) {
			// Add defer attribute if the script handle is wp-dark-mode-ultimate.
			if ( 'wp-dark-mode-ultimate' === $handle ) {
				$tag = str_replace( ' src', ' defer src', $tag );
			}

			return $tag;
		}

		/**
		 * Add dark mode settings
		 *
		 * @since 5.0.0
		 * @param array $settings Dark mode settings.
		 * @return array
		 */
		public function add_dark_mode_settings( $settings ) {
			$settings['is_ultimate'] = $this->is_ultimate();

			return $settings;
		}



		/**
		 * Get options via AJAX
		 *
		 * @since 5.0.0
		 */
		public function ajax_options() {
			// Check nonce.
			check_ajax_referer( 'wp_dark_mode_nonce', 'nonce' );

			$options = [
				'performance_exclude_cache' => get_option( 'wp_dark_mode_performance_exclude_cache' ),
			];

			wp_send_json_success( $options );
		}

	}

	// Instantiate the class.
	Assets::init();
}