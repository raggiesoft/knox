<?php
/**
 * Loads everything for WP Dark Mode to be functional
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . '\Boot' ) ) {

	/**
	 * Loads everything for WP Dark Mode to be functional
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Boot {

		/**
		 * Singleton instance
		 *
		 * @since 5.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Returns the singleton instance
		 *
		 * @since 5.0.0
		 * @return mixed
		 */
		public static function instance() {
			// Create an instance if not exists, returns only one instance throughout the request.
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Boot ) ) {
				self::$instance = new Boot();
			}

			return self::$instance;
		}

		/**
		 * Defines the constants
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function define_constants() {
			define( 'WP_DARK_MODE_ULTIMATE_PATH', plugin_dir_path( WP_DARK_MODE_ULTIMATE_FILE ) );
			define( 'WP_DARK_MODE_ULTIMATE_INCLUDES', WP_DARK_MODE_ULTIMATE_PATH . 'includes/' );

			define( 'WP_DARK_MODE_ULTIMATE_URL', plugin_dir_url( WP_DARK_MODE_ULTIMATE_FILE ) );
			define( 'WP_DARK_MODE_ULTIMATE_ASSETS', WP_DARK_MODE_ULTIMATE_URL . 'assets/' );
		}
		/**
		 * Is Dark Mode enabled
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_dark_mode_enabled() {
			return wp_validate_boolean( get_option( 'wp_dark_mode_enabled', true ) );
		}

		/**
		 * Loads the required files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_files() {

			$this->load_common_files();

			$dependency = \WP_Dark_Mode\Ultimate\Dependency::get_instance();

			if ( ! $dependency->is_compatible() ) {
				return;
			}

			$this->load_admin_files();

			if ( $this->is_dark_mode_enabled() ) {
				$this->load_public_files();
			}
		}

		/**
		 * Loads the common files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_common_files() {
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-base.php';
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-dependency.php';
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-install.php';

			$dependency = \WP_Dark_Mode\Ultimate\Dependency::get_instance();

			if ( ! $dependency->is_compatible() ) {
				return;
			}

			// Load dependencies.
			if ( file_exists( WP_PLUGIN_DIR . '/wp-dark-mode/includes/traits/trait-options.php' ) ) {
				require_once WP_PLUGIN_DIR . '/wp-dark-mode/includes/traits/trait-options.php';
			}
			if ( file_exists( WP_PLUGIN_DIR . '/wp-dark-mode/includes/traits/trait-utility.php' ) ) {
				require_once WP_PLUGIN_DIR . '/wp-dark-mode/includes/traits/trait-utility.php';
			}

			// Menu switch has both admin and public files.
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/admin/class-admin-switches.php';

			$this->connect_license();
		}

		/**
		 * Loads the public files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_public_files() {
			$dependency = \WP_Dark_Mode\Ultimate\Dependency::get_instance();

			if ( ! $dependency->is_compatible() ) {
				return;
			}
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-hooks.php';
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-assets.php';
			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/classes/class-analytics.php';
		}

		/**
		 * Loads the admin files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_admin_files() {

			if ( ! is_admin() ) {
				return;
			}

			require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/admin/class-admin-hooks.php';

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/admin/class-admin-ajax.php';
			}
		}


		public function connect_license() {

			if ( ! class_exists( '\WPPOOL\License' ) ) {
				require_once WP_DARK_MODE_ULTIMATE_INCLUDES . '/license/class-license.php';
			}

			$license = ( new \WPPOOL\License([
				'plugin_file' => WP_DARK_MODE_ULTIMATE_FILE,
				'plugin_version' => WP_DARK_MODE_ULTIMATE_VERSION,
				'plugin_name' => 'WP Dark Mode Ultimate',
				'parent_slug' => 'wp-dark-mode',
				'menu_title' => 'License Activation',
				'item_id' => 30,
				'appsero_client_id' => '44e81435-c0f1-4149-983b-eb8d9f7a9a66',
				'pricing_page_url' => 'https://wppool.dev/wp-dark-mode-pricing/',
			]) )->connect();

			if ( $license->is_valid() ) {
				add_filter( 'wp_dark_mode_is_ultimate', '__return_true' );
			}
		}

		/**
		 * Starts the plugin
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public static function start() {
			$boot = self::instance();

			// Register hooks.
			$boot->define_constants();

			$boot->load_files();

			// Fires after the plugin is loaded.
			do_action( 'wp_dark_mode_ultimate_loaded' );
		}
	}

	add_action( 'init', function () {
		Boot::start();
	});
}
