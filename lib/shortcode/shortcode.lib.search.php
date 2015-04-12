<?php
	
    // Include WordPress
	
	$includewp = realpath("../../../../../wp-load.php");
	$includewp2 = realpath("../../../../../../wp-load.php");
	$includewp3 = realpath("../../../../../../../wp-load.php");
	
	if ( $includewp === false )
		if ( $includewp2 === false )
			if ( $includewp3 === false )
				trigger_error("Could not find file {$filename}", E_USER_ERROR);
			else
				require($includewp3);
		else
			require($includewp2);
	else
	    require($includewp);
	
    define('WP_USE_THEMES', false);
    
    // Prevent access to users who are not editors
    if ( ! current_user_can('edit_others_posts') && ! is_admin() )
		wp_die( __('Only logged-in editors can access this page.'), __('Zotpress: 403 Access Denied'), array( 'response' => 403 ) );
    
    global $wpdb;
    
    header('Content-type: text/html; charset=utf-8');
    
	
    // Determine account
	if ( isset($_GET['user']) && preg_match("/^[0-9]+$/", $_GET['user']) == 1 )
	{
		$zp_api_user_id = $_GET['user'];
	}
	else // No user id passed through
	{
		if (get_option("Zotpress_DefaultAccount"))
		{
			$zp_api_user_id = get_option("Zotpress_DefaultAccount");
		}
		else
		{
			$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
			$zp_api_user_id = $zp_account->api_user_id;
		}
	}
	
	
	// Determine filter, if any
	$filter = "items"; if ( isset($_GET['filter']) && preg_match("/^[a-z]+$/", $_GET['filter']) == 1 ) $filter = $_GET['filter'];
	
	// Determine max results, if set
	$limit = "100"; if ( isset($_GET['maxresults']) && preg_match("/^[0-9]+$/", $_GET['maxresults']) == 1 ) $limit = $_GET['maxresults'];


	if ( $filter )
	{
		if ( $filter == "items" )
		{
			$tempquery =
			"
				SELECT DISTINCT ".$wpdb->prefix."zotpress_zoteroItems.author,
					".$wpdb->prefix."zotpress_zoteroItems.json,
					".$wpdb->prefix."zotpress_zoteroItems.citation AS item,
					".$wpdb->prefix."zotpress_zoteroItems.item_key 
				FROM ".$wpdb->prefix."zotpress_zoteroItems
				
				WHERE ".$wpdb->prefix."zotpress_zoteroItems.api_user_id='".$zp_api_user_id."' 
				AND ".$wpdb->prefix."zotpress_zoteroItems.itemType NOT IN ('attachment', 'note') 
				AND ".$wpdb->prefix."zotpress_zoteroItems.citation LIKE %s
				
				ORDER BY ".$wpdb->prefix."zotpress_zoteroItems.author ASC LIMIT ".$limit."
			";
			
			$zpSearchResults = $wpdb->get_results(
				$wpdb->prepare( $tempquery, '%' . $wpdb->esc_like($_GET['term']) . '%'
			), OBJECT );
		}
		elseif ( $filter == "collections" )
		{
			//					CONCAT( ".$wpdb->prefix."zotpress_zoteroCollections.title, ' (', ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key, ')' ) AS item_key 
			$tempquery =
			"
				SELECT DISTINCT ".$wpdb->prefix."zotpress_zoteroItems.author,
					".$wpdb->prefix."zotpress_zoteroItems.json,
					".$wpdb->prefix."zotpress_zoteroItems.citation AS item,
					".$wpdb->prefix."zotpress_zoteroCollections.title AS item_key 
				FROM ".$wpdb->prefix."zotpress_zoteroRelItemColl
				
				LEFT JOIN ".$wpdb->prefix."zotpress_zoteroCollections 
				ON ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key=".$wpdb->prefix."zotpress_zoteroCollections.item_key  
				
				INNER JOIN ".$wpdb->prefix."zotpress_zoteroItems 
				ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key 
				
				WHERE ".$wpdb->prefix."zotpress_zoteroRelItemColl.api_user_id='".$zp_api_user_id."' 
				AND ".$wpdb->prefix."zotpress_zoteroRelItemColl.api_user_id=".$wpdb->prefix."zotpress_zoteroItems.api_user_id 
				AND ".$wpdb->prefix."zotpress_zoteroRelItemColl.api_user_id=".$wpdb->prefix."zotpress_zoteroCollections.api_user_id 
				AND ".$wpdb->prefix."zotpress_zoteroItems.itemType NOT IN ('attachment', 'note') 
				AND ".$wpdb->prefix."zotpress_zoteroCollections.title LIKE %s
				
				ORDER BY ".$wpdb->prefix."zotpress_zoteroItems.author ASC LIMIT ".$limit."
			";
			
			$zpSearchResults = $wpdb->get_results(
				$wpdb->prepare( $tempquery, '%' . $wpdb->esc_like($_GET['term']) . '%'
			), OBJECT );
		}
		elseif ( $filter == "tags" )
		{
			$tempquery =
			"
				SELECT DISTINCT ".$wpdb->prefix."zotpress_zoteroItems.author,
					".$wpdb->prefix."zotpress_zoteroItems.json,
					".$wpdb->prefix."zotpress_zoteroItems.citation AS item,
					".$wpdb->prefix."zotpress_zoteroRelItemTags.tag_title AS item_key 
				FROM ".$wpdb->prefix."zotpress_zoteroItems
				
				INNER JOIN ".$wpdb->prefix."zotpress_zoteroRelItemTags 
				ON ".$wpdb->prefix."zotpress_zoteroItems.api_user_id=".$wpdb->prefix."zotpress_zoteroRelItemTags.api_user_id 
				AND ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemTags.item_key 
				
				WHERE ".$wpdb->prefix."zotpress_zoteroItems.api_user_id='".$zp_api_user_id."' 
				AND ".$wpdb->prefix."zotpress_zoteroItems.itemType NOT IN ('attachment', 'note') 
				AND ".$wpdb->prefix."zotpress_zoteroRelItemTags.tag_title LIKE %s
				
				ORDER BY ".$wpdb->prefix."zotpress_zoteroItems.author ASC LIMIT ".$limit."
			";
			
			$zpSearchResults = $wpdb->get_results(
				$wpdb->prepare( $tempquery, '%' . $wpdb->esc_like($_GET['term']) . '%'
			), OBJECT );
		}
	}
	//else // ALL: How? Would be a really complex query
	//{
	//}
	
	
    // Format results for display
	
    $zpSearch = array();
    
    if ( isset($zpSearchResults) && count($zpSearchResults) > 0 )
    {
        foreach ( $zpSearchResults as $zpResult )
        {
            // Deal with author
            $author = $zpResult->author;
            $zpResultJSON = json_decode( $zpResult->json );
            
            if ( $author == "" )
            {
                if ( isset($zpResultJSON->creators) && count($zpResultJSON->creators) > 0 )
                    foreach ( $zpResultJSON->creators as $i => $creator )
                        if ( $i != (count($zpResultJSON->creators)-1) )
                            $author .= $creator->name . ', ';
                        else
                            $author .= $creator->name;
            }
            
            array_push( $zpSearch, array( "author" => $author, "item" => $zpResult->item, "item_key" => $zpResult->item_key) );
        }
    }
    
    echo json_encode($zpSearch);
    
    unset($zp_api_user_id);
    unset($zp_account);
    $wpdb->flush();

?>