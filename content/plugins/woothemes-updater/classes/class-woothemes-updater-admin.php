<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooThemes Updater Admin Class
 *
 * Admin class for the WooThemes Updater.
 *
 * @package WordPress
 * @subpackage WooThemes Updater
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * private $token
 * private $api
 * private $name
 * private $menu_label
 * private $page_slug
 * private $plugin_path
 * private $screens_path
 * private $classes_path
 *
 * private $installed_products
 * private $pending_products
 *
 * - __construct()
 * - register_settings_screen()
 * - settings_screen()
 * - get_activated_products()
 * - get_product_reference_list()
 * - get_detected_products()
 * - get_pending_products()
 * - activate_products()
 * - deactivate_product()
 * - load_updater_instances()
 */
class WooThemes_Updater_Admin {
	private $token;
	private $api;
	private $name;
	private $menu_label;
	private $page_slug;
	private $plugin_path;
	private $plugin_url;
	private $screens_path;
	private $classes_path;
	private $assets_url;
	private $hook;

	private $installed_products;
	private $pending_products;

	/**
	 * Constructor.
	 *
	 * @access  public
	 * @since    1.0.0
	 * @return    void
	 */
	public function __construct ( $file ) {
		$this->token = 'woothemes-updater'; // Don't ever change this, as it will mess with the data stored of which products are activated, etc.

		// Load in the class to use for the admin screens.
		require_once( 'class-woothemes-updater-screen.php' );

		// Load the API.
		require_once( 'class-woothemes-updater-api.php' );
		$this->api = new WooThemes_Updater_API();

		$this->name = __( 'The WooThemes Helper', 'woothemes-updater' );
		$this->menu_label = __( 'WooThemes Helper', 'woothemes-updater' );
		$this->page_slug = 'woothemes-helper';
		$this->plugin_path = trailingslashit( plugin_dir_path( $file ) );
		$this->plugin_url = trailingslashit( plugin_dir_url( $file ) );
		$this->screens_path = trailingslashit( $this->plugin_path . 'screens' );
		$this->classes_path = trailingslashit( $this->plugin_path . 'classes' );
		$this->assets_url = trailingslashit( $this->plugin_url . 'assets' );

		$this->installed_products = array();
		$this->pending_products = array();

		// Load the updaters.
		add_action( 'admin_init', array( $this, 'load_updater_instances' ) ); //$this->load_updater_instances();

		$menu_hook = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action( $menu_hook, array( $this, 'register_settings_screen' ) );

		// Display an admin notice, if there are Woo products, eligible for licenses, that are not activated.
		add_action( 'network_admin_notices', array( $this, 'maybe_display_activation_notice' ) );
		add_action( 'admin_notices', array( $this, 'maybe_display_activation_notice' ) );
		if( is_multisite() && ! is_network_admin() ) remove_action( 'admin_notices', array( $this, 'maybe_display_activation_notice' ) );

		// Process the 'Dismiss' link, if valid.
		add_action( 'admin_init', array( $this, 'maybe_process_dismiss_link' ) );

		add_action( 'admin_footer', array( $this, 'theme_upgrade_form_adjustments' ) );

		add_action( 'woothemes_updater_license_screen_before', array( $this, 'ensure_keys_are_actually_active' ) );

		add_action( 'wp_ajax_woothemes_activate_license_keys', array( $this, 'ajax_process_request' ) );
	} // End __construct()

	/**
	 * If the nonce is valid and the action is "woothemes-helper-dismiss", process the dismissal.
	 * @access  public
	 * @since   1.2.1
	 * @return  void
	 */
	public function maybe_process_dismiss_link () {
		if ( isset( $_GET['action'] ) && ( 'woothemes-helper-dismiss' == $_GET['action'] ) && isset( $_GET['nonce'] ) && check_admin_referer( 'woothemes-helper-dismiss', 'nonce' ) ) {
			update_site_option( 'woothemes_helper_dismiss_activation_notice', true );

			$redirect_url = remove_query_arg( 'action', remove_query_arg( 'nonce', $_SERVER['REQUEST_URI'] ) );

			wp_safe_redirect( $redirect_url );
			exit;
		} else if ( isset( $_GET['woo-dismiss-renewal-notice'] ) && 'true' == $_GET['woo-dismiss-renewal-notice'] ) {
			// Only hide it for 60 days on end, make sure future renewals will get notices as well
			set_transient( 'woo_hide_renewal_notices', 'yes', 60 * DAY_IN_SECONDS );
		}
	} // End maybe_process_dismiss_link()

	/**
	 * Display an admin notice, if there are licenses that are not yet activated.
	 * @access  public
	 * @since   1.2.1
	 * @return  void
	 */
	public function maybe_display_activation_notice () {
		if ( isset( $_GET['page'] ) && 'woothemes-helper' == $_GET['page'] ) return;
		if ( ! current_user_can( 'manage_options' ) ) return; // Don't show the message if the user isn't an administrator.
		if ( is_multisite() && ! is_super_admin() ) return; // Don't show the message if on a multisite and the user isn't a super user.
		$this->maybe_display_renewal_notice();
		if ( true == get_site_option( 'woothemes_helper_dismiss_activation_notice', false ) ) return; // Don't show the message if the user dismissed it.

		$products = $this->get_detected_products();

		$has_inactive_products = false;
		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				if ( isset( $v['product_status'] ) && 'inactive' == $v['product_status'] ) {
					$has_inactive_products = true; // We know we have inactive product licenses, so break out of the loop.
					break;
				}
			}

			if ( $has_inactive_products ) {
				$url = add_query_arg( 'page', 'woothemes-helper', network_admin_url( 'index.php' ) );
				$dismiss_url = add_query_arg( 'action', 'woothemes-helper-dismiss', add_query_arg( 'nonce', wp_create_nonce( 'woothemes-helper-dismiss' ) ) );
				echo '<div class="updated fade"><p class="alignleft">' . sprintf( __( '%sYour WooThemes products are almost ready.%s To get started, %sactivate your product licenses%s.', 'woothemes-updater' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' ) . '</p><p class="alignright"><a href="' . esc_url( $dismiss_url ) . '">' . __( 'Dismiss', 'woothemes-updater' ) . '</a></p><div class="clear"></div></div>' . "\n";
			}
		}
	} // End maybe_display_activation_notice()

	public function maybe_display_renewal_notice() {
		$products = $this->get_detected_products();
		$notices = array();
		$renew_link = add_query_arg( array( 'utm_source' => 'product', 'utm_medium' => 'upsell', 'utm_campaign' => 'licenserenewal' ), 'https://www.woothemes.com/my-account/my-licenses/' );
		foreach ( $products as $file => $product ) {
			if ( isset( $product['license_expiry'] ) && ! in_array( $product['license_expiry'], array( '-', 'Please activate' ) ) ) {
				$date = new DateTime( $product['license_expiry'] );

				if ( current_time( 'timestamp' ) > strtotime( '-60 days', $date->format( 'U' ) ) && current_time( 'timestamp' ) < strtotime( '+4 days', $date->format( 'U' ) ) ) {
					$notices[] = sprintf( __( 'Your license for <strong>%s</strong> expires on %s, %srenew now for a 50%% discount%s.'), $product['product_name'], $date->format( get_option( 'date_format' ) ), '<a href="' . $renew_link . '">', '</a>' );
				} elseif ( current_time( 'timestamp' ) > $date->format( 'U' ) ) {
					$notices[] = sprintf( __( 'Your license for <strong>%s</strong> has expired. Please %srenew now%s to be eligible for future updates and support.'), $product['product_name'], '<a href="' . $renew_link . '">', '</a>' );
				}
			}
		}
		if ( is_array( $notices ) && 0 < count( $notices ) && FALSE == get_transient( 'woo_hide_renewal_notices' ) ) {
			echo '<div class="update-nag"><p class="alignleft">' . implode( '<br/>', $notices ) . '</p><br/><a class="button primary" href="' . add_query_arg( array( 'woo-dismiss-renewal-notice' => 'true' ) ) . '">' . __( 'Don\'t remind me','woothemes-updater' ) . '</a></div>';
		}
	}

	/**
	 * Run a small snippet of JavaScript to highlight the "you will lose all your changes" text on the theme updates screen.
	 * Be sure to add a confirmation dialog box to the "Update Themes" button as well.
	 *
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function theme_upgrade_form_adjustments () {
		global $pagenow;
		if ( 'update-core.php' != $pagenow ) return;
?>
<script type="text/javascript">
/* <![CDATA[ */
if ( jQuery( 'form[name="upgrade-themes"]' ).length ) {
	jQuery( 'form[name=upgrade-themes]' ).prev( 'p' ).wrap( '<div class="error fade"></div>' );

	jQuery( 'form[name=upgrade-themes]' ).find( 'input.button[name=upgrade]' ).click( function ( e ) {
		var response = confirm( '<?php _e( 'Any customizations you have made to theme files will be lost. Are you sure you would like to update?', 'woothemes-updater' ); ?>' );
		if ( false == response ) return false;
	});
}
/*]]>*/
</script>
<?php
	} // End theme_upgrade_form_adjustments()

	/**
	 * Register the admin screen.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function register_settings_screen () {
		$this->hook = add_dashboard_page( $this->name, $this->menu_label, 'manage_options', $this->page_slug, array( $this, 'settings_screen' ) );

		add_action( 'load-' . $this->hook, array( $this, 'process_request' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_scripts-' . $this->hook, array( $this, 'enqueue_scripts' ) );
	} // End register_settings_screen()

	/**
	 * Load the main management screen.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function settings_screen () {
		?>
		<div id="welcome-panel" class="wrap about-wrap woothemes-updater-wrap">
			<h1><?php _e( 'Welcome to WooThemes Helper', 'woothemes-updater' ); ?></h1>

			<div class="about-text woothemes-helper-about-text">
				<?php
					_e( 'Looking for a hand with activating your licenses, or have questions about WooThemes products? We\'ve got you covered.', 'woothemes-updater' );
				?>
			</div>
		</div><!--/#welcome-panel .welcome-panel-->
		<?php

		Woothemes_Updater_Screen::get_header();

		$screen = Woothemes_Updater_Screen::get_current_screen();

		switch ( $screen ) {
			// Help screen.
			case 'help':
				do_action( 'woothemes_updater_help_screen_before' );
				$this->load_help_screen_boxes();
				require_once( $this->screens_path . 'screen-help.php' );
				do_action( 'woothemes_updater_help_screen_after' );
			break;

			// Licenses screen.
			case 'license':
			default:
				if ( $this->api->ping() ) {
					$this->installed_products = $this->get_detected_products();
					$this->pending_products = $this->get_pending_products();

					do_action( 'woothemes_updater_license_screen_before' );
					require_once( $this->screens_path . 'screen-manage.php' );
					do_action( 'woothemes_updater_license_screen_after' );
				} else {
					do_action( 'woothemes_updater_api_unreachable_screen_before' );
					require_once( $this->screens_path . 'woothemes-api-unreachable.php' );
					do_action( 'woothemes_updater_api_unreachable_screen_after' );
				}
			break;
		}

		Woothemes_Updater_Screen::get_footer();
	} // End settings_screen()

	/**
	 * Load the boxes for the "Help" screen.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function load_help_screen_boxes () {
		add_action( 'woothemes_helper_column_left', array( $this, 'display_general_links' ) );
		add_action( 'woothemes_helper_column_left', array( $this, 'display_sensei_links' ) );
		add_action( 'woothemes_helper_column_middle', array( $this, 'display_woocommerce_links' ) );
		add_action( 'woothemes_helper_column_middle', array( $this, 'display_themes_links' ) );
		// add_action( 'woothemes_helper_column_right', array( $this, 'display_panic_button' ) );
	} // End load_help_screen_boxes()

	/**
	 * Display rendered HTML markup containing general support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_general_links () {
		$links = array(
			'http://docs.woothemes.com/' => __( 'Documentation Portal', 'woothemes-updater' ),
			'http://support.woothemes.com/' => __( 'Knowledgebase', 'woothemes-updater' ),
			'http://www.woothemes.com/support-faq/' => __( 'FAQ', 'woothemes-updater' ),
			'http://www.woothemes.com/blog/' => __( 'Blog', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/getting-started.png' ) . '" alt="' . __( 'Getting Started', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Getting Started', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'https://twitter.com/WooThemes/' ) . '" title="' . esc_attr__( 'Follow WooThemes on Twitter', 'woothemes-updater' ) . '">' . __( 'Follow WooThemes on Twitter', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_general_links()

	/**
	 * Display rendered HTML markup containing WooCommerce support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_woocommerce_links () {
		$links = array(
			'http://docs.woothemes.com/documentation/plugins/woocommerce/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/third-party-custom-theme-compatibility/' => __( 'Theme Compatibility', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/product-variations/' => __( 'Product Variations', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/woocommerce-self-service-guide/' => __( 'WooCommerce Self Service Guide', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/woocommerce.png' ) . '" alt="' . __( 'WooCommerce', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'WooCommerce', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woothemes.com/products/woocommerce/?utm_source=helper' ) . '" title="' . esc_attr__( 'Find out more about WooCommerce', 'woothemes-updater' ) . '">' . __( 'Find out more about WooCommerce', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_woocommerce_links()

	/**
	 * Display rendered HTML markup containing Sensei support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_sensei_links () {
		$links = array(
			'http://docs.woothemes.com/document/sensei/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/sensei-theming/' => __( 'Theming Sensei', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/importing-sensei-dummy-data/' => __( 'Import Sensei Dummy Data', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/sensei.png' ) . '" alt="' . __( 'Sensei', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Sensei', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woothemes.com/products/sensei/?utm_source=helper' ) . '" title="' . esc_attr__( 'Find out more about Sensei', 'woothemes-updater' ) . '">' . __( 'Find out more about Sensei', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_sensei_links()

	/**
	 * Display rendered HTML markup containing Themes support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_themes_links () {
		$links = array(
			'http://docs.woothemes.com/documentation/themes/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woothemes.com/document/canvas/' => __( 'Setting up Canvas', 'woothemes-updater' ),
			'http://docs.woothemes.com/documentation/woocodex/' => __( 'WooCodex', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/themes.png' ) . '" alt="' . __( 'Themes', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Themes', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woothemes.com/product-category/themes/?utm_source=helper' ) . '" title="' . esc_attr__( 'Find out more about our Themes', 'woothemes-updater' ) . '">' . __( 'Find out more about our Themes', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_themes_links()

	/**
	 * Display rendered HTML markup containing a panic button.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_panic_button () {
		echo '<div class="panic-button-wrap"><a href="' . esc_url( 'http://www.woothemes.com/contact-us/?utm_source=helper' ) . '" title="' . esc_attr__( 'Help!', 'woothemes-updater' ) . '" class="panic-button" target="_blank">' . '<strong>' . __( 'Panic Button', 'woothemes-updater' ) . '</strong> <em>' . __( 'For when all else fails', 'woothemes-updater' ) . '</em>' . '</a></div>' . "\n";
	} // End display_panic_button()

	/**
	 * Generate the HTML for a given array of links.
	 * @access  private
	 * @since   1.2.0
	 * @param   array  $links Links with the key as the URL and the value as the title.
	 * @return  string Rendered HTML for the links.
	 */
	private function _generate_link_list ( $links = array() ) {
		if ( 0 >= count( $links ) ) return;
		$html = '';
		foreach ( $links as $k => $v ) {
			$html .= '<li><a href="' . esc_url( trailingslashit( $k ) . '?utm_source=helper' ) . '" title="' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a></li>' . "\n";
		}

		return $html;
	} // End _generate_link_list()

	/**
	 * Returns the action value to use.
	 * @access private
	 * @since 1.0.0
	 * @return string|bool Contains the string given in $_POST['action'] or $_GET['action'], or false if none provided
	 */
	private function get_post_or_get_action( $supported_actions ) {
		if ( isset( $_POST['action'] ) && in_array( $_POST['action'], $supported_actions ) )
			return $_POST['action'];

		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], $supported_actions ) )
			return $_GET['action'];

		return false;
	} // End get_post_or_get_action()

	/**
	 * Enqueue admin styles.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function enqueue_styles( $hook ) {
		if ( ! in_array( $hook, array( 'plugins.php', 'update-core.php', $this->hook ) ) ) {
			return;
		}
		wp_enqueue_style( 'woothemes-updater-admin', esc_url( $this->assets_url . 'css/admin.css' ), array(), '1.0.0', 'all' );
	} // End enqueue_styles()

	/**
	 * Enqueue admin scripts.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$screen = get_current_screen();
		wp_enqueue_script( 'post' );
		wp_register_script( 'woothemes-updater-admin', $this->assets_url . 'js/admin.js', array( 'jquery' ) );

		// Only load script and localization on helper admin page.
		if ( in_array( $screen->id, array( 'dashboard_page_woothemes-helper' ) ) ) {
			wp_enqueue_script( 'woothemes-updater-admin' );
			$localization = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'activate_license_nonce' => wp_create_nonce( 'activate-license-keys' )
			);
			wp_localize_script( 'woothemes-updater-admin', 'WTHelper', $localization );
		}
	} // End enqueue_scripts()

	/**
	 * Process the action for the admin screen.
	 * @since  1.0.0
	 * @return  void
	 */
	public function process_request () {
		$notices_hook = is_multisite() ? 'network_admin_notices' : 'admin_notices';
		add_action( $notices_hook, array( $this, 'admin_notices' ) );

		$supported_actions = array( 'activate-products', 'deactivate-product' );

		$action = $this->get_post_or_get_action( $supported_actions );

		if ( $action && in_array( $action, $supported_actions ) ) {

			// should we be here?
			if ( $action == 'activate-products' ) {
				check_admin_referer( 'wt-helper-activate-license', 'wt-helper-nonce' );
			} else {
				check_admin_referer( 'bulk-licenses' );
			}

			$response = false;
			$status = 'false';
			$type = $action;

			switch ( $type ) {
				case 'activate-products':
					$license_keys = array();
					if ( isset( $_POST['license_keys'] ) && 0 < count( $_POST['license_keys'] ) ) {
						foreach ( $_POST['license_keys'] as $k => $v ) {
							if ( '' != $v ) {
								$license_keys[$k] = $v;
							}
						}
					}

					if ( 0 < count( $license_keys ) ) {
						$response = $this->activate_products( $license_keys );
					} else {
						$response = false;
						$type = 'no-license-keys';
					}
				break;

				case 'deactivate-product':
					if ( isset( $_GET['filepath'] ) && ( '' != $_GET['filepath'] ) ) {
						$response = $this->deactivate_product( $_GET['filepath'] );
					}
				break;

				default:
				break;
			}

			if ( $response == true ) {
				$status = 'true';
			}

			wp_safe_redirect( add_query_arg( 'type', urlencode( $type ), add_query_arg( 'status', urlencode( $status ), add_query_arg( 'page', urlencode( $this->page_slug ),  network_admin_url( 'index.php' ) ) ) ) );
			exit;
		}
	} // End process_request()

	/**
	 * Process Ajax license activation requests
	 * @since 1.3.1
	 * @return void
	 */
	public function ajax_process_request() {
		if ( isset( $_POST['security'] ) && wp_verify_nonce( $_POST['security'], 'activate-license-keys' ) && isset( $_POST['license_data'] ) && ! empty( $_POST['license_data'] ) ) {
			$license_keys = array();
			foreach ( $_POST['license_data'] as $license_data ) {
				if ( '' != $license_data['key'] ) {
					$license_keys[ $license_data['name'] ] = $license_data['key'];
				}
			}
			if ( 0 < count( $license_keys ) ) {
				$response = $this->activate_products( $license_keys );
			}
			if ( $response == true ) {
				$request_errors = $this->api->get_error_log();
				if ( 0 >= count( $request_errors ) ) {
					$return = '<div class="updated true fade">' . "\n";
					$return .= wpautop( __( 'Products activated successfully.', 'woothemes-updater' ) );
					$return .= '</div>' . "\n";
					$return_json = array( 'success' => 'true', 'message' => $return, 'url' => add_query_arg( array( 'page' => 'woothemes-helper', 'status' => 'true', 'type' => 'activate-products' ), admin_url( 'index.php' ) ) );
				} else {
					$return = '<div class="error fade">' . "\n";
					$return .= wpautop( __( 'There was an error and not all products were activated.', 'woothemes-updater' ) );
					$return .= '</div>' . "\n";

					$message = '';
					foreach ( $request_errors as $k => $v ) {
						$message .= wpautop( $v );
					}

					$return .= '<div class="error fade">' . "\n";
					$return .= make_clickable( $message );
					$return .= '</div>' . "\n";

					$return_json = array( 'success' => 'false', 'message' => $return );

					// Clear the error log.
					$this->api->clear_error_log();
				}
			} else {
				$return = '<div class="error fade">' . "\n";
				$return .= wpautop( __( 'No license keys were specified for activation.', 'woothemes-updater' ) );
				$return .= '</div>' . "\n";
				$return_json = array( 'success' => 'false', 'message' => $return );
			}
			echo json_encode( $return_json );
		}
		die();
	}

	/**
	 * Display admin notices.
	 * @since  1.0.0
	 * @return  void
	 */
	public function admin_notices () {
		$message = '';
		$response = '';

		if ( isset( $_GET['status'] ) && in_array( $_GET['status'], array( 'true', 'false' ) ) && isset( $_GET['type'] ) ) {
			$classes = array( 'true' => 'updated', 'false' => 'error' );

			$request_errors = $this->api->get_error_log();

			switch ( $_GET['type'] ) {
				case 'no-license-keys':
					$message = __( 'No license keys were specified for activation.', 'woothemes-updater' );
				break;

				case 'deactivate-product':
					if ( 'true' == $_GET['status'] && ( 0 >= count( $request_errors ) ) ) {
						$message = __( 'Product deactivated successfully.', 'woothemes-updater' );
					} else {
						$message = __( 'There was an error while deactivating the product.', 'woothemes-updater' );
					}
				break;

				default:

					if ( 'true' == $_GET['status'] && ( 0 >= count( $request_errors ) ) ) {
						$message = __( 'Products activated successfully.', 'woothemes-updater' );
					} else {
						$message = __( 'There was an error and not all products were activated.', 'woothemes-updater' );
					}
				break;
			}

			$response = '<div class="' . esc_attr( $classes[$_GET['status']] ) . ' fade">' . "\n";
			$response .= wpautop( $message );
			$response .= '</div>' . "\n";

			// Cater for API request error logs.
			if ( is_array( $request_errors ) && ( 0 < count( $request_errors ) ) ) {
				$message = '';

				foreach ( $request_errors as $k => $v ) {
					$message .= wpautop( $v );
				}

				$response .= '<div class="error fade">' . "\n";
				$response .= make_clickable( $message );
				$response .= '</div>' . "\n";

				// Clear the error log.
				$this->api->clear_error_log();
			}

			if ( '' != $response ) {
				echo $response;
			}
		}
	} // End admin_notices()

	/**
	 * Detect which products have been activated.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	protected function get_activated_products () {
		$response = array();

		$response = get_option( $this->token . '-activated', array() );

		if ( ! is_array( $response ) ) $response = array();

		return $response;
	} // End get_activated_products()

	/**
	 * Get a list of products from WooThemes.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	protected function get_product_reference_list () {
		global $woothemes_updater;
		$response = array();
		$response = $woothemes_updater->get_products();
		return $response;
	} // End get_product_reference_list()

	/**
	 * Get a list of WooThemes products found on this installation.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	protected function get_detected_products () {
		$response = array();
		$products = get_plugins();

		$themes = wp_get_themes();
		if ( 0 < count( $themes ) ) {
			foreach ( $themes as $k => $v ) {
				$filepath = basename( $v->__get( 'stylesheet_dir' ) ) . '/style.css';
				$products[$filepath] = array( 'Name' => $v->__get( 'name' ), 'Version' => $v->__get( 'version' ) );
			}
		}

		if ( is_array( $products ) && ( 0 < count( $products ) ) ) {
			$reference_list = $this->get_product_reference_list();
			$activated_products = $this->get_activated_products();
			if ( is_array( $reference_list ) && ( 0 < count( $reference_list ) ) ) {
				foreach ( $products as $k => $v ) {
					if ( in_array( $k, array_keys( $reference_list ) ) ) {
						$status = 'inactive';
						$license_expiry = __( 'Please activate', 'woothemes-updater' );
						if ( in_array( $k, array_keys( $activated_products ) ) ) {
							$status = 'active';
							if ( isset( $activated_products[ $k ][3] ) ) {
								$license_expiry = $activated_products[ $k ][3];
							} else {
								$license_expiry = '-';
							}
						}
						$response[$k] = array( 'product_name' => $v['Name'], 'product_version' => $v['Version'], 'file_id' => $reference_list[$k]['file_id'], 'product_id' => $reference_list[$k]['product_id'], 'product_status' => $status, 'product_file_path' => $k, 'license_expiry' => $license_expiry );
					}
				}
			}
		}

		return $response;
	} // End get_detected_products()

	/**
	 * Get an array of products that haven't yet been activated.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return  array Products awaiting activation.
	 */
	protected function get_pending_products () {
		$response = array();

		$products = $this->installed_products;

		if ( is_array( $products ) && ( 0 < count( $products ) ) ) {
			$activated_products = $this->get_activated_products();

			if ( is_array( $activated_products ) && ( 0 <= count( $activated_products ) ) ) {
				foreach ( $products as $k => $v ) {
					if ( isset( $v['product_key']) && ! in_array( $v['product_key'], $activated_products ) ) {
						$response[$k] = array( 'product_name' => $v['product_name'] );
					}
				}
			}
		}

		return $response;
	} // End get_pending_products()

	/**
	 * Activate a given array of products.
	 *
	 * @since    1.0.0
	 * @param    array   $products  Array of products ( filepath => key )
	 * @return boolean
	 */
	protected function activate_products ( $products ) {
		$response = false;
		if ( ! is_array( $products ) || ( 0 >= count( $products ) ) ) { return false; } // Get out if we have incorrect data.

		$key = $this->token . '-activated';
		$has_update = false;
		$already_active = $this->get_activated_products();
		$product_keys = $this->get_product_reference_list();

		foreach ( $products as $k => $v ) {
			if ( ! in_array( $v, $product_keys ) ) {

				// Perform API "activation" request.
				$activate = $this->api->activate( $products[$k], $product_keys[$k]['product_id'], $k );

				if ( ! ( ! $activate ) ) {
					// key: base file, 0: product id, 1: file_id, 2: hashed license, 3: expiry date
					$already_active[$k] = array( $product_keys[$k]['product_id'], $product_keys[$k]['file_id'], md5( $products[$k] ), $activate->expiry_date );
					$has_update = true;
				}
			}
		}

		// Store the error log.
		$this->api->store_error_log();

		if ( $has_update ) {
			$response = update_option( $key, $already_active );
		} else {
			$response = true; // We got through successfully, and the supplied keys are already active.
		}

		// Lets Clear the updates transient
		delete_transient( 'woothemes_helper_updates' );

		return $response;
	} // End activate_products()

	/**
	 * Deactivate a given product key.
	 * @since    1.0.0
	 * @param    string $filename File name of the to deactivate plugin licence
	 * @param    bool $local_only Deactivate the product locally without pinging WooThemes.com.
	 * @return   boolean          Whether or not the deactivation was successful.
	 */
	public function deactivate_product ( $filename, $local_only = false ) {
		$response = false;
		$already_active = $this->get_activated_products();

		if ( 0 < count( $already_active ) ) {
			$deactivated = true;

			if ( isset( $already_active[ $filename ][0] ) ) {
				$key = $already_active[ $filename ][2];

				if ( false == $local_only ) {
					$deactivated = $this->api->deactivate( $key );
				}
			}

			if ( $deactivated ) {
				unset( $already_active[ $filename ] );
				$response = update_option( $this->token . '-activated', $already_active );
			} else {
				$this->api->store_error_log();
			}
		}

		return $response;
	} // End deactivate_product()

	/**
	 * Load an instance of the updater class for each activated WooThemes Product.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_updater_instances () {
		$products = $this->get_detected_products();
		$activated_products = $this->get_activated_products();
		$themes = array();
		$plugins = array();
		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				if ( isset( $v['product_id'] ) && isset( $v['file_id'] ) ) {
					$license_hash = isset( $activated_products[ $k ][2] ) ? $activated_products[ $k ][2] : '';

					// If it's a theme, add it to the themes list, otherwise add it to the plugins list
					if ( strpos( $k, 'style.css' ) ) {
						$themes[] = array( $k, $v['product_id'], $v['file_id'], $license_hash, $v['product_version'] );
					} else {
						$plugins[] = array( $k, $v['product_id'], $v['file_id'], $license_hash, $v['product_version'] );
					}
				}
			}
			require_once( 'class-woothemes-updater-update-checker.php' );
			new WooThemes_Updater_Update_Checker( $plugins, $themes );
		}
	} // End load_updater_instances()

	/**
	 * Run checks against the API to ensure the product keys are actually active on WooThemes.com. If not, deactivate them locally as well.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function ensure_keys_are_actually_active () {
		$products = (array)$this->get_activated_products();
		$call_data = array();

		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				$call_data[ $k ] = array( $v[0], $v[1], $v[2], esc_url( home_url( '/' ) ) );
			}
		}

		if ( empty( $call_data ) ) {
			return;
		}

		$statuses = $this->api->product_active_statuses_check( $call_data );
		if ( ! $statuses ) {
			return;
		}

		foreach ( $statuses as $k => $v ) {
			if ( isset( $v->deactivate ) ) {
				$this->deactivate_product( $k, true );
			}
		}
	} // End ensure_keys_are_actually_active()
} // End Class
?>