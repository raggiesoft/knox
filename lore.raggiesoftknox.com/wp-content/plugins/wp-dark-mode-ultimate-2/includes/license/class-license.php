<?php
/**
 * WPPOOL Hybrid License System.
 *
 * A robust, product-agnostic licensing system that supports both Appsero and Fluent licensing.
 * This class can be integrated across multiple products with minimal configuration.
 *
 * @package WPPOOL
 * @version 2.0.0
 */

namespace WPPOOL;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

/**
 * Hybrid License Manager Class
 *
 * Handles dual licensing system (Appsero + Fluent) with automatic fallback,
 * caching, error handling, and comprehensive logging.
 */
class License {

    /**
     * Configuration array for the license system
     *
     * @var array
     */
    protected $config = [
        // Required Plugin Information
        'plugin_file'    => '',        // Full path to main plugin file
        'plugin_name'    => '',        // Human-readable plugin name
        'plugin_version' => '',        // Plugin version
        'item_id'        => '',        // Fluent item/product ID
		'appsero_client_id' => '', // Appsero client id

        // Menu Configuration
        'menu_title'      => 'License', // Menu title for license page
        'menu_slug'       => '',        // Custom menu slug (optional)
        'parent_slug'     => '',        // Admin menu parent slug (optional)
        'show_action_link' => true,     // Show action link in plugins page

        // External URLs
        'pricing_page_url' => '',       // Product pricing page URL
        'account_url'      => 'https://portal.wppool.dev/', // Account URL
        'api_url'          => 'https://portal.wppool.dev/',        // Fluent API URL

		'activate_redirect_url' => '',
		'deactivate_redirect_url' => '',

    ];

    /**
     * License data from active provider
     *
     * @var array
     */
    public $data = [];

    /**
     * License validity status
     *
     * @var bool|null
     */
    public $is_valid = null;

    /**
     * Fluent licensing instance
     *
     * @var object|null
     */
    public $license = null;

	/**
	 * License Settings.
	 */
	public $license_settings = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor - Initialize the licensing system
     *
     * @param array $config Configuration array
     * @throws \Exception If required configuration is missing
     * @return self
     */
    public function __construct( $config = [] ) {
        // Validate required configuration
        $this->validate_config( $config );

        // Merge with defaults
        $this->config = array_merge( $this->config, $config );
    }

	/**
	 * Get Slug by File Path.
	 *
	 * @param string $file_path File path.
	 * @return string Slug.
	 */
	private function get_slug_by_file_path( $file_path ) {
		// Return the directory name.
		return dirname( plugin_basename( $file_path ) );
	}

    /**
     * Validate required configuration parameters
     *
     * @param array $config Configuration array
     * @throws \Exception If required parameters are missing
     */
    protected function validate_config( $config ) {
        $required_fields = [ 'plugin_file', 'plugin_name', 'plugin_version' ];

        foreach ( $required_fields as $field ) {
            if ( empty( $config[ $field ] ) ) {
                throw new \Exception( esc_html( "WPPOOL License: Missing required configuration: {$field}" ) );
            }
        }

        // Require at least one licensing system
        if ( empty( $config['item_id'] ) ) {
            throw new \Exception( 'WPPOOL License: Item ID must be configured' );
        }
    }

	/**
	 * Connect.
	 */
	public function connect() {
		$this->connect_fluent();
		$this->connect_appsero();
		$this->add_action_link();
		$this->add_action_link();
		return $this;
	}

    /**
     * Connect Fluent.
     *
     * @return mixed
     */
	public function connect_fluent() {

		if ( empty( $this->config['item_id'] ) ) {
			return;
		}

		if ( ! class_exists( '\WPPOOL\FluentLicensing' ) ) {
            require_once __DIR__ . '/fluent/FluentLicensing.php';
            require_once __DIR__ . '/fluent/PluginUpdater.php';
            require_once __DIR__ . '/fluent/LicenseSettings.php';
        }

		$this->license = ( new \WPPOOL\FluentLicensing() )->register(
			array(
				'version'     => $this->config['plugin_version'],
				'item_id'     => $this->config['item_id'],
				'basename'    => plugin_basename( $this->config['plugin_file'] ),
				'api_url'     => $this->config['api_url'],
				'plugin_name' => $this->config['plugin_name'],
				'menu_title'  => $this->config['menu_title'],
				'menu_slug'   => $this->config['menu_slug'],
				'plugin_slug' => $this->get_slug_by_file_path( $this->config['plugin_file'] ),
			)
		);

		$this->license_settings = ( new \WPPOOL\LicenseSettings() )
		->register( $this->license )
		->setConfig(
			array(
				'menu_title'      => $this->config['menu_title'],
				'title'           => wp_sprintf( '%s License', $this->config['plugin_name'] ),
				'license_key'     => 'License Key',
				'action_renderer' => plugin_basename( $this->config['plugin_file'] ),
				'purchase_url'    => $this->config['pricing_page_url'],
				'account_url'     => $this->config['account_url'],
				'plugin_name'     => $this->config['plugin_name'],
				'menu_slug'       => $this->config['menu_slug'],
				'plugin_slug'     => $this->get_slug_by_file_path( $this->config['plugin_file'] ),
				'activate_redirect_url' => $this->config['activate_redirect_url'],
				'deactivate_redirect_url' => isset( $this->config['deactivate_redirect_url'] ) && ! empty( $this->config['deactivate_redirect_url'] ) ? $this->config['deactivate_redirect_url'] : $this->config['activate_redirect_url'],
			) );

		// Initialize license settings if parent_slug is provided.
		if ( ! empty( $this->config['parent_slug'] ) ) {


			// If $this->config['parent_slug'] not empty, add page to license settings.
			$this->license_settings->addPage(
				array(
					'type'        => 'submenu',
					'menu_slug'   => $this->config['menu_slug'],
					'parent_slug' => $this->config['parent_slug'],
				)
			);
		}

		// Check Fluent license status.
		$license_status = $this->license->getStatus();
		if ( ! empty( $license_status['status'] ) && 'valid' === $license_status['status'] ) {
			$this->is_valid = true;
			$this->data     = $license_status;
		}
	}
	/**
     * Connect Appsero.
     *
     * @return mixed
     */
	public function connect_appsero() {

		if ( empty( $this->config['appsero_client_id'] ) ) {
			return;
		}

		if ( ! class_exists( '\WPPOOL\Appsero\Client' ) ) {
			require_once __DIR__ . '/appsero/Client.php';
		}

		add_filter('appsero_is_local', '__return_false');

		$appsero = new \WPPOOL\Appsero\Client(
			$this->config['appsero_client_id'],
			$this->config['plugin_name'],
			$this->config['plugin_file'] );

		$appsero->insights()->hide_notice()->init();
	}

	/**
	 * Add action link.
	 */
	public function add_action_link() {
		// Add plugins row link.
		if ( $this->config['show_action_link'] ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->config['plugin_file'] ), array( $this, 'add_plugins_row_link' ) );
		}
	}

	/**
	 * Check if license is valid.
	 *
	 * @return bool License validity status.
	 */
	public function is_valid() {
		return (bool) $this->is_valid;
	}

	/**
	 * Get license data.
	 *
	 * @return array License data array.
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get license information for display.
	 *
	 * @return array Formatted license information.
	 */
	public function get_license_info() {
		return array(
			'is_valid'        => $this->is_valid(),
			'plugin_name'     => $this->config['plugin_name'],
			'plugin_version'  => $this->config['plugin_version'],
			'license_data'    => $this->get_data(),
		);
	}

	/**
	 * Get plugin configuration.
	 *
	 * @param string|null $key Specific config key (optional).
	 * @return mixed Full config array or specific value.
	 */
	public function get_config( $key = null ) {
		if ( null === $key ) {
			return $this->config;
		}

		return isset( $this->config[ $key ] ) ? $this->config[ $key ] : null;
	}

	/**
	 * Add plugins row link.
	 *
	 * @param array $links Plugin action links.
	 * @return array Plugin action links.
	 */
	public function add_plugins_row_link( $links ) {
		$text = ( $this->is_valid() ? __( 'Manage', 'social-contact-form-ultimate' ) : __( 'Activate', 'social-contact-form-ultimate' ) ) . ' License';
		$links[] = '<a href="' . admin_url( 'admin.php?page=' . ( isset($this->config['menu_slug']) && $this->config['menu_slug'] ? $this->config['menu_slug'] : ( $this->get_slug_by_file_path( $this->config['plugin_file'] ) . '-license' ) ) ) . '">' . $text . '</a>';
		return $links;
	}
}
