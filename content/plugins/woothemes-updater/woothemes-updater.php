<?php
/**
 * Plugin Name: WooThemes Helper
 * Plugin URI: http://woothemes.com/products/
 * Description: Hi there. I'm here to help you manage licenses for your WooThemes products, as well as help out when you need a guiding hand.
 * Version: 1.5.9
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Network: true
 * Requires at least: 3.8.1
 * Tested up to: 4.1.0
 *
 * Text Domain: woothemes-updater
 * Domain Path: /languages/
 */
/*
    Copyright 2012  WooThemes  (email : info@woothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_admin() ) {
	require_once( 'classes/class-woothemes-updater.php' );

	global $woothemes_updater;
	$woothemes_updater = new WooThemes_Updater( __FILE__, '1.5.9' );
}
?>
