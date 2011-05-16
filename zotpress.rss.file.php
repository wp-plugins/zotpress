<?php

	// Include WordPress
	require('../../../wp-load.php');

	define('WP_USE_THEMES', false);

	// Include Special cURL
	require('zotpress.rss.curl.php');
	
	$zp_xml = false;
	
	
	
	// SET UP VARS
	
	// Download URL
	if (isset($_GET['download_url']) && trim($_GET['download_url']) != "") {
		$zp_download_url = trim(urldecode($_GET['download_url']));
	}
	else {
		$zp_xml = "No download URL provided.";
	}
	
	// Account Type
	if (isset($_GET['account_type']) && trim($_GET['account_type']) != "") {
		$zp_account_type = trim(urldecode($_GET['account_type']));
	}
	else {
		$zp_xml = "No account type provided.";
	}
	
	// Api User ID
	if (isset($_GET['api_user_id']) && trim($_GET['api_user_id']) != "") {
		$zp_api_user_id = trim(urldecode($_GET['api_user_id']));
	}
	else {
		$zp_xml = "No API User ID provided.";
	}
	
	
	
	// GET KEY FROM DB
	
	if ($zp_xml === false)
	{
		// Access Wordpress db
		global $wpdb;
		
		$zp_account = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."zotpress WHERE account_type='".$zp_account_type."' AND api_user_id='".$zp_api_user_id."'", OBJECT );
		$zp_accounts_total = $wpdb->num_rows;
		
		$zp_public_key = false;
		
		if ($zp_accounts_total > 0)
		{
			header("Location: ".$zp_download_url."?key=".$zp_account[0]->public_key);
			exit;
		}
		else
		{
			$zp_xml = "No account found.";
		}
	}
?>