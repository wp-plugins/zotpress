<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
	
	
	// Check if user id
	$api_user_id = false;
	if ($_GET['api_user_id'] != "")
		if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1) $api_user_id = htmlentities($_GET['api_user_id']);
		else wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
	else
		wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );

    
    // Access WordPress db
    global $wpdb;
    
    // Ignore user abort
    ignore_user_abort(true);
    set_time_limit(60); // 1 minute (vs. 60*10)
    
    // Include Request Functionality
    require("../request/rss.request.php");
    
    // Include Import Functions
    require("admin.import.functions.php");
    
	$GLOBALS['zp_session'][$api_user_id]['collections']['query_params'] = array();
	$GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
	
	$zp_current_set = 0;
	
	while ( $zp_current_set <= $GLOBALS['zp_session'][$api_user_id]['collections']['last_set'] )
	{
		$zp_continue = zp_get_collections ($wpdb, $api_user_id, $zp_current_set, true);
		//zp_save_collections ($wpdb, $api_user_id, true, true); // might be confusing to users because doesn't import items or subcollections
		$zp_current_set += 50;
	}
	
	$output = "<!DOCTYPE HTML>\n<html>\n<head>";
    $output .= '<script type="text/javascript" src="'. ZOTPRESS_PLUGIN_URL .'js/jquery-1.5.2.min.js"></script>';
	$output .= '<script>
	
	jQuery(document).ready(function() {
		
		jQuery("input").click( function()
		{
			jQuery(this).parent().toggleClass("selected");
		});
		
		jQuery(".zp-Collection").click( function()
		{
			jQuery(this).toggleClass("selected");
			
			if ( jQuery("input", this).is("[checked]") )
				jQuery("input", this).removeAttr("checked");
			else
				jQuery("input", this).attr("checked","checked");
		});
		
	});
	
	</script>';
	$output .= "<style>
	
	body { font: normal 13px/13px 'Arial', sans-serif; }
	
	.zp-Collection { background: #f9f9f9 url('".ZOTPRESS_PLUGIN_URL."images/sprite.png') no-repeat 12px -642px; border-bottom: 2px solid #fff; padding: 12px; color: #555; padding-left: 42px; cursor: pointer; font-family: 'Open Sans', Helvetica, Arial, sans-serif; }
	.zp-Collection.selected { background: #6D798B url('".ZOTPRESS_PLUGIN_URL."images/sprite.png') no-repeat -466px -642px; color: #fff; }
	.zp-Collection .title { float: left; width: 49%; }
	.zp-Collection .meta { float: right; width: 49%; font-size: 11px; text-align: right; }
	p.error { margin: 0; padding: 1em; font-family: 'Open Sans', Helvetica, Arial, sans-serif; }
	
	/* Thanks to http://nicolasgallagher.com/micro-clearfix-hack/ */
	
	.zp-Collection:before,
	.zp-Collection:after {
		content: \"\";
		display: table;
	}
	.zp-Collection:after {
		clear: both;
	}
	
	.zp-Collection {
		*zoom: 1;
	}
	
	</style>\n";
	$output .= "</head><body>\n\n";
	
	// Display
	if ( $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] > 0 )
	{
		$output .= "<div class='zp-Collection-List'>\n";
		for ($i = 0; $i <= ($GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] - 1); $i++ )
		{
			$mod = $i * 7;
			
			$output .= "<div class='zp-Collection' rel='" . $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][4+$mod] . "'>";
			$output .= "<span class='title'>" . $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][1+$mod] . "</span>";
			$output .= "<span class='meta'>" . $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][6+$mod] . " items, ";
			$output .= $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][5+$mod] . " subcollections</span>";
			$output .= "</div>\n";
		}
		$output .= "</div>\n\n";
	}
	else // No collections exist
	{
		$output .= "<p class='error'>Sorry, no collections to display.</p>";
	}
	
	$output .= "</body>\n</html>";
	
	echo $output;
	
	// Unset
	$GLOBALS['zp_session'][$api_user_id]['collections']['query_params'] = array();
	$GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;

?>