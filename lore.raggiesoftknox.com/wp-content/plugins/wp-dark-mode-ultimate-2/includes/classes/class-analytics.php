<?php
/**
 * Analytics for WP Dark Mode Ultimate
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Analytics' ) ) {
	
	/**
	 * Analytics for WP Dark Mode Ultimate
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Analytics extends \WP_Dark_Mode\Ultimate\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;
		/**
		 * Adds action hooks
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'init', array( $this, 'trigger_email_reporting' ) );
		}

		/**
		 * Triggers email reporting
		 *
		 * @since 5.0.0
		 */
		public function trigger_email_reporting() {
			$enabled_email_reporting = $this->get_option('analytics_enabled_email_reporting');
			if ( ! $enabled_email_reporting ) {
				return;
			}

			$report_sent = $this->get_transient('analytics_email_report_sent');

			if ( $report_sent ) {
				return; // Return if report was already sent within the interval
			}

			// Get frequency.
			$frequency = $this->get_option('analytics_email_reporting_frequency');
			$frequencies = array(
				'daily' => 1,
				'weekly' => 7,
				'biweekly' => 14, 
				'monthly' => 30,
				'quarterly' => 90,
				'yearly' => 365,
			);

			$interval = (in_array( $frequency, $frequencies ) ? $frequencies[ $frequency ] : 7 ) * DAY_IN_SECONDS;
			
			// Set transient.
			$this->set_transient('analytics_email_report_sent', true, $interval);

			try {
				$this->send_email_report();
			} catch (\Exception $e) { // phpcs:ignore
				// Do nothing
			}
			
		}

		/**
		 * Sends email report
		 *
		 * @since 5.0.0
		 * @return bool|void Returns true if email sent successfully, void otherwise
		 * @throws \Exception If email address not set or sending fails
		 */
		public function send_email_report() {
			$to = $this->get_option('analytics_email_reporting_address');

			if ( ! $to ) {
				throw new \Exception( 'Email not set' );
			}

			$subject = $this->get_option('analytics_email_reporting_subject');
			$message = $this->get_email_report();
			$headers = array('Content-Type: text/html; charset=UTF-8');

			$send = wp_mail( $to, $subject, $message, $headers );

			if ( ! $send ) {
				throw new \Exception( 'Email not sent' );
			}

			return true;
		}

		/**
		 * Gets email report
		 *
		 * @since 5.0.0
		 */
		public function get_email_report() {

			global $wpdb;

			$frequency = $this->get_option('analytics_email_reporting_frequency');

			$from = '';
			$formatted_frequency = '';

			switch ( $frequency ) {
				case 'daily':
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) );
					$formatted_frequency = 'last 24 hours';
					break;
				case 'weekly':
				default:
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-1 week' ) );
					$formatted_frequency = 'last 7 days';
					break;
				case 'biweekly':
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-2 week' ) );
					$formatted_frequency = 'last 14 days';
					break;
				case 'monthly':
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-1 month' ) );
					$formatted_frequency = 'last 30 days';
					break;
				case 'quarterly':
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-3 month' ) );
					$formatted_frequency = 'last 90 days';
					break;
				case 'yearly':
					$from = gmdate( 'Y-m-d H:i:s', strtotime( '-1 year' ) );
					$formatted_frequency = 'last 365 days';
					break;
			}

			$visitors = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DATE(created_at) AS visit_date, COUNT(*) AS total_visitors,
					(
					SELECT COUNT(*) FROM {$wpdb->prefix}wpdm_visitors WHERE mode = 'dark' AND DATE(created_at) >= %s GROUP BY DATE(created_at)
					) AS dark_mode_users
					FROM {$wpdb->prefix}wpdm_visitors WHERE DATE(created_at) >= %s GROUP BY DATE(created_at)",
					$from, $from
				),
				ARRAY_A
			);

			if ( $visitors ) {
				$visitors = array_map(
					function( $visitor ) {
						// Calculate percentage with 2 decimal places, if total visitors is 0, then percentage is 0.
						$visitor['percentage'] = $visitor['total_visitors'] ? round( ( $visitor['dark_mode_users'] / $visitor['total_visitors'] ) * 100, 2 ) : 0;
						return $visitor;
					},
					$visitors
				);

				$visitors = array_column( $visitors, 'percentage', 'visit_date' );
			} 


			
			ob_start();
			$args = [
				'visitors'    => $visitors,
				'frequency' => $frequency,
				'length'    => $formatted_frequency,
			];
			
			include_once WP_DARK_MODE_ULTIMATE_PATH . 'templates/email-report.php';
			return ob_get_clean();
		}

		
	}

	// Instantiate the class.
	Analytics::init();
}
