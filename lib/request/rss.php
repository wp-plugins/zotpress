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
			$mzr_force_recache=false, // DO WE NEED THIS?
			$mzr_instance_id=false, // REMOVE
			$mzr_get_meta=false,
			$mzr_get_children=false,
			$mzr_get_style=false,
			$mzr_topLevel=false
		)
	{
		// Access WordPress db
		global $wpdb;
		
		// Include Request Functionality
		require('rss.request.php');
		
		// Set up vars
		$zp_xml = "";
		
		
		
		// SET UP VARS
		
		// API User ID
		if ($mzr_api_user_id == false
			&& $mzr_include == false
			&& (isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id'])))
		{
			$mzr_api_user_id = trim($_GET['api_user_id']);
		}
		
		
		// Account Type
		if ($mzr_account_type == false
			&& $mzr_include == false
			&& (isset($_GET['account_type']) && preg_match("/^[a-zA-Z]+$/", $_GET['account_type'])))
		{
			$mzr_account_type = trim($_GET['account_type']);
		}
		
		
		// Display Images
		if ($mzr_displayImages == false
			&& $mzr_include == false
			&& (isset($_GET['displayImages']) && preg_match("/^[a-zA-Z]+$/", $_GET['displayImages'])))
		{
			if ($_GET['displayImages'] == "true")
				$mzr_displayImages = true;
			else
				$mzr_displayImages = false;
		}
		
		
		
		// MAKE THE REQUEST
		if (isset($mzr_account_type) && isset($mzr_api_user_id))
		{
			
			// IMAGES
			
			if ($mzr_displayImages == true)
			{
				$zp_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
				
				$image_xml = "";
				
				global $wpdb;
				
				if (isset($_GET['displayImageByCitationID']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['displayImageByCitationID']))
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
				
				if ($mzr_data_type == false)
				{
					if (isset($_GET['data_type']) && $mzr_include == false && preg_match("/^[a-zA-Z]+$/", $_GET['data_type']))
						$urlDataType = $_GET['data_type'];
					else
						$urlDataType = "items/top";
				}
				else
				{
					$urlDataType = $mzr_data_type;
					
					if ($urlDataType == "items")
						$urlDataType = "items/top";
				}
				
				
				// LIST
				
				// Collection ID
				if ($mzr_collection_id == false)
				{
					if (isset($_GET['collection_id']) && $mzr_include == false && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id'])) {
						$urlDataType = "collections/".$_GET['collection_id']."/items";
					}
				}
				else
				{
					$urlDataType = "collections/".$mzr_collection_id."/items";
				}
				
				// Item Key
				if ($mzr_item_key != false)
				{
					$urlDataType = "items/".$mzr_item_key;
				}
				else if (isset($_GET['item_key']) && $mzr_include == false && preg_match("/^[a-zA-Z0-9]+$/", $_GET['item_key'])) {
					$urlDataType = "items/".$_GET['item_key'];
				}
				
				// Children
				if ($mzr_get_children === true)
				{
					$urlDataType .= "/children";
				}
				
				
				// Tag Name
				if ($mzr_tag_name == false)
				{
					if (isset($_GET['tag_name']) && $mzr_include == false && preg_match("/^[a-zA-Z0-9 -_+]+$/", $_GET['tag_name'])) {
						$urlDataType = "tags/".str_replace(" ", "+", trim($_GET['tag_name']))."/items";
					}
				}
				else
				{
					$mzr_tag_name = str_replace(" ", "+", trim($mzr_tag_name));
					$urlDataType = "tags/".$mzr_tag_name."/items";
				}
				
				
				// PARAMETERS
				
				// Author
				$author = false;
				if (isset($_GET['author'])  && preg_match("/^[a-zA-Z0-9 -_+]+$/", $_GET['author']))
				{
					$author = $_GET['author'];
				}
				
				// Year
				$year = false;
				if (isset($_GET['year']) && preg_match("/^[0-9]+$/", $_GET['year']))
				{
					$year = $_GET['year'];
				}
				
				// Content
				if (isset($_GET['content']) && preg_match("/^[a-zA-Z]+$/", $_GET['content']))
				{
					$content = "&content=" . $_GET['content'];
				}
				else
				{
					if ($mzr_get_meta == true) {
						$content = "&content=json";
					}
					else if ($mzr_get_children == true) {
						$content = "";
					}
					else {
						$content = "&content=bib";
					}
				}
				
				// Style
				if (isset($_GET['style']) && preg_match("/^[a-zA-Z-_]+$/", $_GET['style'])) {
					$style = "&style=" . $_GET['style'];
				}
				else {
					$style = "&style=apa";
				}
				if ($mzr_get_style == true) {
					$style = "&style=".$mzr_get_style;
				}
				
				// Order
				$order = false;
				if (isset($_GET['order']) && preg_match("/^[a-zA-Z]+$/", $_GET['order']))
				{
					$order = "&order=" . $_GET['order'];
				}
				
				// Sort
				$sort = false;
				if (isset($_GET['sort']) && preg_match("/^[a-zA-Z]+$/", $_GET['sort'])) {
					$sort = "&sort=" . $_GET['sort'];
				}
				
				// Limit
				if ($mzr_limit != false)
				{
					if ($mzr_limit == -1) {
						$mzr_limit = false;
					}
					else {
						$mzr_limit = "&limit=".$mzr_limit;
					}
				}
				else
				{
					if (isset($_GET['limit']) && $mzr_include == false && preg_match("/^[0-9]+$/", $_GET['limit'])) {
						$mzr_limit = "&limit=".$_GET['limit'];
					}
				}
				
				if ($author || $year)
				{
					$mzr_limit = false;
				}
				
				
				
				// PUBLIC KEY
				$zp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$mzr_api_user_id."'");
				$public_key = $zp_account[0]->public_key;
				
				
				
				// GENERATE URL: Users & Groups [& Children]		ASSUMED TO BE SET AS: &format=bib
				
				if (isset($_GET['children']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['children']))
					$zp_url = "https://api.zotero.org/".$mzr_account_type."/".$mzr_api_user_id."/".$urlDataType."/".$_GET['children']."/children?key=".$public_key;
				else if (isset($_GET['topLevel']) && trim($_GET['topLevel']) == "true")
					$zp_url = str_replace("/items", "/items/top", "https://api.zotero.org/".$mzr_account_type."/".$mzr_api_user_id."/".$urlDataType."?key=".$public_key.$content.$style.$order.$sort.$mzr_limit);
				else
					$zp_url = "https://api.zotero.org/".$mzr_account_type."/".$mzr_api_user_id."/".$urlDataType."?key=".$public_key.$content.$style.$order.$sort.$mzr_limit;
				
				
				//echo "<br />" . $zp_url . "<br />";
				
				
				
				// GET & DISPLAY CITATIONS
				
				$zp_request = new ZotpressRequest();
				$zp_xml = $zp_request->get_request_contents( $zp_url, $mzr_force_recache );
			}
			
			return $zp_xml;
		}
	}
	
	
	
	// DISPLAY XML
	
	if (!isset($include))
		print MakeZotpressRequest();



?>