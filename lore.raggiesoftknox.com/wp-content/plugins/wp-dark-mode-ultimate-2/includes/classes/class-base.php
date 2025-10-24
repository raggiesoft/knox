<?php
/**
 * Base abstract class for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Base' ) ) {
	/**
	 * Enqueues script and styles to frontend for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	abstract class Base {

		/**
		 * The instance of the class
		 *
		 * @since 5.0.0
		 * @var array<object>
		 */
		private static $instances = array();

		/**
		 * Returns the instance of the child class
		 *
		 * @since 5.0.0
		 * @return object
		 */
		public static function get_instance() {
			$class_name = get_called_class();

			if ( ! isset( self::$instances[ $class_name ] ) ) {
				self::$instances[ $class_name ] = new $class_name();
			}

			return self::$instances[ $class_name ];
		}

		/**
		 * Initializes the class
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public static function init() {
			$instance = static::get_instance();

			$instance->actions();
			$instance->filters();
		}
		/**
		 * Adds the actions
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function actions() {}

		/**
		 * Adds the filters
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function filters() {}


		/**
		 * Is pro version active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_ultimate() {

			global $wp_dark_mode_license;
		
			// $wp_dark_mode_license->is_valid()
			if( $wp_dark_mode_license && is_object( $wp_dark_mode_license ) && method_exists( $wp_dark_mode_license, 'is_valid' ) && $wp_dark_mode_license->is_valid() ) {
				return true;
			}

			return false;

			// $wp_dark_mode_is_ultimate = apply_filters( 'wp_dark_mode_is_ultimate', false );

			// if ( $wp_dark_mode_is_ultimate ) {
			// 	return true;
			// }
			
		}
		
	}
}