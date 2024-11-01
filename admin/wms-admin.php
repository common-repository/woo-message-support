<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

add_filter( 'manage_edit-shop_order_columns', 'wms_woo_message_column_header' );
function wms_woo_message_column_header($columns){
    $new_columns = (is_array($columns)) ? $columns : array();
    unset( $new_columns['order_actions'] );

    $new_columns['woo_message_support'] = __('Message', 'woo-message-support');
    $new_columns['order_actions'] = $columns['order_actions'];
    return $new_columns;
}

function wms_load_css() {
        wp_register_style( 'wms_admin_css', plugins_url( '/assets/css/wms_admin.css', dirname(__FILE__) ), array(), false );
        wp_enqueue_style( 'wms_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'wms_load_css' );

/*
 * This function will mark read / unread messages in grid view of column.
 * Author: Vidish Purohit
 */
add_action( 'manage_shop_order_posts_custom_column', 'wms_woo_message_column_content', 2 );
function wms_woo_message_column_content($column){
    global $post, $wpdb;
    
    if ( $column == 'woo_message_support' ) {    
        
        $strData = "SELECT comment_author, comment_content FROM {$wpdb->prefix}comments WHERE comment_post_ID = '" . (int)$post->ID . "' ORDER BY comment_ID DESC LIMIT 1";
        $arrData = $wpdb->get_results($strData);

        if(isset($arrData) && !empty($arrData)) {
	    	
            $isUnread = strpos($arrData[0]->comment_author, "WMS - ") === false?false:true;
            if($isUnread) {
                ?><a href="<?php echo admin_url('post.php?post=' . $post->ID . '&action=edit') ;?>"><div class="letter" title="<?php _e("Customer says : ") . print($arrData[0]->comment_content);?>">&nbsp;</div></a><?php
            } else {
                ?><a href="<?php echo admin_url('post.php?post=' . $post->ID . '&action=edit') ;?>"><div class="readletter" title="<?php _e("Customer says : ") . print($arrData[0]->comment_content);?>">&nbsp;</div></a><?php
            }
        } else {
            echo '-';
        }
        
    }
}