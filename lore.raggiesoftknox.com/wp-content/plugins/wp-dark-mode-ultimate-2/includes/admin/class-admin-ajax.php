<?php

/**
 * Handles ajax requests for WP Dark Mode admin 
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Ajax' ) ) {
	/**
	 * Handles ajax requests for WP Dark Mode admin
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Ajax extends \WP_Dark_Mode\Ultimate\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Register ajax actions
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'wp_ajax_wp_dark_mode_admin_upload_image', array( $this, 'upload_image' ) );
		}

		/**
		 * Uploads image
		 *
		 * @since 5.0.0
		 */
		public function upload_image() {
			// Check ajax referer.
			check_ajax_referer( 'wp_dark_mode_admin_ultimate_security', 'security_key' );

			// Stop if ultimate is not active.
			if ( ! $this->is_ultimate() ) {
				wp_send_json_error( __( 'WP Dark Mode Ultimate is not active.', 'wp-dark-mode' ) );
			}

			// Check permissions.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( __( 'You do not have permission to do this.', 'wp-dark-mode' ) );
			}

			// Check if file is uploaded.
			if ( ! isset( $_FILES['file'] ) ) {
				wp_send_json_error( __( 'File not found.', 'wp-dark-mode' ) );
			}

			// Retrieve the uploaded file details
			$file = $_FILES['file'];

			// Check for errors during upload
			if ( $file['error'] === UPLOAD_ERR_OK ) {
				// Generate a unique file name to prevent overwriting       
				$upload_dir = wp_upload_dir(); // Get the upload directory details
				$filename = wp_unique_filename( $upload_dir['path'], $file['name'] ); // Generate a unique filename

				// Define the path for the uploaded file
				$upload_path = $upload_dir['path'] . '/' . $filename;

				// Move the uploaded file to the desired location
				if ( move_uploaded_file( $file['tmp_name'], $upload_path ) ) {
					// Insert the uploaded file into the media library
					$attachment_id = wp_insert_attachment(
						array(
							'post_mime_type' => $file['type'],
							'post_title' => sanitize_file_name( $filename ),
							'post_content' => '',
							'post_status' => 'inherit',
						),
						$upload_path
					);

					// Generate metadata for the attachment
					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );
					wp_update_attachment_metadata( $attachment_id, $attachment_data );

					// Send a success response with attachment ID
					wp_send_json_success( 
						array( 
							'id' => $attachment_id, 
							'url' => wp_get_attachment_url( $attachment_id ),
							'name' => $filename,
						)
					 );
				} else {
					wp_send_json_error( __( 'Failed to move uploaded file.', 'wp-dark-mode' ) );
				}
			} else {
				wp_send_json_error( __( 'Error during file upload: ', 'wp-dark-mode' ) . $file['error'] );
			}
		}
	}

	// Instantiate the class.
	Ajax::init();
}