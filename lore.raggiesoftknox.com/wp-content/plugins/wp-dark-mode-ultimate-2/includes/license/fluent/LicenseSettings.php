<?php
// phpcs:ignoreFile

namespace WPPOOL;

class LicenseSettings {


    private static $instances = [];

    private $licensing = null;

    private $menuArgs = [];

    private $config = [];

    public function register( $licensing, $config = [] ) {
        // Determine slug from licensing config or action_renderer
        $slug = null;
        if ( $licensing && method_exists($licensing, 'getConfig') ) {
            $slug = $licensing->getConfig('slug');
        }

        // If no slug from licensing, try from config
        if ( ! $slug && isset($config['plugin_slug']) ) {
            $slug = $config['plugin_slug'];
        }

        // If no slug available, use action_renderer as fallback
        if ( ! $slug && isset($config['action_renderer']) ) {
            $slug = explode('/', $config['action_renderer'])[0];
        }

        if ( $slug && isset(self::$instances[ $slug ]) ) {
            return self::$instances[ $slug ]; // Return existing instance for this slug
        }

        if ( ! $licensing ) {
            try {
                $licensing = FluentLicensing::getInstance();
            } catch ( \Exception $e ) {
                return new self(); // Return empty instance if FluentLicensing is not available.
            }
        }

        $this->licensing = $licensing;

        if ( ! $this->config ) {
            $defaultLabels = [
                'menu_title'      => 'License Settings',
                'page_title'      => 'License Settings',
                'title'           => 'License Settings',
                'description'     => 'Manage your license settings for the plugin.',
                'license_key'     => 'License Key',
                'purchase_url'    => '',
                'account_url'     => '',
                'plugin_name'     => '',
                'menu_slug'       => '',
                'action_renderer' => '',
            ];

            $this->config = wp_parse_args($config, $defaultLabels);
        }

        $ajaxPrefix = 'wp_ajax_' . $this->licensing->getConfig('slug') . '_license';

        add_action($ajaxPrefix . '_activate', array( $this, 'handleLicenseActivateAjax' ));
        add_action($ajaxPrefix . '_deactivate', array( $this, 'handleLicenseDeactivateAjax' ));
        add_action($ajaxPrefix . '_status', array( $this, 'handleLicenseStatusAjax' ));

        if ( ! empty($this->config['action_renderer']) ) {
            add_action('fluent_licenseing_render_' . $this->config['action_renderer'], array( $this, 'renderLicensingContent' ));
        }

        if ( $slug ) {
            self::$instances[ $slug ] = $this; // Set the instance for this slug
        }

        return $this;
    }

    public static function getInstance( $slug = null ) {
        // If no slug provided, return the first registered instance for backward compatibility
        if ( $slug === null ) {
            if ( empty(self::$instances) ) {
                throw new \Exception('LicenseSettings is not registered. Please call register() method first.');
            }
            return reset(self::$instances);
        }

        // Return specific instance by slug
        if ( ! isset(self::$instances[ $slug ]) ) {
            throw new \Exception( sprintf( esc_html__( "LicenseSettings for slug '%s' is not registered. Please call register() method first.", 'social-contact-form-ultimate' ), esc_html( $slug ) ) );
        }

        return self::$instances[ $slug ];
    }

    public function setConfig( $config = [] ) {
        $this->config = wp_parse_args($config, $this->config);
        return $this;
    }

    public function handleLicenseActivateAjax() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json([
                'message' => 'Sorry! You do not have permission to perform this action.',
            ], 422);
        }

        $nonce = isset($_POST['_nonce']) ? sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ) : '';
        $licenseKey = isset($_POST['license_key']) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

        if ( ! wp_verify_nonce($nonce, 'fct_license_nonce') ) {
            wp_send_json([
                'message' => 'Invalid nonce. Please try again.',
            ], 422);
        }

        if ( ! $licenseKey ) {
            wp_send_json([
                'message' => 'Please provide a valid license key.',
            ], 422);
        }

        $currentLicense = $this->licensing->getStatus();

        if ( $currentLicense['status'] === 'active' && $currentLicense['license_key'] === $licenseKey ) {
            wp_send_json([
                'message' => 'This license key is already active.',
            ], 200);
        }

        $activated = $this->licensing->activate($licenseKey);

        if ( is_wp_error($activated) ) {
            wp_send_json([
                'message' => $activated->get_error_message(),
                'status'  => 'api_error',
            ], 422);
        }

        if ( $activated['status'] !== 'valid' ) {
            wp_send_json([
                'message' => 'License activation failed. Please check your license key.',
                'status'  => $activated['status'],
            ], 422);
        }

        return wp_send_json([
            'message' => 'License activated successfully.',
            'status'  => 'active',
        ], 200);
    }

    public function handleLicenseDeactivateAjax() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json([
                'message' => 'Sorry! You do not have permission to perform this action.',
            ], 422);
        }

        $nonce = isset($_POST['_nonce']) ? sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ) : '';

        if ( ! wp_verify_nonce($nonce, 'fct_license_nonce') ) {
            wp_send_json([
                'message' => 'Invalid nonce. Please try again.',
            ], 422);
        }

        $deactivated = $this->licensing->deactivate();

        wp_send_json([
            'message'            => 'License deactivated successfully.',
            'remote_deactivated' => ! is_wp_error($deactivated),
        ]);
    }

    public function handleLicenseStatusAjax() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json([
                'message' => 'Sorry! You do not have permission to perform this action.',
            ], 422);
        }

        $nonce = isset($_POST['_nonce']) ? sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ) : '';

        if ( ! wp_verify_nonce($nonce, 'fct_license_nonce') ) {
            wp_send_json([
                'message' => 'Invalid nonce. Please try again.',
            ], 422);
        }

        $status = $this->licensing->getStatus(true);

        if ( is_wp_error($status) ) {
            wp_send_json([
                'error_notice' => $status->get_error_message(),
            ]);
        }

        $message = '';
        if ( ! empty($status['is_expired']) ) {
            $message = '<p>Your license has expired. Please renew your license to continue receiving updates and support.</p>';
            if ( ! empty($status['renewal_url']) ) {
                $message .= '<p><a href="' . esc_url($status['renewal_url']) . '" target="_blank" class="button button-primary fct_renew_url_btn">Renew License</a></p>';
            }
        } else if ( ! empty($status['error_type']) && $status['error_type'] === 'disabled' ) {
            $message = ( isset( $status['message'] ) && ! empty( $status['message'] ) ) ? $status['message'] : '<p>Your license has been disabled. Please contact support for assistance.</p>';
        }

        unset($status['license_key']);

        wp_send_json([
            'error_notice' => $message,
            'remote_data'  => $status,
        ]);
    }

    public function addPage( $args ) {
        if ( ! $this->licensing ) {
            return;
        }

        $this->menuArgs = wp_parse_args($args, [
            'type'        => 'submenu', // Can be: menu, options, submenu.
            'page_title'  => ( isset( $this->config['page_title'] ) && ! empty( $this->config['page_title'] ) ) ? $this->config['page_title'] : '',
            'menu_title'  => ( isset( $this->config['menu_title'] ) && ! empty( $this->config['menu_title'] ) ) ? $this->config['menu_title'] : '',
            'capability'  => ( isset( $args['capability'] ) && ! empty( $args['capability'] ) ) ? $args['capability'] : 'manage_options',
            'parent_slug' => ( isset( $args['parent_slug'] ) && ! empty( $args['parent_slug'] ) ) ? $args['parent_slug'] : 'tools.php',
            'menu_slug'   => ( isset( $args['menu_slug'] ) && ! empty( $args['menu_slug'] ) ) ? $args['menu_slug'] : $this->licensing->getConfig('slug') . '-license',
            'menu_icon'   => ( isset( $args['menu_icon'] ) && ! empty( $args['menu_icon'] ) ) ? $args['menu_icon'] : '',
            'position'    => ( isset( $args['position'] ) && ! empty( $args['position'] ) ) ? $args['position'] : 999,
        ]);

        add_action('admin_menu', array( $this, 'createMenuPage' ), 999);

        return $this;
    }

    public function createMenuPage() {
        switch ( $this->menuArgs['type'] ) {
            case 'menu':
                $this->createTopeLevelMenuPage();
                break;
            case 'submenu':
                $this->createSubMenuPage();
                break;
            case 'options':
                $this->createOptionsPage();
                break;
        }
    }

    private function createTopeLevelMenuPage() {
        add_menu_page(
            $this->menuArgs['page_title'],
            $this->menuArgs['menu_title'],
            $this->menuArgs['capability'],
            ( isset( $this->menuArgs['menu_slug'] ) && ! empty( $this->menuArgs['menu_slug'] ) ) ? $this->menuArgs['menu_slug'] : $this->licensing->getConfig('slug') . '-license',
            array( $this, 'renderLicensingContent' ),
            ( isset( $this->menuArgs['menu_icon'] ) && ! empty( $this->menuArgs['menu_icon'] ) ) ? $this->menuArgs['menu_icon'] : 'dashicons-admin-generic',
            ( isset( $this->menuArgs['position'] ) && ! empty( $this->menuArgs['position'] ) ) ? $this->menuArgs['position'] : 100
        );
    }

    private function createOptionsPage() {
        if ( function_exists('add_options_page') ) {
            add_options_page(
                $this->menuArgs['page_title'],
                $this->menuArgs['menu_title'],
                $this->menuArgs['capability'],
                ( isset( $this->menuArgs['menu_slug'] ) && ! empty( $this->menuArgs['menu_slug'] ) ) ? $this->menuArgs['menu_slug'] : $this->licensing->getConfig('slug') . '-license',
                array( $this, 'renderLicensingContent' )
            );
        }
    }

    private function createSubMenuPage() {
        add_submenu_page(
            $this->menuArgs['parent_slug'],
            $this->menuArgs['page_title'],
            $this->menuArgs['menu_title'],
            $this->menuArgs['capability'],
            ( isset( $this->menuArgs['menu_slug'] ) && ! empty( $this->menuArgs['menu_slug'] ) ) ? $this->menuArgs['menu_slug'] : $this->licensing->getConfig('slug') . '-license',
            array( $this, 'renderLicensingContent' ),
            ( isset( $this->menuArgs['position'] ) && ! empty( $this->menuArgs['position'] ) ) ? $this->menuArgs['position'] : 10
        );
    }

    public function renderLicensingContent() {
        if ( ! $this->licensing ) {
            echo '<div class="fct_error"><p>' . esc_html__( 'Licensing instance is not available.', 'social-contact-form-ultimate' ) . '</p></div>';
            return;
        }

        $licenseStatus = $this->licensing->getStatus();
        $purchaseUrl = ( isset( $this->config['purchase_url'] ) && ! empty( $this->config['purchase_url'] ) ) ? $this->config['purchase_url'] : '';
        ?>

        <div class="fct_licensing_wrap">
            <div class="fct_licensing_header">
                <h1><?php echo esc_html($this->config['title']); ?></h1>
                <?php if ( $this->config['account_url'] ) : ?>
                    <a rel="noopener" target="_blank" href="<?php echo esc_url($this->config['account_url']); ?>">Account</a>
                <?php endif; ?>
            </div>

            <div id="fct_license_body" class="fct_licensing_body">
                <?php if ( $licenseStatus['status'] === 'valid' ) : ?>
                    <h2>Your License is Active</h2>
                    <p>Thank you for activating your license for <?php echo esc_html($this->config['plugin_name']); ?>
                        .</p>
                    <p><strong>Status:</strong> <?php echo esc_html(ucfirst($licenseStatus['status'])); ?></p>
                    <?php if ( $licenseStatus['expires'] !== 'lifetime' ) : ?>
                        <p><strong>Expires On:</strong> <?php echo esc_html($licenseStatus['expires']); ?></p>
                    <?php else : ?>
                        <p><strong>Expires On:</strong> Never</p>
                    <?php endif; ?>
                    <p><a id="fct_deactivate_license" href="#">Deactivate License</a></p>

                    <div id="fct_error_wrapper"></div>

                <?php else : ?>
                    <h2>Please Provide the License key of <?php echo esc_html($this->config['plugin_name']); ?></h2>
                    <div class="fct_licensing_form">
                        <input type="text" name="fct_license_key"
                               value="<?php echo esc_attr($licenseStatus['license_key']); ?>"
                               placeholder="Your License Key"/>
                        <button id="license_key_submit" class="button button-primary">Activate License</button>
                    </div>

                    <div id="fct_error_wrapper"></div>

                    <?php if ( $purchaseUrl ) : ?>
                        <div class="fct_purchase_wrap">
                            <p>
                                Don't have a license? <a rel="noopener" href="<?php echo esc_url($purchaseUrl); ?>"
                                                         target="_blank">
                                    Purchase License
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>


                <div class="fct_loader_item">
                    <svg fill="hsl(228, 97%, 42%)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="4" cy="12" r="3">
                            <animate id="spinner_qFRN" begin="0;spinner_OcgL.end+0.25s" attributeName="cy"
                                     calcMode="spline" dur="0.6s" values="12;6;12"
                                     keySplines=".33,.66,.66,1;.33,0,.66,.33"/>
                        </circle>
                        <circle cx="12" cy="12" r="3">
                            <animate begin="spinner_qFRN.begin+0.1s" attributeName="cy" calcMode="spline" dur="0.6s"
                                     values="12;6;12" keySplines=".33,.66,.66,1;.33,0,.66,.33"/>
                        </circle>
                        <circle cx="20" cy="12" r="3">
                            <animate id="spinner_OcgL" begin="spinner_qFRN.begin+0.2s" attributeName="cy"
                                     calcMode="spline" dur="0.6s" values="12;6;12"
                                     keySplines=".33,.66,.66,1;.33,0,.66,.33"/>
                        </circle>
                    </svg>
                </div>
            </div>
        </div>

        <?php
            do_action('wppool_license_settings_render', $this->config['plugin_slug'], $licenseStatus);
        ?>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {

                var licenseBody = document.getElementById('fct_license_body');

                function setError(errorMessage, isHtml = false) {
                    var errorWrapper = document.getElementById('fct_error_wrapper');
                    if (!errorMessage) {
                        if (errorWrapper) {
                            errorWrapper.innerHTML = '';
                        }
                        return;
                    }
                    if (errorWrapper) {
                        // create dom for security
                        var errorDiv = document.createElement('div');
                        errorDiv.className = 'fct_error_notice';

                        if (isHtml) {
                            errorDiv.innerHTML = errorMessage;
                        } else {
                            errorDiv.textContent = errorMessage;
                        }

                        errorWrapper.innerHTML = '';
                        errorWrapper.appendChild(errorDiv);
                    } else {
                        alert(errorMessage);
                    }
                }

                function sendAjaxRequest(action = 'activate', data) {

                    // add class loader
                    licenseBody && licenseBody.classList.add('fct_loading');

                    setError(''); // Clear previous errors

                    var ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
                    var _nonce = '<?php echo esc_js(wp_create_nonce('fct_license_nonce')); ?>';

                    data.action = '<?php echo esc_js($this->licensing->getConfig('slug')); ?>_license_' + action;
                    data._nonce = _nonce;

                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', ajaxUrl, true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function () {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve(JSON.parse(xhr.responseText));
                            } else {
                                // Handle errors and send the response json
                                reject(JSON.parse(xhr.responseText));
                            }
                        };
                        xhr.onerror = function () {
                            reject({
                                message: 'Network error occurred while processing the request.'
                            });
                        };
                        xhr.send(new URLSearchParams(data).toString());

                        // remove class loader on ajax complete
                        xhr.onloadend = function () {
                            licenseBody && licenseBody.classList.remove('fct_loading');
                        }
                    });
                }

                var licenseInformation = {
                    slug: '<?php echo esc_js($this->config['plugin_slug']); ?>',
                }

                var activateRedirection = '<?php echo esc_js( $this->config['activate_redirect_url'] ); ?>'
                var deactivateRedirection = '<?php echo esc_js( $this->config['deactivate_redirect_url'] ); ?>'

                function activateLicense(licenseKey) {
                    if (!licenseKey) {
                        alert('Please enter a valid license key.');
                        return;
                    }

                    sendAjaxRequest('activate', {license_key: licenseKey})
                        .then(response => {
                            // trigger custom event.
                            document.dispatchEvent(new CustomEvent('wppool_license_activated', { detail: licenseInformation }));

                            if ( activateRedirection ) {
                                window.location.href = activateRedirection;
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            // trigger custom event.
                            document.dispatchEvent(new CustomEvent('wppool_license_activated_error', { detail: error }));

                            // set error message.
                            setError(error.message || 'An error occurred while activating the license.');
                        });
                }

                <?php if ( $licenseStatus['status'] !== 'unregistered' ) : ?>
                sendAjaxRequest('status', {})
                    .then(response => {
                        if (response.error_notice) {
                            // trigger custom event.
                            document.dispatchEvent(new CustomEvent('wppool_license_status_error', { detail: response }));

                            // set error message.
                            setError(response.error_notice, true);
                        }
                    });
                <?php endif; ?>

                var activateBtn = document.getElementById('license_key_submit');
                if (activateBtn) {
                    activateBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        var licenseKey = document.querySelector('input[name="fct_license_key"]').value.trim();
                        activateLicense(licenseKey);
                    });
                }

                var deactivateBtn = document.getElementById('fct_deactivate_license');
                if (deactivateBtn) {
                    deactivateBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        sendAjaxRequest('deactivate', {})
                            .then(response => {
                                // trigger custom event.
                                document.dispatchEvent(new CustomEvent('wppool_license_deactivated', { detail: licenseInformation }));

                                // reload the page to reflect changes.
                                if ( deactivateRedirection ) {
                                    window.location.href = deactivateRedirection;
                                } else {
                                    window.location.reload();
                                }
                            })
                            .catch(error => {
                                // trigger custom event.
                                document.dispatchEvent(new CustomEvent('wppool_license_deactivated_error', { detail: error }));

                                // reload the page to reflect changes.
                                window.location.reload();
                            });
                    });
                }

                document.querySelectorAll('.update-nag, .notice, #wpbody-content > .updated, #wpbody-content > .error').forEach(element => element.remove());
            });
        </script>

        <style>
            .fct_licensing_wrap {
                position: relative;
                max-width: 600px;
                margin: 30px auto;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .fct_loader_item {
                display: none;
            }

            .fct_loading .fct_loader_item {
                display: block;
                position: absolute;
                right: 10px;
                bottom: 0px;
            }

            .fct_loader_item svg {
                fill: #686a6b;
                width: 40px;
            }

            .fct_error_notice {
                color: #ff4e16;
                margin-top: 20px;
                font-size: 15px;
            }

            .fct_error_notice p {
                font-size: 15px;
            }

            .fct_purchase_wrap {
                margin-top: 20px;
                display: block;
                overflow: hidden;
            }

            .fct_licensing_header {
                background: #f7fafc;
                padding: 15px 20px;
                border-bottom: 1px solid #ddd;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .fct_licensing_header a {
                text-decoration: none;
                background: #0073aa;
                color: #fff;
                padding: 2px 12px;
                border-radius: 4px;
            }

            .fct_licensing_header h1 {
                margin: 0;
                font-size: 20px;
                padding: 0;
            }

            .fct_licensing_body {
                padding: 30px 20px;
            }

            .fct_licensing_wrap h2 {
                margin-top: 0;
                font-size: 18px;
                margin-bottom: 10px;
            }

            .fct_licensing_form {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                flex-wrap: wrap;
            }

            .fct_licensing_form input {
                width: 100%;
                padding: 6px 10px;
            }
        </style>

        <?php
    }
}