<?php
/**
 * Controls all the switch actions for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Switches' ) ) {
	/**
	 * Controls all the switch actions for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Switches extends \WP_Dark_Mode\Ultimate\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Actions
		 *
		 * @since 5.0.0
		 */
		public function actions() {

			// Bail, if ultimate is not active.
			if ( ! $this->is_ultimate() ) {
				return;
			}

			// Menu switch.
			add_action( 'admin_head-nav-menus.php', array( $this, 'add_dark_mode_switch_meta_box' ) );

			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'edit_dark_mode_switch_item' ), 10, 5 );

			add_action('wp_update_nav_menu_item', array( $this, 'save_custom_field_for_menu_item' ), 10, 3);

			add_filter ('wp_setup_nav_menu_item', array( $this, 'add_menu_style_data_to_menu' ), 0);
		}

		/**
		 * Filters
		 *
		 * @since 5.0.0
		 */
		public function filters() {

			// Bail, if ultimate is not active.
			if ( ! $this->is_ultimate() ) {
				return;
			}

			// frontend menu walker
			add_filter( 'walker_nav_menu_start_el', array( $this, 'frontend_nav_menu_args' ), 10, 4 );
		}

		/**
		 * Menu switch
		 *
		 * @since 5.0.0
		 */
		public function add_dark_mode_switch_meta_box() {
			// Add meta box.

			add_meta_box(
				'wp_dark_mode_nav_link',
				__( 'Dark Mode Switcher', 'wp-dark-mode' ),
				[ $this, 'render_menu_switch' ],
				'nav-menus',
				'side',
				'low'
			);

		}

		public function edit_dark_mode_switch_item( $item_id = null, $menu_item = null, $depth = null, $args = null, $current_object_id = null ) {

			// Bail, if menu_item, item_id is null.
			if ( ! $menu_item || ! $item_id ) {
				return;
			}

			// if object is not wp-dark-mode-switch, then return
			if ( $menu_item->object !== 'wp-dark-mode-switch' ) {
				return;
			}

			$style_id = get_post_meta( $item_id, 'dark_mode_switch_id', true );
			$style_id = $style_id ? intval($style_id) : 1; 

			?>
			<div class="wp-dark-mode-switches-panel">
				<label for="" class="label">Switch Style</label>
				<div class="wp-dark-mode-switches cols-3">
					<?php
					foreach ( [1, 2, 3, 23, 24, 22, 20, 21, ...range( 4, 13)] as $i ) {
						echo wp_sprintf( '<div class="wp-dark-mode-switches-item wp-dark-mode-menu-switch-admin dummy %s" data-style="%s">', 
							$style_id === $i ? 'active' : '', 
							esc_attr( $i ) 
						);
						echo '<img src="' . esc_url( WP_DARK_MODE_URL . 'assets/images/switches/switch-' . $i . '.svg' ) . '" alt="Switch Style ' . $i . '" />';
						if ( 22 === $i ) {
							echo '<span class="wp-dark-mode-switches-item-new">New</span>';
						}
						echo '</div>';
					}
					?>
				</div>

				<input type="hidden" class="menu-item-style" name="menu-item[<?php echo esc_attr( $item_id ); ?>][menu-item-style]"
					value="<?php echo esc_attr( $style_id ); ?>" />
			</div>

		<?php
		}

		/**
		 * Render menu switch
		 *
		 * @since 5.0.0
		 */
		public function render_menu_switch() {

			?>
			<div id="posttype-wp-dark-mode-switcher" class="posttypediv">
				<div id="tabs-panel-darkmode-switcher-endpoints" class="tabs-panel tabs-panel-active">
					<ul id="darkmode-switcher-endpoints-checklist" class="categorychecklist form-no-clear">
						<li>
							<?php // Bail if menu_switch is disabled.
			if ( ! $this->get_option( 'menu_switch_enabled' ) ) {
				?>
				<p>
				Dark Mode for Menu Switch is disabled. <a href="<?php echo esc_url( admin_url( 'admin.php?page=wp-dark-mode#/switch?tab=menu' ) ); ?>">Enable it from here.</a>

				</p>				
				<?php 
			} ?>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" checked
									value="-1" />
								<?php esc_html_e( 'Dark Mode Switcher', 'wp-dark-mode' ); ?>
							</label>
							<input type="hidden" class="menu-item-object" name="menu-item[-1][menu-item-object]"
								value="wp-dark-mode-switch" />
							<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]"
								value="wp-dark-mode-switch" />
							<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]"
								value="DarkMode Switch" />
							<input type="hidden" class="menu-item-description" name="menu-item[-1][menu-item-description]"
								value="WP Dark Mode Switch" />
							<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-style]" value="1" />
						</li>
					</ul>
				</div>
				<p class="button-controls">
					<span class="add-to-menu">
						<button type="submit" class="button-secondary submit-add-to-menu right" name="add-post-type-menu-item"
							id="submit-posttype-wp-dark-mode-switcher">
							<?php esc_attr_e( 'Add to Menu', 'wp-dark-mode' ); ?>
						</button>
						<span class="spinner"></span>
					</span>
				</p>
			</div>
			<?php
		}


		/**
		 * Save custom field for menu item
		 *
		 * @since 5.0.0
		 */
		public function save_custom_field_for_menu_item( $menu_id = null, $menu_item_db_id = null, $args = null ) {

			if( ! $args || empty( $args ) ) return;

			if( $args['menu-item-object'] !== 'wp-dark-mode-switch' ) return;
 
			$menu_style = isset( $_REQUEST['menu-item'][ $menu_item_db_id ]['menu-item-style'] ) ? intval( $_REQUEST['menu-item'][ $menu_item_db_id ]['menu-item-style'] ) : 1;
 
			update_post_meta( $menu_item_db_id, 'dark_mode_switch_id', $menu_style );
		}
 

		/**
		 * Frontend nav menu args
		 *
		 * @param array $args Menu args.
		 * @param array $menu Menu.
		 * @since 5.0.0
		 */
		public function frontend_nav_menu_args( $item_output, $menu_item, $depth, $args ) {

			// Bail, if menu_item['menu-item-object'] is not wp-dark-mode-switch.
			if ( $menu_item->object !== 'wp-dark-mode-switch' ) {
				return $item_output;
			}

			// Bail if menu_switch is disabled.
			if ( ! $this->get_option( 'menu_switch_enabled' ) ) {
				return false;
			}

			$style_id = get_post_meta( $menu_item->ID, 'dark_mode_switch_id', true );
  
			return do_shortcode( wp_sprintf( '[wp-dark-mode-switch style="%s" size=".7"]', esc_attr( $style_id ) ) );
		}

		/**
		 * Add custom megamenu fields data to the menu.
		 *
		 * @access public
		 * @param object $menu_item A single menu item.
		 * @return object The menu item.
		 */
		public function add_menu_style_data_to_menu( $menu_item ) {

			// Make it object if it's not.
			$menu_item = (object) $menu_item;

			return $menu_item;
		}

	}

	// Initialize the class.
	Switches::init();
}