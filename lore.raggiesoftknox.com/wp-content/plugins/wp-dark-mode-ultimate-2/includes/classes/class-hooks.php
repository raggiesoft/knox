<?php
/**
 * Handles all the custom hooks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Hooks' ) ) {
	/**
	 * Handles all the custom hooks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Hooks extends \WP_Dark_Mode\Ultimate\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Register hooks.
		 *
		 * @since 5.0.0
		 */
		public function filters() {

			// Modify the json.
			add_filter( 'wp_dark_mode_json', array( $this, 'wp_dark_mode_json_callback' ) );

			// Modify the content.
			add_filter( 'the_content', array( $this, 'the_content_callback' ) );

			// Check if current page is excluded.
			add_filter( 'wp_dark_mode_is_excluded', array( $this, 'is_excluded' ), 9999 );

			// Check if time based dark mode is enabled.
			add_filter( 'wp_dark_mode_is_time_based_dark_mode', array( $this, 'is_time_based_dark_mode' ) );

			// Check if sunset based dark mode is enabled.
			add_filter( 'wp_dark_mode_is_sunset_based_dark_mode', array( $this, 'is_sunset_based_dark_mode' ) );
		}



		/**
		 * Modify the json.
		 *
		 * @since 5.0.0
		 *
		 * @param array $json The admin json.
		 * @return array
		 */
		public function wp_dark_mode_json_callback( $json ) {

			$json['is_ultimate'] = $this->is_ultimate();

			return $json;
		}

		/**
		 * Modify the content.
		 *
		 * @since 5.0.0
		 *
		 * @param string $content The content.
		 * @return string
		 */
		public function the_content_callback( $content ) {

			// Bail, if ultimate is not active.
			if ( ! $this->is_ultimate() ) {
				return $content;
			}

			// Bail, if not single post.
			if ( ! is_singular() ) {
				return $content;
			}

			// Bail, if frontend darkmode is disabled.
			if ( ! $this->get_option( 'frontend_enabled' ) ) {
				return $content;
			}

			// Bail, if post type is not post or page.
			if ( ! in_array( get_post_type(), array( 'post', 'page' ), true ) ) {
				return $content;
			}

			$enabled_top_of_posts = $this->get_option( 'content_switch_enabled_top_of_posts' );
			$enabled_top_of_pages = $this->get_option( 'content_switch_enabled_top_of_pages' );

			// Bail, if content switch is disabled.
			if ( ! $enabled_top_of_posts && ! $enabled_top_of_pages ) {
				return $content;
			}

			// Content switch style.
			$content_switch_style = $this->get_option( 'content_switch_style' );

			$switch_content = do_shortcode( wp_sprintf( '[wp-dark-mode style="%s"]', esc_attr( $content_switch_style ) ) );

			// Add content switch on top of posts.
			if ( $enabled_top_of_posts && is_single() ) {
				$content = $switch_content . $content;
			}

			// Add content switch on top of pages.
			if ( $enabled_top_of_pages && is_page() ) {
				$content = $switch_content . $content;
			}

			return $content;
		}


		/**
		 * Is excluded
		 *
		 * @since 5.0.0
		 * @since 5.0.0
		 * @return boolean
		 */
		public function is_excluded( $is_excluded = false ) {

			// Bail, if ultimate is not active.
			if ( ! $this->is_ultimate() ) {
				return $is_excluded;
			}

			/**
			 * For single page, post, search, 404, etc.
			 */
			if ( is_home() || is_single() || is_singular() || is_front_page() || is_search() || is_404() ) {

				// Single post id.
				$post_id = get_queried_object_id();
				$post_type = get_post_type();

				$is_excluded = false;

				$exclude_all = false;

				$id_in = [];
				$id_not_in = [];

				$taxonomies = [];
				
				$exclude_all_taxonomies = false;
				$taxonomy_in = [];
				$taxonomy_not_in = [];

				// If not Product.
				if (  'product' !== $post_type ) {
					
					$exclude_all = wp_validate_boolean( $this->get_option( 'excludes_posts_all' ) );
					$id_in = $this->get_option( 'excludes_posts' );
					$id_not_in = $this->get_option( 'excludes_posts_except' );

					if ( ! is_front_page() && 'post' === $post_type ) {
						// Check taxonomies.
						$taxonomies = wp_get_post_terms( $post_id, array_keys( $this->get_taxonomies() ), array( 'fields' => 'ids' ) );

						$exclude_all_taxonomies = wp_validate_boolean( $this->get_option( 'excludes_taxonomies_all' ) );
						$taxonomy_in = $this->get_option( 'excludes_taxonomies' );
						$taxonomy_not_in = $this->get_option( 'excludes_taxonomies_except' );

						if ( $exclude_all_taxonomies ) {

							if ( $taxonomy_not_in && is_array( $taxonomy_not_in ) ) {
								return count( array_intersect( $taxonomies, $taxonomy_not_in ) ) === 0;
							}
						} else {
							if ( $taxonomy_in && is_array( $taxonomy_in ) ) {
								return count( array_intersect( $taxonomies, $taxonomy_in ) ) > 0;
							}
						}
						
					}

				} else {
					$exclude_all = $this->get_option( 'excludes_wc_products_all' );
					$id_in = $this->get_option( 'excludes_wc_products' );
					$id_not_in = $this->get_option( 'excludes_wc_products_except' );
					
					// Product categories of current post.
					$taxonomies = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );

					$exclude_all_taxonomies = wp_validate_boolean( $this->get_option( 'excludes_wc_categories_all' ) );
					$taxonomy_in = $this->get_option( 'excludes_wc_categories' );
					$taxonomy_not_in = $this->get_option( 'excludes_wc_categories_except' );

				}

				// If exclude all is enabled.
				if ( $exclude_all ) {
					if ( $id_not_in && is_array( $id_not_in ) && !empty( $id_not_in ) ) {
						$is_excluded = !in_array( $post_id, $id_not_in );
					} else {
						$is_excluded = true;
					}
				} else {

					if ( $id_in && is_array( $id_in ) && !empty( $id_in ) ){
						$is_excluded = in_array( $post_id, $id_in );
					} else {
						$is_excluded = false;
					}
				}

				if ( $is_excluded ) return true;

				if ( is_front_page() ) {
					return false;
				}

				// Calculate post from taxonomy.
				if ( empty( $taxonomies ) && ! is_front_page() ) {

					// If exclude all is enabled.
					if ( $exclude_all_taxonomies) {
						if ( $taxonomy_not_in && is_array( $taxonomy_not_in ) && !empty( $taxonomy_not_in ) ) {
							$is_excluded = !array_intersect( $taxonomies, $taxonomy_not_in );

							$is_excluded = !empty( $is_excluded );
						} else {
							$is_excluded = true;
						}
					} else {
						if ( $taxonomy_in && is_array( $taxonomy_in ) && !empty( $taxonomy_in ) ) {
							$is_excluded = array_intersect( $taxonomies, $taxonomy_in );
							$is_excluded = !empty( $is_excluded );
						} else {
							$is_excluded = false;
						}
					}

				}

			} else {
				// If it's a WooCommerce category page.
				if ( function_exists( 'is_product_category' ) && is_product_category() ) {
					// Product Category Page.
					$term_id = get_queried_object_id();

					$exclude_all = $this->get_option( 'excludes_wc_categories_all' );
					$id_in = $this->get_option( 'excludes_wc_categories' );
					$id_not_in = $this->get_option( 'excludes_wc_categories_except' );

					// If exclude all is enabled.
					if ( $exclude_all ) {
						if ( $id_not_in && is_array( $id_not_in ) && !empty( $id_not_in ) ) {
							$is_excluded = !in_array( $term_id, $id_not_in );
						} else {
							$is_excluded = true;
						}
					} else {
						if ( $id_in && is_array( $id_in ) && !empty( $id_in ) ){
							$is_excluded = in_array( $term_id, $id_in );
						} else {
							$is_excluded = false;
						}
					}


				}
				
				// If category or tags.
				if ( ! is_home() && ( is_category() || is_tag() )  ) {
					// Category or Tag Page.
					$term_id = get_queried_object_id();

					$exclude_all = wp_validate_boolean( $this->get_option( 'excludes_taxonomies_all' ) );
					$id_in = $this->get_option( 'excludes_taxonomies' );
					$id_not_in = $this->get_option( 'excludes_taxonomies_except' );

					// If exclude all is enabled.
					if ( $exclude_all ) {
						if ( $id_not_in && is_array( $id_not_in ) && !empty( $id_not_in ) ) {
							$is_excluded = !in_array( $term_id, $id_not_in );
						} else {
							$is_excluded = true;
						}
					} else {
						if ( $id_in && is_array( $id_in ) && !empty( $id_in ) ){
							$is_excluded = in_array( $term_id, $id_in );
						} else {
							$is_excluded = false;
						}
					}
				}

				// for shop page.
				if ( function_exists( 'is_shop' ) && is_shop() ) {

					$term_id = get_option( 'woocommerce_shop_page_id' );

					$exclude_all = $this->get_option( 'excludes_posts_all' );
					$id_in = $this->get_option( 'excludes_posts' );
					$id_not_in = $this->get_option( 'excludes_posts_except' );

					// If exclude all is enabled.
					if ( $exclude_all ) {
						if ( $id_not_in && is_array( $id_not_in ) && !empty( $id_not_in ) ) {
							$is_excluded = !in_array( $term_id, $id_not_in );
						} else {
							$is_excluded = true;
						}
					} else {
						if ( $id_in && is_array( $id_in ) && !empty( $id_in ) ){
							$is_excluded = in_array( $term_id, $id_in );
						} else {
							$is_excluded = false;
						}
					}
				}
			}

			return $is_excluded;
		}



		/**
		 * Get taxonomies.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_taxonomies() {
			$taxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true,
			), 'objects' );

			$taxonomies = array_filter( $taxonomies, function ($taxonomy) {
				return ! in_array( $taxonomy->name, array( 'product_cat', 'product_tag', 'post_format' ), true );
			} );

			// make it to slug => label.
			$taxonomies = array_combine( wp_list_pluck( $taxonomies, 'name' ), wp_list_pluck( $taxonomies, 'label' ) );

			return $taxonomies;
		}

		/**
		 * Is single post
		 *
		 * @since 5.0.0
		 * @return boolean
		 */
		public function is_single() {
			return is_singular() 
				|| is_single()
				|| is_page() 
				|| is_home() 
				|| is_front_page()
				|| is_search()
				|| function_exists( 'is_shop' ) && is_shop()
				|| is_404();
		}

		/**
		 * Is single product.
		 *
		 * @since 5.0.0
		 * @return boolean
		 */
		public function is_single_product() {

			if ( function_exists( 'is_product' ) ) {
				return is_product() && ! is_shop();
			}
		}
					
		/**
		 * Get Excluded Post Ids
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_excluded_post_ids() {}

		/**
		 * Get Excepted Product Ids
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_excepted_product_ids() {}

		/**
		 * Get exclude archive Ids
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_exclude_archive_ids() {}

		/**
		 * Get except archive Ids
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_except_archive_ids() {}


		/**
		 * Get device time
		 *
		 * @since 5.0.0
		 * @return int
		 */
		public function get_device_time() {
			// Parsing device timezone offset.
			$timezone_offset = isset( $_COOKIE['wp-dark-mode-timezone'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wp-dark-mode-timezone'] ) ) : get_option( 'gmt_offset' );

			$device_time = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
			$device_time->modify( $timezone_offset . 'hours' );

			return $device_time->getTimestamp();
		}

		/**
		 * Is time based dark mode
		 *
		 * @since 5.0.0
		 * @return boolean
		 */
		public function is_time_based_dark_mode() {

			$device_time = $this->get_device_time();
			$device_date = date( 'Y-m-d ', $device_time );

			// Times from options.
			$start_time = $this->get_option( 'frontend_time_starts' );
			// Start time with current date.
			$start_time = strtotime( $device_date . $start_time );

			$end_time = $this->get_option( 'frontend_time_ends' );
			// End time with current date.
			$end_time = strtotime( $device_date . $end_time );

			// For reverse time.
			if ( $end_time < $start_time ) {
				// Add one day to end time if its already passed.
				if ( $end_time < $device_time ) {
					$end_time = strtotime( '+1 day', $end_time );
				} else {
					// Subtract one day from start time if its already passed.
					$start_time = strtotime( '-1 day', $start_time );
				}
			}

			// Check if current time is between start and end time.
			if ( $device_time >= $start_time && $device_time <= $end_time ) {
				return true;
			}

			return false;
		}

		/**
		 * Is sunset based dark mode
		 *
		 * @since 5.0.0
		 * @return boolean
		 */
		public function is_sunset_based_dark_mode() {

			// Device time.
			$device_time = $this->get_device_time();

			// Get device location
			$location = isset( $_COOKIE['wp-dark-mode-location'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wp-dark-mode-location'] ) ) : null;

			// Bail, if location is not set.
			if ( ! $location ) {
				return false;
			}

			$location = \explode( ',', $location );
			$latitude = $location[0] ?? null;
			$longitude = $location[1] ?? null;

			// Bail, if latitude or longitude is not set.
			if ( ! $latitude || ! $longitude ) {
				return false;
			}

			$timezone_offset = isset( $_COOKIE['wp-dark-mode-timezone'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wp-dark-mode-timezone'] ) ) : get_option( 'gmt_offset' );

			$sun_info = date_sun_info( $device_time, $latitude, $longitude );

			// Bail, if sunset or sunrise is not set.
			if ( ! $sun_info['sunset'] || ! $sun_info['sunrise'] ) {
				return false;
			}

			$sunset = $sun_info['sunset'] + ( $timezone_offset * 3600 );
			$sunrise = $sun_info['sunrise'] + ( $timezone_offset * 3600 );

			// If sunrise date is greater than device time, then subtract one day from sunrise.
			if ( $sunrise > $device_time ) {
				$sunrise = strtotime( '-1 day', $sunrise );
			}

			// Check if current time is between sunset and sunrise.
			if ( $device_time >= $sunset || $device_time <= $sunrise ) {
				return true;
			}

			return false;
		}



		/**
		 * Get timezone from offset
		 *
		 * @since 5.0.0
		 * @param int $offset Offset.
		 * @return string|null
		 */
		function get_timezone_from_offset( $offset = 0 ) {
			// Get a list of timezone identifiers
			$timezones = \DateTimeZone::listIdentifiers();
		
			// Iterate through the timezones and find the one with the matching offset
			foreach ($timezones as $timezone) {
				$timezoneObject = new \DateTimeZone($timezone);
				$currentOffset = $timezoneObject->getOffset(new \DateTime());
		
				if ($currentOffset / 3600 == $offset) {
					return $timezone;
				}
			}
		
			// Return null if no matching timezone is found
			return 'UTC';
		}
	}

	// Instantiate the class.
	Hooks::init();
}
