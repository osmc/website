<?php
/**
 * Plugin Name: Weight Based Shipping for Woocommerce
 * Plugin URI: http://wordpress.org/plugins/weight-based-shipping-for-woocommerce/
 * Description: Simple yet flexible weight based shipping method for Woocommerce.
 * Version: 2.6.8
 * Author: dangoodman
 * Text Domain: woowbs
 * Domain Path: /lang/
 */

    require_once(dirname(__FILE__).'/WBS_Loader.php');
    WBS_Loader::loadWbs(__FILE__);
?>