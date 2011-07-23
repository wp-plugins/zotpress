<?php

	// Include WordPress
	require('../../../wp-load.php');

	define('WP_USE_THEMES', false);
	
	
	
	// SET UP VARS
	
	// zp_instance_id
	if (isset($_GET['zp_instance_id']) && trim($_GET['zp_instance_id']) != "") {
		$zp_instance_id = trim(urldecode($_GET['zp_instance_id']));
	}
	else {
		$zp_instance_id = false;
	}
	
	// zp_return_url
	if (isset($_GET['zp_return_url']) && trim($_GET['zp_return_url']) != "") {
		$zp_return_url = trim(urldecode($_GET['zp_return_url']));
	}
	else {
		$zp_return_url = get_bloginfo("url");
	}
	
	
	
	// DELETE CACHE FOR THIS INSTANCE ID
	
	if ($zp_instance_id !== false)
	{
		// Access Wordpress db
		global $wpdb;
		
		$zp_account = $wpdb->get_results( "DELETE FROM ".$wpdb->prefix."zotpress_cache WHERE instance_id='".$zp_instance_id."'", OBJECT );
		
		header("Refresh: 0; url=".$zp_return_url);
		exit;
	}
	else
	{
		header("Refresh: 0; url=".$zp_return_url);
		exit;
	}
?>