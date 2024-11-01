<?php
/*
 * Plugin Name: Woo Message Support
 * Author: WooExtend
 * Author URI: https://www.wooextend.com
 * Version: 2.0
 * Requires at least: 4.0
 * Tested up to: 4.9
 * Description: Woo Message Support allows administrator to communicate to customers in most efficient way.
 */
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
    require_once ('admin/wms-admin.php');
    require_once ('front-end/wms-message.php');
    
}

?>