<?php
// phpcs:ignoreFile

namespace WPPOOL;

class FluentLicensing {

    private static $instances = [];

    private $config = [];

    public $settingsKey = '';

    public function register( $config = [] ) {
        // Get slug from config early for instance management
        $baseName = isset($config['basename']) ? $config['basename'] : plugin_basename(__FILE__);
        $slug = isset($config['slug']) ? $config['slug'] : explode('/', $baseName)[0];

        if ( isset(self::$instances[ $slug ]) ) {
            return self::$instances[ $slug ]; // Return existing instance for this slug
        }

        if ( empty($config['basename']) || empty($config['version']) || empty($config['api_url']) ) {
            throw new \Exception('Invalid configuration provided for FluentLicensing. Please provide basename, version, and api_url.');
        }

        $this->config = $config;
        $baseName = isset($config['basename']) ? $config['basename'] : plugin_basename(__FILE__);

        $slug = isset($config['slug']) ? $config['slug'] : explode('/', $baseName)[0];
        $this->config['slug'] = (string) $slug;

        $this->settingsKey = isset($config['settings_key']) ? $config['settings_key'] : '__' . $this->config['slug'] . '_sl_info';

        $config = $this->config;

        if ( empty($config['license_key']) && empty($config['license_key_callback']) ) {
            $config['license_key_callback'] = function () {
                return $this->getCurrentLicenseKey();
            };
        }

        if ( ! class_exists('\\' . __NAMESPACE__ . '\PluginUpdater') ) {
            require_once __DIR__ . '/PluginUpdater.php';
        }

        // Initialize the updater with the provided configuration.
        new PluginUpdater($config);

        self::$instances[ $slug ] = $this; // Set the instance for this slug

        return self::$instances[ $slug ];
    }

    public function getConfig( $key ) {
        if ( isset($this->config[ $key ]) ) {
            return $this->config[ $key ]; // Return the requested configuration value.
        }

        throw new \Exception( sprintf( esc_html__( "Configuration key '%s' does not exist.", 'social-contact-form-ultimate' ), esc_html( $key ) ) );
    }

    public static function getInstance( $slug = null ) {
        // If no slug provided, return the first registered instance for backward compatibility
        if ( $slug === null ) {
            if ( empty(self::$instances) ) {
                throw new \Exception('Licensing is not registered. Please call register() method first.');
            }
            return reset(self::$instances);
        }

        // Return specific instance by slug
        if ( ! isset(self::$instances[ $slug ]) ) {
            throw new \Exception( sprintf( esc_html__( "Licensing for slug '%s' is not registered. Please call register() method first.", 'social-contact-form-ultimate' ), esc_html( $slug ) ) );
        }

        return self::$instances[ $slug ];
    }

    public function activate( $licenseKey = '' ) {
        if ( ! $licenseKey ) {
            return new \WP_Error('license_key_missing', 'License key is required for activation.');
        }

        $response = $this->apiRequest('activate_license', [
            'license_key' => $licenseKey,
        ]);

        if ( is_wp_error($response) ) {
            return $response; // Return the error response if there is an error.
        }

        $saveData = [
            'license_key'     => $licenseKey,
            'status'          => ( isset( $response['status'] ) && ! empty( $response['status'] ) ) ? $response['status'] : 'valid',
            'variation_id'    => ( isset( $response['variation_id'] ) && ! empty( $response['variation_id'] ) ) ? $response['variation_id'] : '',
            'variation_title' => ( isset( $response['variation_title'] ) && ! empty( $response['variation_title'] ) ) ? $response['variation_title'] : '',
            'expires'         => ( isset( $response['expiration_date'] ) && ! empty( $response['expiration_date'] ) ) ? $response['expiration_date'] : '',
            'activation_hash' => ( isset( $response['activation_hash'] ) && ! empty( $response['activation_hash'] ) ) ? $response['activation_hash'] : '',
        ];

        // Save the license data to the database.
        update_option($this->settingsKey, $saveData, false);

        // Added Hook.
        do_action('wppool_license_activated', $this->config['plugin_slug'], $saveData);

        return $saveData; // Return the saved data.
    }

    public function deactivate() {
        $deactivated = $this->apiRequest('deactivate_license', [
            'license_key' => $this->getCurrentLicenseKey(),
        ]);

        delete_option($this->settingsKey); // Remove the license data from the database.

        // Remove Appsero License too.
        $appser_license_key = 'appsero_' . md5( $this->config['plugin_slug'] ) . '_manage_license';
        delete_option( $appser_license_key );

        // Added Hook.
        do_action('wppool_license_deactivated', $this->config['plugin_slug']);

        return $deactivated;
    }

    public function getStatus( $remoteFetch = false ) {
        $currentLicense = get_option($this->settingsKey, []);
        if ( ! $currentLicense || ! is_array($currentLicense) || empty($currentLicense['license_key']) ) {

            // Old Appsero License.
            $appser_license_key = 'appsero_' . md5( $this->config['plugin_slug'] ) . '_manage_license';

            $appsero_license = get_option( $appser_license_key, [] );

            if ( ! empty( $appsero_license['status'] ) && 'activate' === $appsero_license['status'] ) {
                $currentLicense = array(
                    'license_key'     => isset( $appsero_license['key'] ) ? sanitize_text_field( $appsero_license['key'] ) : '',
                    'status'          => 'valid',
                    'variation_id'    => isset( $appsero_license['source_id'] ) ? sanitize_text_field( $appsero_license['source_id'] ) : '',
                    'variation_title' => isset( $appsero_license['title'] ) ? sanitize_text_field( $appsero_license['title'] ) : '',
                    'expires'         => '',
                    'activation_hash' => '',
                );

                return $currentLicense;
            }

            $currentLicense = [
                'license_key'     => '',
                'status'          => 'unregistered',
                'variation_id'    => '',
                'variation_title' => '',
                'expires'         => '',
            ];
        }

        if ( ! $remoteFetch ) {
            return $currentLicense; // Return the current license status without fetching from the API.
        }

        $remoteStatus = $this->apiRequest('check_license', [
            'license_key'     => $currentLicense['license_key'],
            'activation_hash' => $currentLicense['activation_hash'],
            'item_id'         => $this->config['item_id'],
            'site_url'        => home_url(),
        ]);

        if ( is_wp_error($remoteStatus) ) {
            return $remoteStatus; // Return the error response if there is an error.
        }

        $status = ( isset( $remoteStatus['status'] ) && ! empty( $remoteStatus['status'] ) ) ? $remoteStatus['status'] : 'unregistered';
        $errorType = ( isset( $remoteStatus['error_type'] ) && ! empty( $remoteStatus['error_type'] ) ) ? $remoteStatus['error_type'] : '';

        if ( ! empty($currentLicense['status']) ) {
            $currentLicense['status'] = $status;
            if ( ! empty($remoteStatus['expiration_date']) ) {
                $currentLicense['expires'] = sanitize_text_field($currentLicense['expires']);
            }

            if ( ! empty($remoteStatus['variation_id']) ) {
                $currentLicense['variation_id'] = sanitize_text_field($remoteStatus['variation_id']);
            }

            if ( ! empty($remoteStatus['variation_title']) ) {
                $currentLicense['variation_title'] = sanitize_text_field($remoteStatus['variation_title']);
            }

            update_option($this->settingsKey, $currentLicense, false); // Save the updated license status.
        } else {
            $currentLicense['status'] = 'error';
        }

        $currentLicense['renew_url'] = ( isset( $remoteStatus['renew_url'] ) && ! empty( $remoteStatus['renew_url'] ) ) ? $remoteStatus['renew_url'] : '';
        $currentLicense['is_expired'] = ( isset( $remoteStatus['is_expired'] ) && ! empty( $remoteStatus['is_expired'] ) ) ? $remoteStatus['is_expired'] : false;

        if ( $errorType ) {
            $currentLicense['error_type'] = $errorType;
            $currentLicense['error_message'] = $remoteStatus['message'];
        }

        return $currentLicense;
    }

    public function getCurrentLicenseKey() {
        $status = $this->getStatus();
        return ( isset( $status['license_key'] ) && ! empty( $status['license_key'] ) ) ? $status['license_key'] : ''; // Return the current license key.
    }

    private function apiRequest( $action, $data = [] ) {
        $url = $this->config['api_url'];
        $fullUrl = add_query_arg(array(
            'fluent-cart' => 'custom_' . $action,
        ), $url);

        $defaults = [
            'item_id'         => $this->config['item_id'],
            'current_version' => $this->config['version'],
            'site_url'        => home_url(),
        ];

        $payload = wp_parse_args($data, $defaults);

        // send the post request to the API.
        $response = wp_remote_post($fullUrl, array(
            'timeout'   => 15,
            'body'      => $payload,
            'sslverify' => false,
        ));

        if ( is_wp_error($response) ) {
            return $response; // Return the error response if there is an error.
        }

        if ( 200 !== wp_remote_retrieve_response_code($response) ) {
            $errorData = wp_remote_retrieve_body($response);
            $message = 'API request failed with status code: ' . wp_remote_retrieve_response_code($response);
            if ( ! empty($errorData) ) {
                $decodedData = json_decode($errorData, true);
                if ( $decodedData ) {
                    $errorData = $decodedData;
                }

                if ( ! empty($errorData['message']) ) {
                    $message = (string) $errorData['message'];
                }
            }
            return new \WP_Error('api_error', $message, $errorData);
        }

        $responseData = json_decode(wp_remote_retrieve_body($response), true); // Return the decoded response body.

        if ( $responseData ) {
            return $responseData;
        }

        return new \WP_Error('api_error', 'API request returned an empty or not JSON response.', []);
    }
}
