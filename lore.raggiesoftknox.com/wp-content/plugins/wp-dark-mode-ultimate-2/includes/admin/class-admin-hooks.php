<?php
/**
 * Handles all the custom hooks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate\Admin;

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
			add_filter( 'wp_dark_mode_admin_json', array( $this, 'modify_wp_dark_mode_admin_json' ) );

			// Activation.
			register_deactivation_hook( WP_DARK_MODE_ULTIMATE_FILE, array( $this, 'deactivate' ) );

			// Admin init.
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}

		/**
		 * Modify the admin json.
		 *
		 * @since 5.0.0
		 *
		 * @param array $json The admin json.
		 * @return array
		 */
		public function modify_wp_dark_mode_admin_json( $json ) {

			$json['is_ultimate'] = $this->is_ultimate();
			$json['url']['ultimate'] = WP_DARK_MODE_ULTIMATE_URL;
			$json['security_key'] = wp_create_nonce( 'wp_dark_mode_admin_ultimate_security' );

			return $json;
		}

		/**
		 * Activate the plugin.
		 *
		 * @since 5.0.0
		 */
		public function deactivate() {
			
			// Set ultimate installed.
			delete_option( 'wp_dark_mode_ultimate_redirect' );
		}

		/**
		 * Redirect to license page on activation.
		 *
		 * @since 5.0.0
		 */
		public function admin_init() {
			// Bail, if redirect is set already.
			if ( get_option( 'wp_dark_mode_ultimate_redirect' ) ) {
				return;
			}

			// Set redirect option.
			update_option( 'wp_dark_mode_ultimate_redirect', true );

			// Bail, if not ultimate.
			if ( $this->is_ultimate() ) {
				return;
			}

			// Redirect to license page.
			wp_safe_redirect( admin_url( 'admin.php?page=wp-dark-mode-ultimate-license' ) );
			exit;
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 5.0.0
		 */
		public function admin_head() {

			$screen = get_current_screen();

			if ( !in_array($screen->id, [
				'toplevel_page_wp-dark-mode', 
				'wp-dark-mode_page_wp-dark-mode-social-share', 
				'wp-dark-mode_page_wp-dark-mode-get-started',
				'wp-dark-mode_page_wp-dark-mode-recommended-plugins',
				'wp-dark-mode_page_wp-dark-mode-ultimate-license',
			])) {
				return;
			}

			$user = wp_get_current_user();

			echo wp_sprintf('<script>!function(Gleap,t,i){
				if(!(Gleap=window.Gleap=window.Gleap||[]).invoked){for(window.GleapActions=[],Gleap.invoked=!0,Gleap.methods=["identify","setEnvironment","setTicketAttribute","setTags","attachCustomData","setCustomData","removeCustomData","clearCustomData","registerCustomAction","trackEvent","setUseCookies","log","preFillForm","showSurvey","sendSilentCrashReport","startFeedbackFlow","startBot","setAppBuildNumber","setAppVersionCode","setApiUrl","setFrameUrl","isOpened","open","close","on","setLanguage","setOfflineMode","startClassicForm","initialize","disableConsoleLogOverwrite","logEvent","hide","enableShortcuts","showFeedbackButton","destroy","getIdentity","isUserIdentified","clearIdentity","openConversations","openConversation","openHelpCenterCollection","openHelpCenterArticle","openHelpCenter","searchHelpCenter","openNewsArticle","openChecklists","startChecklist","openNews","openFeatureRequests","isLiveMode"],Gleap.f=function(e){return function(){var t=Array.prototype.slice.call(arguments);window.GleapActions.push({e:e,a:t})}},t=0;t<Gleap.methods.length;t++)Gleap[i=Gleap.methods[t]]=Gleap.f(i);Gleap.load=function(){var t=document.getElementsByTagName("head")[0],i=document.createElement("script");i.type="text/javascript",i.async=!0,i.src="https://sdk.gleap.io/latest/index.js",t.appendChild(i)},Gleap.load();
				Gleap.identify("%s", {
					email: "%s",
					name: "%s",
					role: "%s",
					plugin_name: "WP Dark Mode",
					version: "%s",
					site_url: "%s",
					is_multisite: "%s",
					wp_version: "%s",
					php_version: "%s",
					theme_name: "%s",
					ultimate_active: "%s",
				});
				Gleap.initialize("d8ZYD7leSWHDkJD0vMrmy1ARdEvfPYsu")
			}}();</script>',

			NONCE_SALT . '-' . $user->ID,
			$user->user_email,
			$user->display_name,
			$user->roles[0],
			WP_DARK_MODE_VERSION,
			get_site_url(),
			is_multisite() ? 'Yes' : 'No',
			get_bloginfo('version'),
			PHP_VERSION,
			get_bloginfo('name'),
			$this->is_ultimate() ? 'Yes' : 'No',
			);
		}
			
	}

	// Instantiate the class.
	Hooks::init();
}