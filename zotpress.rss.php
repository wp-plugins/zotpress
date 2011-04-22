<?php

	// Include WordPress
	if (!isset( $include ) || $include == false)
		require('../../../wp-load.php');

	if (!defined('WP_USE_THEMES'))
		define('WP_USE_THEMES', false);


	function MakeZotpressRequest(
			$mzr_account_type=false,
			$mzr_api_user_id=false,
			$mzr_data_type=false,
			$mzr_collection_id=false,
			$mzr_item_key=false,
			$mzr_tag_name=false,
			$mzr_limit=false,
			$mzr_displayImages=false,
			$mzr_include=false,
			$mzr_force_recache=false)
	{
		// Access Wordpress db
		global $wpdb;
		
		// Include Special cURL
		require('zotpress.curl.php');
		
		$zp_xml = "";
		
		
		
		// SET UP VARS
		
		// Account Type
		if ($mzr_account_type == false && $mzr_include == false && isset($_GET['account_type']))
			$mzr_account_type = trim($_GET['account_type']);
		
		// API User ID
		if ($mzr_api_user_id == false && $mzr_include == false && isset($_GET['api_user_id']))
			$mzr_api_user_id = trim($_GET['api_user_id']);
		
		// Display Images
		if ($mzr_displayImages == false && $mzr_include == false && isset($_GET['displayImages']))
			if (trim($_GET['displayImages']) == "true")
				$mzr_displayImages = true;
			else
				$mzr_displayImages = false;
		
		
		
		// MAKE THE REQUEST
		if (isset($mzr_account_type) && isset($mzr_api_user_id))
		{
			
			// IMAGES
			
			if ($mzr_displayImages == true)
			{
				//header('Content-Type: text/xml');
				
				$zp_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
				
				$image_xml = "";
				
				global $wpdb;
				
				if (isset($_GET['displayImageByCitationID']))
					$images = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".trim($_GET['displayImageByCitationID'])."'");
				else
					$images = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_images");
				
				$total = $wpdb->num_rows;
				
				foreach ($images as $image)
					$image_xml .= "	<zpimage citation_id='".$image->citation_id."' account_type='".$image->account_type."' api_user_id='".$image->api_user_id."' image_url='".$image->image."' />\n";
					
				$zp_xml .= "\n<zpimages total=\"".$total."\">\n";
				$zp_xml .= $image_xml;
				$zp_xml .= "</zpimages>";
				
			}
			else
			{
				
				// DATA TYPE
				
				if ($mzr_data_type == false) {
					if (isset($_GET['data_type']) && $mzr_include == false) {
						$urlDataType = trim($_GET['data_type']);
					}
					else {
						$urlDataType = "items";
					}
				}
				else {
					$urlDataType = $mzr_data_type;
				}
				
				
				// LIST
				// Collection ID
				if ($mzr_collection_id == false) {
					if (isset($_GET['collection_id']) && $mzr_include == false && trim($_GET['collection_id']) != '') {
						$urlDataType = "collections/".trim($_GET['collection_id'])."/items";
					}
				}
				else {
					$urlDataType = "collections/".$mzr_collection_id."/items";
				}
				
				// Item Key
				if ($mzr_item_key != false) {
					$urlDataType = "items/".$mzr_item_key;
				}
				else if (isset($_GET['item_key']) && $mzr_include == false && trim($_GET['item_key']) != '') {
					$urlDataType = "items/".trim($_GET['item_key']);
				}
				
				// Tag Name
				if ($mzr_tag_name == false) {
					if (isset($_GET['tag_name']) && $mzr_include == false && trim($_GET['tag_name']) != '') {
						$urlDataType = "tags/".urlencode(trim($_GET['tag_name']))."/items";
					}
				}
				else {
					$urlDataType = "tags/".urlencode($mzr_tag_name)."/items";
				}
				
				
				// PARAMETERS
				
				// Author
				if (isset($_GET['author']) && trim($_GET['author'] != ''))
					$author = trim($_GET['author']);
				else
					$author = false;
					
				// Year
				if (isset($_GET['year']) && trim($_GET['year'] != ''))
					$year = trim($_GET['year']);
				else
					$year = false;
					
				// Content
				if (isset($_GET['content']))
					$content = "&content=" . $_GET['content'];
				else
					$content = "&content=bib";
				//if ($author || $year)
				//	$content = "&content=html";
				
				// Style
				if (isset($_GET['style']))
					$style = "&style=" . trim($_GET['style']);
				else
					$style = "&style=apa";
				
				// Order
				if (isset($_GET['order']) && $_GET['order'] != '')
					$order = "&order=" . $_GET['order'];
				else
					$order = false;
				
				// Sort
				if (isset($_GET['sort']) && $_GET['sort'] != '')
					$sort = "&sort=" . $_GET['sort'];
				else
					$sort = false;
				
				// Limit
				if ($mzr_limit != false)
					if ($mzr_limit == -1)
						$mzr_limit = false;
					else
						$mzr_limit = "&limit=".$mzr_limit;
				else
					if (isset($_GET['limit']) && $mzr_include == false && $_GET['limit'] != '')
						$mzr_limit = "&limit=".$_GET['limit'];
					
				if ($author || $year)
					$mzr_limit = false;
				
				
				
				// PUBLIC KEY
				$zp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$mzr_api_user_id."'");
				$public_key = $zp_account[0]->public_key;
				
				
				
				
				// Users & Groups [& Children]
				// ASSUMED: &format=bib
				
				if (isset( $_GET['children'] ))
					$zp_url = "https://api.zotero.org/".$mzr_account_type."/".$mzr_api_user_id."/".$urlDataType."/".$_GET['children']."/children?key=".$public_key;
				else
					$zp_url = "https://api.zotero.org/".$mzr_account_type."/".$mzr_api_user_id."/".$urlDataType."?key=".$public_key.$content.$style.$order.$sort.$mzr_limit;
					
					
				
				
				// DETERMINE IF FIRST OR SECOND STEP
				
				$zp_initial = false;
				
				if (isset( $_GET['step'] ) && $_GET['step'] == "one")
					$zp_initial = true;
				
				
				
				// DISPLAY
				
				if (in_array ('curl', get_loaded_extensions()))
				{
					$curl = new CURL();
					if ($zp_initial === true) {
						$curl->setInitial();
					} else {
						$curl->enableCache();
						$curl->recache( $mzr_force_recache );
					}
					$zp_xml = $curl->get_curl_contents( $zp_url );
				}
				else // Use the regular away
				{
					$curl = new CURL();
					if ($zp_initial === true) {
						$curl->setInitial();
					} else {
						$curl->enableCache();
						$curl->recache( $mzr_force_recache );
					}
					$zp_xml = $curl->get_file_get_contents( $zp_url );
				}
			}
			
			
			return $zp_xml;
		}
	}
	
	
	
	// DISPLAY XML
	
	if (!isset($include))
		print MakeZotpressRequest();



?>