<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

add_action('woocommerce_thankyou', 'wms_show_feedback', 10, 1);
add_action('woocommerce_view_order', 'wms_show_feedback', 10, 1);
function wms_show_feedback( $order_id ) {
	
	global $wpdb;
	wp_enqueue_style( 'wms-front', plugins_url( '/assets/css/wms_front.css', dirname(__FILE__) ), array(), false );
	wp_enqueue_script( 'wms-front', plugins_url( '/assets/js/wms_front.js', dirname(__FILE__) ), array(), false, true );

	?><h2><?php _e('Feedback');?></h2><?php

	if(isset($_POST) && !empty($_POST)) {
		wms_save_feedback();
	}

	// Get comments data
	$strMessages = "SELECT c.*, cm.* FROM {$wpdb->prefix}comments c
					LEFT JOIN {$wpdb->prefix}commentmeta cm ON ( cm.comment_id = c.comment_ID )
					WHERE c.comment_post_ID = {$order_id} AND (c.comment_author LIKE 'WMS%' OR ( cm.meta_key = 'is_customer_note' AND cm.meta_value = '1')) ORDER BY c.comment_ID ASC";
	$arrMessages = $wpdb->get_results($strMessages);
	
	//get subject data
	$wms_subject =  get_post_meta($order_id, '_wms_subject', true);

	$userPic = get_avatar(get_current_user_id());
	$adminPic =  get_avatar(get_option('admin_email'));
	?><form method="POST">
		<table>
			<tbody><?php
				if(isset($wms_subject) && !empty($wms_subject)) {
					?><tr>
						<th class="wms_subject_header" colspan="4"><?php _e('Subject : ');echo $wms_subject; ?></th>
					</tr><?php
				} else {
					?><tr>
						<td colspan="2"><?php _e('Subject');?></td>
						<td colspan="2"><input type="text" name="txtWMSSubject" placeholder="Subject" id="txtWMSSubject"></td>
					</tr><?php
				}
				if(isset($arrMessages) && !empty($arrMessages)) {
					foreach ($arrMessages as $key => $value) {

						$isCustomer = $value->comment_author == 'WMS - Customer'?true:false;
						?><tr>
							<td class="wms_pic"><?php echo  !$isCustomer?$adminPic:'';?></td>
							<td class="wms_admin_says"><?php echo !$isCustomer?$value->comment_content:'';?></td>
							<td><div class="wms_customer_says"><?php echo $isCustomer?$value->comment_content:'';?></div></td>
							<td class="wms_pic"><?php echo $isCustomer?$userPic:'';?></td>
						</tr><?php
					}
				}
			?><tr>
				<td colspan="2"><?php _e('Message');?></td>
				<td colspan="2"><textarea name="taWMSFeedback" id="taWMSFeedback" placeholder="We value your feedback!"></textarea></td>
			</tr>
			<tr>
				<td colspan="4"><input type="submit" onclick=" return submitFeedback();" value="Share"></td>
			</tr>
			</tbody>
		</table>
		<input type="hidden" name="hidOrder" value="<?php echo $order_id;?>"/>
	</form><?php
}

add_filter('comments_clauses', 'wms_remove_wms_comments', 10, 1);
function wms_remove_wms_comments( $clauses ) {
	if(!is_admin()) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_author LIKE 'WMS%'  ";
	}
	return $clauses;
}

function wms_save_feedback() {

	$order_id = $_POST['hidOrder'];

	// save subject
	if(isset($_POST['txtWMSSubject']) && !empty($_POST['txtWMSSubject'])) {
		update_post_meta($order_id, '_wms_subject', sanitize_title($_POST['txtWMSSubject']));
	}

	$time = current_time('mysql');

	if(empty(trim($_POST['taWMSFeedback']))) {
		return;
	}
	$data = array(
	    'comment_content' => $_POST['taWMSFeedback'],
	    'comment_type' => 'order_note',
	    'comment_post_ID' => $order_id,
	    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
	    'comment_author' => 'WMS - Customer',
	    'comment_date' => $time,
	    'comment_approved' => 1,
	    'comment_agent' => 'WooCommerce',
	    'user_id' => get_current_user_id()
	);

	wp_insert_comment($data);
}