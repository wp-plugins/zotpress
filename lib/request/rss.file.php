<?php

	// Include WordPress
	require('../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require('rss.request.php');
	
	// Content prep
	$zp_xml = false;
	
	// Download (item key)
	if (isset($_GET['download']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['download']))
		$zp_item_key = trim(urldecode($_GET['download']));
	else
		$zp_xml = "No item key provided.";
	
	// Api User ID
	if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['api_user_id']))
		$zp_api_user_id = trim(urldecode($_GET['api_user_id']));
	else
		$zp_xml = "No API User ID provided.";
	
	// GET KEY FROM DB
	
	if ($zp_xml === false)
	{
		// Access WordPress db
		global $wpdb;
		
		$zp_download_url_query = "SELECT ".$wpdb->prefix."zotpress.public_key, ".$wpdb->prefix."zotpress_zoteroItems.citation
				FROM ".$wpdb->prefix."zotpress
				JOIN ".$wpdb->prefix."zotpress_zoteroItems ON ".$wpdb->prefix."zotpress.api_user_id = ".$wpdb->prefix."zotpress_zoteroItems.api_user_id
				WHERE ".$wpdb->prefix."zotpress_zoteroItems.item_key='".$zp_item_key."' 
				AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id='".$zp_api_user_id."';";
		
		$zp_download_url = $wpdb->get_results( $zp_download_url_query, OBJECT );
		
		if (count($zp_download_url) > 0)
		{
			header("Location: ".$zp_download_url[0]->citation."/file?key=".$zp_download_url[0]->public_key);
			exit;
		}
		else {
			$zp_xml = "No file to download found.";
		}
	}
	else {
		echo $zp_xml;
	}
?>