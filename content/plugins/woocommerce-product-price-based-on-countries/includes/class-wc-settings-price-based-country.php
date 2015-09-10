
<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Price_Based_Country' ) ) :

/**
 * WC_Settings_Price_Based_Country
 *
 * WooCommerce Price Based Country settings page
 *
 * @class 		WC_Settings_Price_Based_Country
 * @version		1.3.4
 * @author 		oscargare
 */
class WC_Settings_Price_Based_Country extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'price_based_country';
		$this->label = __( 'Price Based on Country', 'wc-price-based-country' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_admin_field_country_groups', array( $this, 'country_groups_table' ) );		
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );						
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {					

		$sections = array(
			''         => __( 'Price Based on Country Options', 'wc-price-based-country' )
		);

		foreach ( get_option( 'wc_price_based_country_regions', array() ) as $key => $country_group ) {
			$sections[$key] = $country_group['name'];
		}		
		
		return $sections;
	}

	/**
	 * Display donate notices
	 */
	function donate_notice() {

		if ( get_option('wc_price_based_country_hide_ads', 'no') == 'no' ) {

			global $pagenow;		
		
			if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] == 'price_based_country' ) {
				?>
				<div class="updated">
					<p><strong>Donate to Price Based Country</strong></p>
					<p><?php _e('It is difficult to provide, support, and maintain free software. Every little bit helps is greatly appreciated!','wc-price-based-country') ; ?></p>
					<p class="submit">
						<a class="button-primary" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NG75SHRLAX28L"><?php _e( 'Donate now', 'woocommerce' ); ?></a>
						<a class="skip button-secondary" href="<?php echo esc_url( add_query_arg( 'wc_price_based_country_donate_hide', 'true', admin_url( 'admin.php?page=wc-settings&tab=price_based_country' ) ) ); ?>">Don't show me again</a>
					</p>
		   		</div>
				<?php							
			}
		}		
	}

	/**
	 * Display donate notice and after display sections
	 */
	public function output_sections() {
		$this->donate_notice();
		parent::output_sections();		
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {						
			
		return array(			
		
			array( 
				'title' => __( 'Pricing groups', 'wc-price-based-country' ), 
				'type' => 'title', 
				'desc' => 'Pricing groups are listed below. Add a group for each price you need to add to products and include the countries for which this price will be displayed. For deleted a group, check "Delete" and save changes', 
				'id' => 'price_based_country_groups'
			),

			array( 'type' => 'country_groups' ),				

			array( 'type' => 'sectionend', 'id' => 'price_based_country_groups' ),				

			array( 
				'title' => __( 'Test Mode', 'wc-price-based-country' ), 
				'type' => 'title', 
				'desc' => 'If you want to check that prices are shown successfully, enable test mode and enter the Country which you want to do the test.', 
				'id' => 'price_based_country_test'
			),

			array(
				'title' => __( 'Enabled/Disabled', 'wc-price-based-country' ),
				'desc' 		=> __( 'Enabled Test Mode', 'wc-price-based-country' ),
				'id' 		=> 'wc_price_based_country_test_mode',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'desc_tip'	=> __('If test mode is enabled, a demo store notice will be displayed .')
			),

			array(
				'title' => __( 'Test country', 'wc-price-based-country' ),					
				'id' 		=> 'wc_price_based_country_test_country',				
				'default'	=> wc_get_base_location(),
				'type' 		=> 'select',
				'class'		=> 'chosen_select',
				'options'	=>	WC()->countries->countries
			),
			
			array( 'type' => 'sectionend', 'id' => 'price_based_country_test' )

		);			
	
	}

	/**
	 * Output country groups table.
	 *
	 * @access public
	 * @return void
	 */
	public function country_groups_table() {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Groups', 'wc-price-based-country' ) ?></th>
		    <td class="forminp">
				<table class="widefat" cellspacing="0">
					<thead>
						<tr>
							<th style="width:5px;"></th>							
							<th><?php _e( 'Group Name', 'wc-price-based-country' ) ?></th>
							<th><?php _e( 'Countries', 'wc-price-based-country' ) ?></th>
							<th><?php _e( 'Currency', 'woocommerce' ); ?></th>							
							<th style="width:120px;"></th>
							<th style="width:80px;"><?php _e( 'Delete', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>						
						<?php

							$currencies = get_woocommerce_currencies();							
							$base_currency = get_option( 'woocommerce_currency' );						

							echo '<tr><td></td>';								
							echo '<td>' . __('Default Zone', 'wc-price-based-country') . '</td>';
							echo '<td>' . __('All countries not are included in other zones', 'wc-price-based-country') . '</td>';							
							echo '<td>' . $currencies[ $base_currency ] . '(' . get_woocommerce_currency_symbol( $base_currency ) . ')<br /> <span class="description">Default</span></td>';
							echo '<td></td></tr>';

							foreach ( get_option( 'wc_price_based_country_regions', array() ) as $key => $region) {

								echo '<tr id="' . $key . '">';							

								echo '<td></td>';

								echo '<td>' . $region['name'] . '</td>';

								echo '<td>';

								$country_display = array();
								
								foreach( $region['countries'] as $iso_code ) {
									$country_display[] = WC()->countries->countries[$iso_code];										
								}

								echo  implode($country_display, ', ');

								echo '</td>';

								echo '<td>';
								echo $currencies[$region['currency']] . ' (' . get_woocommerce_currency_symbol($region['currency']) . ') <br />';
								echo '<span class="description">1 ' . $base_currency .' = ' . wc_format_localized_decimal( $region['exchange_rate'] ) . ' ' . $region['currency'] . '</span>';
								echo '</td>';								

								echo '<td>';
								echo '<a class="button" href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . $key) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';								
								echo '</td>';

								echo '<td style="padding:15px 10px;"><input type="checkbox" value="' . $key . '" name="delete_group[]" /></td>';

								echo '</tr>';							
							}													
							
						?>					
					</tbody>
					<tfoot>
						<tr>							
							<th style="width:5px;"></th>
							<th colspan="5">
								<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=new_group') ?>" class="button">+ Add group</a>								
							</th>							
						</tr>						
					</tfoot>
				</table>
			</td>
		</tr>
		<?php					

	}

	/**
	 * Output section.
	 *
	 * @access public
	 * @return void
	 */
	public function section_settings( $not_available_countries, $group = array() ) {

		if ( ! isset( $group['name'] ) ) $group['name'] = '';
		if ( ! isset( $group['countries'] ) ) $group['countries'] = array();
		if ( ! isset( $group['currency'] ) ) $group['currency'] = get_option('woocommerce_currency');
		if ( ! isset( $group['empty_price_method'] ) ) $group['empty_price_method'] = '';
		if ( ! isset( $group['exchange_rate'] ) ) $group['exchange_rate'] = '1';

		?>
		<h3><?php echo $group['name'] ? esc_html( $group['name'] ) : __( 'Add Group', 'wc-price-based-country' ); ?></h3>
		<table class="form-table">

			<!-- Region name -->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="group_name"><?php _e( 'Region Name', 'wc-price-based-country' ); ?></label>
					<?php //echo $tip; ?>
				</th>
                <td class="forminp forminp-text">
                	<input name="group_name" id="group_name" type="text" value="<?php echo esc_attr( $group['name'] ); ?>"/> 
                	<?php //echo $description; ?>
                </td>
			</tr>

			<!-- Country multiselect -->			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="group_countries"><?php _e( 'Countries', 'wc-price-based-country' ); ?></label>
				</th>
				<td class="forminp">
					<select multiple="multiple" name="group_countries[]" style="width:350px" data-placeholder="<?php _e( 'Choose countries&hellip;', 'woocommerce' ); ?>" title="Country" class="chosen_select">
						<?php 	
							
							$countries = WC()->countries->countries;							

							asort( $countries );
							
		        			foreach ( $countries as $key => $val ) {
		        				if ( ! in_array( $key, $not_available_countries ) ) {
                					echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $group['countries'] ), true, false ).'>' . $val . '</option>';
                				}
                			}
						?>
					</select>
					<!-- <a class="select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a> -->
				</td>
			</tr>

			<!-- Currency select -->			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="group_currency"><?php _e( 'Currency', 'woocommerce' ); ?></label>
					<?php //echo $tip; ?>
				</th>
				<td class="forminp forminp-select">
					<select name="group_currency" id="group_currency" class="chosen_select">
						<?php
							foreach ( get_woocommerce_currencies() as $code => $name ) {
								echo '<option value="' . esc_attr( $code ) . '" ' . selected( $group['currency'], $code ) .'>' . $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')' . '</option>';
							}
						?>
					</select>
				</td>
			</tr>
			

			<!-- Exchange rate -->			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="exchange_rate"><?php _e( 'Exchange Rate', 'wc-price-based-country' ); ?></label>
					<img class="help_tip" data-tip="<?php echo esc_attr( __( "For each product, if select autocalculate, product's price will be the result of multiplying the default price by this exchange rate.", 'wc-price-based-country' ) ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
				</th>
                <td class="forminp forminp-text">                	
                	1 <?php echo get_option( 'woocommerce_currency' );	?> = <input name="exchange_rate" id="exchange_rate" type="text" class="short wc_input_decimal" value="<?php echo wc_format_localized_decimal( $group['exchange_rate'] ); ?>"/> 
                	<?php //echo $description; ?>
                </td>
			</tr>

		</table>

		<?php				

	}

	/**
	 * Output the settings
	 */
	public function output() {
		global $current_section;		

		if ( $current_section ) {			

			$base_country = wc_get_base_location();			
			
			$not_available_countries = array();
			
			$regions = get_option( 'wc_price_based_country_regions', array() );

			foreach ( $regions  as $key => $value ) {					

				foreach ( $value['countries'] as $code ) {

					if ( $current_section !== $key ) $not_available_countries[] = $code;							
				}
			}					

			if ( $current_section == 'new_group' ) {
				
				$this->section_settings( $not_available_countries );

			} else {			
				
				if ( isset( $regions[$current_section] ) ) {								

					$this->section_settings( $not_available_countries, $regions[$current_section] );
				}					
			}								

		} else {
			parent::output();			
		}			
	}	

	/**
	 * Save section settings
	 */
	public function section_save() {

		global $current_section;

		$save = false;

		if ( ! $_POST['group_name'] ) {

			WC_Admin_Settings::add_error( __( 'Group name is required.', 'wc-price-based-country' ) );

		} elseif ( ! isset( $_POST['group_countries'] ) ) {

			WC_Admin_Settings::add_error( __( 'Add at least one country to the list.', 'wc-price-based-country' ) );

		} elseif ( empty( $_POST['exchange_rate'] ) ||  wc_format_decimal( $_POST['exchange_rate'] ) == 0 ) {
			
			WC_Admin_Settings::add_error( __( 'Exchange rate must be nonzero.', 'wc-price-based-country' ) );			

		} else {

			$section_settings = get_option( 'wc_price_based_country_regions', array() );

			$key = ( $current_section == 'new_group' ) ? sanitize_title( $_POST['group_name'] ) : $current_section;
			
			$section_settings[$key]['name'] = $_POST['group_name'];
			$section_settings[$key]['countries'] = $_POST['group_countries'];
			$section_settings[$key]['currency'] = $_POST['group_currency'];			
			$section_settings[$key]['exchange_rate'] = wc_format_decimal( $_POST['exchange_rate'] );
			
			update_option( 'wc_price_based_country_regions', $section_settings );
			
			if ( $current_section == 'new_group' ) {
				$current_section = $key ;					
			}

			$save = true;
			
		}

		return $save;

	}

	/**
	 * Save global settings 
	 */
	public function save() {
		
		global $current_section;
		
		if ( $current_section ) {
			
			if (  $this->section_save() ) {
				update_option( 'wc_price_based_country_timestamp', time() );
			}			

		} else {
													
			if ( isset( $_POST['delete_group'] ) ) {
				
				$section_settings = WCPBC()->get_regions();
				
				global $wpdb;

				foreach ( $_POST['delete_group'] as $region_key ) {

					unset( $section_settings[$region_key] );

					foreach ( array('_price', '_regular_price', '_sale_price', '_price_method') as $price_type ) {
						
						foreach ( array('', '_variable') as $variable) {
							
							$meta_key = '_' . $region_key . $variable . $price_type;

							$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $meta_key ) );			
						}	

						if ( $price_type !== '_price_method') {

							foreach ( array('_min', '_max' ) as $min_or_max ) {

								$meta_key = '_' . $region_key . $min_or_max . $price_type . '_variation_id';

								$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $meta_key ) );			
							}		
						}		
					}					
				}

				update_option( 'wc_price_based_country_regions', $section_settings );							

			}						

			//save settings				
			$settings = $this->get_settings();
			WC_Admin_Settings::save_fields( $settings );							

			update_option( 'wc_price_based_country_timestamp', time() );			
		}		
	}
	
}

endif;

return new WC_Settings_Price_Based_Country();

?>