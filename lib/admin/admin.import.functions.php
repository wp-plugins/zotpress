<?php
    
    

    /****************************************************************************************
    *
    *     ZOTPRESS BASIC IMPORT FUNCTIONS
    *
    ****************************************************************************************/
    
    function zp_db_prep ($input)
    {
        $input = str_replace("%", "%%", $input);
        return ($input);
    }
    
    
    
    function zp_extract_year ($date)
    {
		preg_match_all( '/(\d{4})/', $date, $matches );
		return $matches[0][0];
    }
    
    
    
    function zp_set_update_time ($time)
    {
        update_option("Zotpress_LastAutoUpdate", $time);
    }
    
    
    
    function zp_get_api_user_id ($api_user_id_incoming=false)
    {
        if (isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
            $api_user_id = htmlentities($_GET['api_user_id']);
        else if ($api_user_id_incoming !== false)
            $api_user_id = $api_user_id_incoming;
        else
            $api_user_id = false;
        
        return $api_user_id;
    }
    
    
    
    function zp_get_account ($wpdb, $api_user_id_incoming=false)
    {
        if ($api_user_id_incoming !== false)
            $zp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id_incoming."'");
        else
            $zp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1");
        
        return $zp_account;
    }



    function zp_get_accounts ($wpdb)
    {
        $zp_accounts = $wpdb->get_results("SELECT api_user_id FROM ".$wpdb->prefix."zotpress");
        
        return $zp_accounts;
    }
	
	
	
	//function zp_delete_collection ($term_id)
	//{
	//	wp_delete_term( $term_id, 'zp_collections' );
	//	delete_option( 'zp_collection-'.$term_id.'-api_user_id' );
	//	delete_option( 'zp_collection-'.$term_id.'-retrieved' );
	//	delete_option( 'zp_collection-'.$term_id.'-parent' );
	//	delete_option( 'zp_collection-'.$term_id.'-item_key' );
	//	delete_option( 'zp_collection-'.$term_id.'-numCollections' );
	//	delete_option( 'zp_collection-'.$term_id.'-numItems' );
	//	delete_option( 'zp_collection-'.$term_id.'-items' );
	//}
	//
	//
	//
	//function zp_delete_tag ($term_id)
	//{
	//	wp_delete_term( $term_id, 'zp_tags' );
	//	delete_option( 'zp_tag-'.$term_id.'-api_user_id' );
	//	delete_option( 'zp_tag-'.$term_id.'-retrieved' );
	//	delete_option( 'zp_tag-'.$term_id.'-numItems' );
	//	delete_option( 'zp_tag-'.$term_id.'-items' );
	//}
	
	
	// Function to recursively delete collections and their items
	function zp_selectively_delete_collection ( $wpdb, $api_user_id, $collection )
	{
		$collection_item_list = array();
		
		// First, get items
		$items = $wpdb->get_results(
			"
			SELECT ".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key
			FROM ".$wpdb->prefix."zotpress_zoteroRelItemColl
			WHERE api_user_id='".$api_user_id."'
			AND ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key='".$collection."'
			",
			OBJECT
		);
		
		if ( count($items) > 0 )
		{
			foreach ( $items as $item )
			{
				// Remember item to check tags later
				$collection_item_list[count($collection_item_list)] = $item->item_key;
				
				// Delete item's children
				$wpdb->query(
					"
					DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems 
					WHERE api_user_id='".$api_user_id."' AND parent='".$item->item_key."'
					"
				);
			}
			// Then delete items
			$wpdb->query(
				"
				DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems 
				WHERE api_user_id='".$api_user_id."' AND item_key IN ( '".implode("','", $collection_item_list)."' )
				"
			);
			// And their relationships with collections
			$wpdb->query(
				"
				DELETE FROM ".$wpdb->prefix."zotpress_zoteroRelItemColl
				WHERE api_user_id='".$api_user_id."' AND item_key IN ( '".implode("','", $collection_item_list)."' )
				"
			);
		}
		
		unset( $items );
		
		// Then delete collection 
		$wpdb->query(
			$wpdb->prepare(
				"
				DELETE FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='%s' AND item_key='%s'
				",
				$api_user_id, $collection
			)
		);
		
		// Next, delete subcollections
		$subcollections = $wpdb->get_results(
			"
			SELECT item_key FROM ".$wpdb->prefix."zotpress_zoteroCollections 
			WHERE api_user_id='".$api_user_id."' AND parent='".$collection."'
			"
		);
		
		if ( count($subcollections) > 0 )
		{
			foreach ( $subcollections as $subcollection )
			{
				$temp = zp_selectively_delete_collection( $wpdb, $api_user_id, $subcollection->item_key );
				
				if ( $temp ) $GLOBALS['zp_session'][$api_user_id]['collection_item_list'] = array_merge( $temp, $GLOBALS['zp_session'][$api_user_id]['collection_item_list'] );
			}
		}
		
		unset( $subcollections );
		
		if ( count($collection_item_list) > 0) return $collection_item_list; else return false;
	}
    
    
    
    function zp_clear_last_import ($wpdb, $api_user_id, $step, $collections=false)
    {
        switch ($step)
        {
            case "items":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."'");
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroRelItemColl WHERE api_user_id='".$api_user_id."'");
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroRelItemTags WHERE api_user_id='".$api_user_id."'");
				//$zp_entry_array = get_posts(
				//	array(
				//		'posts_per_page'   => -1,
				//		'post_type' => 'zp_entry',
				//		'meta_key' => 'api_user_id',
				//		'meta_value' => $api_user_id
				//	)
				//);
				//foreach ($zp_entry_array as $zp_entry) wp_delete_post( $zp_entry->ID, true );
                break;
			
            case "collections":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."'");
				//$zp_collections_array = get_terms(
				//	'zp_collections',
				//	array(
				//		'hide_empty' => false
				//	)
				//);
				//foreach ($zp_collections_array as $zp_collection_term)
				//	if ( get_option( 'zp_collection-'.$zp_collection_term->term_id.'-api_user_id' ) )
				//		zp_delete_collection ($zp_collection_term->term_id);
                break;
			
            case "tags":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."'");
				//$zp_tags_array = get_terms(
				//	'zp_tags',
				//	array(
				//		'hide_empty' => false
				//	)
				//);
				//foreach ($zp_tags_array as $zp_tag_term)
				//	if ( get_option( 'zp_tag-'.$zp_tag_term->term_id.'-api_user_id' ) )
				//		zp_delete_tag ($zp_tag_term->term_id);
                break;
			
            case "selective":
				if ( $collections !== false )
				{
					$collection_item_list = array(); // for tags
					//$collections = str_replace( ",", "', '", $collections );
					//$collections = explode( ",", $collections );
					//$all_top_level_collections = get_terms( 'zp_collections', array( 'hide_empty' => false, 'parent' => 0 ) );
					//$all_top_level_collections = $wpdb->get_results(
					//	"
					//	SELECT item_key FROM ".$wpdb->prefix."zotpress_zoteroCollections
					//	WHERE api_user_id='".$api_user_id."' AND item_key IN ( '".$collections."' )
					//	"
					//);
					$collections = explode(",", $collections);
					$GLOBALS['zp_session'][$api_user_id]['collection_item_list'] = array();
					
					// Delete selected top level collection, items, subcollections and their items
					if ( count($collections) > 0 )
					{
						foreach ( $collections as $top_level_collection )
						{
							$temp = zp_selectively_delete_collection( $wpdb, $api_user_id, $top_level_collection );
							
							if ( $temp ) $collection_item_list = array_merge( $temp, $collection_item_list );
						}
					}
					
					// Merge item lists from top level collection with subcollection item lists
					$collection_item_list = array_merge( $collection_item_list, $GLOBALS['zp_session'][$api_user_id]['collection_item_list'] );
					
					// Remove items from tags, delete tags if no items
					// This means that once imported, tags will remain if there's another item key, even if the item itself isn't imported
					if ( count($collection_item_list) > 0 )
					{
						foreach ( $collection_item_list as $item_key )
						{
							// Get this item's tags
							$item_tags = $wpdb->get_results(
								"
								SELECT tag_title FROM ".$wpdb->prefix."zotpress_zoteroRelItemTags 
								WHERE api_user_id='".$api_user_id."' AND item_key='".$item_key."'
								"
							);
							
							if ( count($item_tags) > 0 )
							{
								foreach ( $item_tags as $item_tag )
								{
									// Delete relationship with item
									$wpdb->query(
										"
										DELETE FROM ".$wpdb->prefix."zotpress_zoteroRelItemTags
										WHERE api_user_id='".$api_user_id."'
										AND (item_key = '".$item_key."' AND tag_title = '".$item_tag->tag_title."')
										"
									);
									
									// Delete tags if they don't have items
									$tag_itemrel_count = $wpdb->get_results(
										"
										SELECT COUNT(*) AS itemrel_count FROM ".$wpdb->prefix."zotpress_zoteroRelItemTags 
										WHERE api_user_id='".$api_user_id."' AND tag_title='".$item_tag->tag_title."'
										"
									);
									
									if ( $tag_itemrel_count[0]->itemrel_count < 1)
									{
										$wpdb->query(
											"
											DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags
											WHERE api_user_id='".$api_user_id."'
											AND title = '".$item_tag->tag_title."'
											"
										);
									}
									
									//$tag_items = str_replace( $item_key.",", "", $item_tag->listItems."," );
									//
									//// Update tag's item list
									//if ( strlen($tag_items) > 0 )
									//{
									//	$wpdb->query( 
									//		$wpdb->prepare( 
									//			"
									//			UPDATE ".$wpdb->prefix."zotpress_zoteroTags
									//			SET listItems=%s
									//			WHERE id=%d
									//			",
									//			rtrim( $tag_items, "," ), $item_tag->id
									//		)
									//	);
									//}
									//else // No items, so delete
									//{
									//	$wpdb->query(
									//		$wpdb->prepare(
									//			"
									//			DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE id=%d
									//			",
									//			$item_tag->id
									//		)
									//	);
									//}
								}
							}
							unset($item_tags);
						}
						unset($collection_item_list);
						
						/*
						$all_tags = $wpdb->get_results(
							"
							SELECT id, title, listItems FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."'
							"
						);
						
						foreach ( $all_tags as $zp_tag )
						{
							$zp_tag_items = explode( ',', $zp_tag->listItems );
							$updated_item_list = $zp_tag->listItems;
							
							// Create new item list for tag
							foreach ( $zp_tag_items as $zp_tag_item ) {
								if ( in_array( $zp_tag_item, $collection_item_list ) ) {
									$updated_item_list = str_replace( $zp_tag_item.',', '', $updated_item_list );
								}
							}
							
							if ( strlen($updated_item_list) > 0 )
							{
								// Update tag's item list
								$wpdb->query( 
									$wpdb->prepare( 
										"
										UPDATE ".$wpdb->prefix."zotpress_zoteroTags
										SET listItems=%s
										WHERE id=%s
										",
										rtrim( $updated_item_list, "," ), $zp_tag->id
									)
								);
							}
							else  // No items left, so delete tag
							{
								$wpdb->query(
									$wpdb->prepare(
										"
										DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE id=%s
										",
										$zp_tag->id
									)
								);
							}
							
							unset($zp_tag_items);
							unset($updated_item_list);
						}
						
						unset($all_tags);
						unset($collection_item_list);
						unset($all_top_level_collections);
						*/
					}
					
					/*
					if ( count($all_top_level_collections) > 0 )
					{
						// Delete selected top level collection, items, subcollections and their items
						foreach ( $all_top_level_collections as $top_level_collection )
						{
							if ( in_array( $top_level_collection->description, $collections ) )
							{
								// Get subcollections
								$subcollections = get_terms( 'zp_collections', array( 'hide_empty' => true, 'child_of' => $top_level_collection->term_id ) );
								
								if ( count($subcollections) > 0 )
								{
									foreach ( $subcollections as $subcollection )
									{
										// Get subcollection items
										$subcollection_items = get_option( 'zp_collection-'.$subcollection->term_id.'-items' );
										if ( $subcollection_items !== false && strlen(trim($subcollection_items)) > 0 ) $collection_item_list .= trim($subcollection_items).','; // add to item list
										unset($subcollection_items);
										
										// Delete subcollection
										wp_delete_term( $subcollection->term_id, 'zp_collections' );
									}
								}
								unset($subcollections);
								
								// Get collection items
								$collection_items = get_option( 'zp_collection-'.$top_level_collection->term_id.'-items' );
								if ( $collection_items !== false && strlen(trim($collection_items)) > 0 ) $collection_item_list .= trim($collection_items).','; // add to item list
								unset($collection_items);
								
								// Delete collection
								wp_delete_term( $top_level_collection->term_id, 'zp_collections' );
							}
						}
						
						if ( trim($collection_item_list) != '' )
						{
							$collection_item_list_arr = explode( ',', rtrim( $collection_item_list, ',' ) );
							$all_tags = get_terms( 'zp_tags', array( 'hide_empty' => false ) );
							
							// Remove items and thier children
							foreach ( $collection_item_list_arr as $c_item )
							{
								// Get item's data
								$c_item_data = get_posts( array( 'posts_per_page' => 1, 'post_type' => 'zp_entry', 'meta_key' => 'item_key', 'meta_value' => $c_item ) );
								
								// Get item's children
								$c_item_children = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'zp_entry', 'meta_key' => 'parent', 'meta_value' => $c_item ) );
								
								if ( count($c_item_children) > 0 )
								{
									foreach ( $c_item_children as $c_item_child )
									{
										$collection_item_list .= get_metadata( 'zp_entry', $c_item_child->ID, 'item_key' ).',';
										wp_delete_post( $c_item_child->ID, true );
									}
									unset($c_item_children);
								}
								foreach ( $c_item_data as $i ) wp_delete_post( $i->ID, true );
								unset($c_item_data);
							}
							unset($collection_item_list_arr);
							
							
							// Remove items from tags, delete tags if no items
							foreach ( $all_tags as $zp_tag )
							{
								$zp_tag_items = explode( ',', $zp_tag->description );
								$updated_item_list = $zp_tag->description;
								
								// Create new item list for tag
								foreach ( $zp_tag_items as $zp_tag_item ) if ( in_array( $zp_tag_item, $collection_item_list ) ) $updated_item_list = str_replace( $zp_tag_item.',', '', $updated_item_list );
								
								if ( strlen($updated_item_list) > 0 )
								{
									// Update tag's item list
									wp_update_term( $zp_tag->term_id, 'zp_tags', array( 'description' => rtrim( $updated_item_list, ',' ) ) );
								}
								else  // No items left, so delete tag
								{
									wp_delete_term( $tag_to_delete->term_id, 'zp_tags' );
								}
								
								unset($zp_tag_items);
								unset($updated_item_list);
							}
							unset($all_tags);
							
						} // collection items exist
					} // top level collections exist
					*/
				} // collections to review exist
				
                break;
        }
    }
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT ITEMS
    *
    ****************************************************************************************/
    
    function zp_get_items ($wpdb, $api_user_id, $zp_start, $zp_collection=false)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
        
        
        // Get default style
        $zp_default_style = "apa";
        if (get_option("Zotpress_DefaultStyle")) $zp_default_style = strtolower( get_option("Zotpress_DefaultStyle") );
        
        // Build request URL
		if ( $zp_collection ) $zp_collection_url = '/collections/'.$zp_collection; else $zp_collection_url = '';
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id.$zp_collection_url."/items?";
		if ( is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "" )
			$zp_import_url .= "key=".$zp_account[0]->public_key."&";
		$zp_import_url .= "format=atom&content=json,bib&style=".$zp_default_style."&limit=50&start=".$zp_start;
		//var_dump($zp_import_url);
        
		
		// Make the request
		$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
		
        // Stop in our tracks if there's a request error
        if ($zp_import_contents->request_error) return $zp_import_contents->request_error;
        
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($GLOBALS['zp_session'][$api_user_id]['items']['last_set']))
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['zp_session'][$api_user_id]['items']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['zp_session'][$api_user_id]['items']['last_set'] = 0;
                    }
                }
            }
        }
        
        
        // PREPARE EACH ENTRY FOR DB INSERT
        // Entries can be items or attachments (e.g. notes)
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
			
			//// For selective import: Keep track of and skip duplicates
			//// Not working for some reason
			////if ( $zp_collection )
			////{
			//	if ( array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['duplicates']['items'] ) )
			//		continue;
			//	else
			//		$GLOBALS['zp_session'][$api_user_id]['duplicates']['items'][$item_key] = true;
			////}
			
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
            
            // Get citation content (json and bib)
            
            $citation_content = "";
            $citation_content_temp = new DOMDocument();
            
            foreach($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "subcontent") as $child)
            {
                if ($child->attributes->getNamedItem("type")->nodeValue == "json")
                {
                    $json_content = $child->nodeValue;
                }
                else // Styled citation
                {
                    foreach($child->childNodes as $child_content) {
                        $citation_content_temp->appendChild($citation_content_temp->importNode($child_content, true));
                        $citation_content = $citation_content_temp->saveHTML();
                    }
                }
            }
            
            // Get basic metadata from JSON
            $json_content_decoded = json_decode($json_content);
			
            $author = "";
            $author_other = "";
            $date = "";
            $year = "";
            $title = "";
            $numchildren = 0;
            $parent = "";
            $link_mode = "";
			
			
            if (count($json_content_decoded->creators) > 0)
                foreach ($json_content_decoded->creators as $creator)
                    if ($creator->creatorType == "author")
						if (isset($creator->name)) // One-name authors
							$author .= $creator->name . ", ";
						else
							$author .= $creator->lastName . ", ";
                    else
						if (isset($creator->name)) // One-name authors
							$author_other .= $creator->name . ", ";
						else
							$author_other .= $creator->lastName . ", ";
            else
				if (isset($creator->name)) // One-name authors
					$author .= $creator->creators["name"];
				else
	                $author .= $creator->creators["lastName"];
            
            // Determine if we use author or other author type
            if (trim($author) == "") $author = $author_other;
            
            // Remove last comma
            $author = preg_replace('~(.*)' . preg_quote(', ', '~') . '~', '$1' . '', $author, 1);
            
            $date = $json_content_decoded->date;
            $year = zp_extract_year($date);
            
            if (trim($year) == "") $year = "0000";
            
            $title = $json_content_decoded->title;
            
            $numchildren = intval($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue);
            
            // DOWNLOAD: Find URL
            // for attachments, look at zapi:subcontent zapi:type="json" - linkMode - either imported_file or linked_url
            if ($item_type == "attachment")
                if (isset($json_content_decoded->linkMode)) $link_mode = $json_content_decoded->linkMode;
            
            // PARENT
			//if ( $zp_collection ) // This was setting the parent of attachments to the collection 
			//{
			//	$parent = $zp_collection;
			//}
			//else // Regular
			//{
				foreach($entry->getElementsByTagName("link") as $entry_link)
				{
					if ($entry_link->getAttribute('rel') == "up") {
						$temp = explode("items/", $entry_link->getAttribute('href'));
						$temp = explode("?", $temp[1]);
						$parent = $temp[0];
					}
					
					// Get download URL
					if ($link_mode == "imported_file" && $entry_link->getAttribute('rel') == "self") {
						$citation_content = substr($entry_link->getAttribute('href'), 0, strpos($entry_link->getAttribute('href'), "?"));
					}
				}
			//}
            
            // Prep for insert into db
            array_push($GLOBALS['zp_session'][$api_user_id]['items']['query_params'],
                    $zp_account[0]->api_user_id,
                    $item_key,
                    zp_db_prep($retrieved),
                    zp_db_prep($json_content),
                    zp_db_prep($author),
                    zp_db_prep($date),
                    zp_db_prep($year),
                    zp_db_prep($title),
                    $item_type,
                    $link_mode,
                    zp_db_prep($citation_content),
                    zp_db_prep($zp_default_style),
                    $numchildren,
                    $parent);
            
            $GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries']++;
            
        } // foreach entry
        
        
        // LAST SET
        if ($GLOBALS['zp_session'][$api_user_id]['items']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of items
        {
            return true;
        }
        
        unset($zp_import_contents);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_items
    
    
    
    function zp_save_items ($wpdb, $api_user_id, $not_done=false)
    {
        if ($GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries'] > 0)
        {
			// Prepare query strings
			$zp_relItemColl = "";
			$zp_relItemTags = "";
			
			// Determine item-collection and item-tag relationships with JSON
			for ( $i = 3; $i < count($GLOBALS['zp_session'][$api_user_id]['items']['query_params']); $i += 14 )
			{
				$i_json = json_decode($GLOBALS['zp_session'][$api_user_id]['items']['query_params'][$i]);
				
				if ( isset($i_json->collections) && count($i_json->collections) > 0 )
					foreach ( $i_json->collections as $i_collection )
						$zp_relItemColl .= "('" . $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][$i-3] . "', '" . $i_json->itemKey . "', '" . htmlentities($i_collection) . "'), ";
				
				if ( isset($i_json->tags) && count($i_json->tags) > 0 )
					foreach ( $i_json->tags as $i_tag )
						if ( trim($i_tag->tag) != "" )
							$zp_relItemTags .= "('" . $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][$i-3] . "', '" . $i_json->itemKey . "', '" . htmlentities($i_tag->tag) . "'), ";
			}
			
			// Prepare string: remove extra comma and space OR set to blank if nothing to add
			if ( strlen($zp_relItemColl) > 0 )
			{
				$zp_relItemColl = "INSERT IGNORE INTO ".$wpdb->prefix."zotpress_zoteroRelItemColl 
					( api_user_id, item_key, collection_key ) VALUES " . substr( $zp_relItemColl, 0, -2 ) . "; ";
			}
			
			if ( strlen($zp_relItemTags) > 0 )
			{
				$zp_relItemTags = "INSERT IGNORE INTO ".$wpdb->prefix."zotpress_zoteroRelItemTags 
					( api_user_id, item_key, tag_title ) VALUES " . substr( $zp_relItemTags, 0, -2 ) . "; ";
			}
			
			// Execute queries
			$wpdb->query( $zp_relItemColl );
			$wpdb->query( $zp_relItemTags );
            $wpdb->query(
				$wpdb->prepare( 
					"   INSERT IGNORE INTO ".$wpdb->prefix."zotpress_zoteroItems
						( api_user_id, item_key, retrieved, json, author, zpdate, year, title, itemType, linkMode, citation, style, numchildren, parent )
						VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )" . str_repeat(", ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )", $GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries']-1) .";", 
					$GLOBALS['zp_session'][$api_user_id]['items']['query_params']
				)
			);
            
            $wpdb->flush();
        }
        /*if ($GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries'] > 0)
        {
			global $user_ID;
			
			for ($i = 0; $i <= ($GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries'] - 1); $i++ )
			{
				$mod = $i * 14; 
				
				$post_id = wp_insert_post(array(
					'post_title' => $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][7+$mod],
					'post_content' => $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][10+$mod],
					'post_status' => 'publish',
					'post_date' => date('Y-m-d H:i:s'),
					'post_author' => $user_ID,
					'post_type' => 'zp_entry'
				));
				
				update_post_meta($post_id, 'api_user_id', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][0+$mod]);
				update_post_meta($post_id, 'item_key', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][1+$mod]);
				update_post_meta($post_id, 'retrieved', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][2+$mod]);
				update_post_meta($post_id, 'json_content', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][3+$mod]);
				update_post_meta($post_id, 'author', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][4+$mod]);
				update_post_meta($post_id, 'date', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][5+$mod]);
				update_post_meta($post_id, 'year', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][6+$mod]);
				update_post_meta($post_id, 'item_type', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][8+$mod]);
				update_post_meta($post_id, 'link_mode', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][9+$mod]);
				update_post_meta($post_id, 'style', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][11+$mod]);
				update_post_meta($post_id, 'numchildren', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][12+$mod]);
				update_post_meta($post_id, 'parent', $GLOBALS['zp_session'][$api_user_id]['items']['query_params'][13+$mod]);
			}
        }*/
        
        if ($not_done) // reset everything
        {
            $GLOBALS['zp_session'][$api_user_id]['items']['query_params'] = array();
            $GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries'] = 0;
        }
        else // unset everything
        {
            unset($GLOBALS['zp_session'][$api_user_id]['items']);
        }
    }
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT COLLECTIONS
    *
    ****************************************************************************************/
    
    function zp_get_collections ($wpdb, $api_user_id, $zp_start, $toplevel=false, $zp_collection=false, $zp_single=false)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
		$zp_collection_keys = "";
		
        // Build request URL
		if ( $toplevel === true ) $toplevel = '/top'; else $toplevel = '';
		if ( $zp_single ) $zp_single = '/'.$zp_single; else $zp_single = '';
		if ( $zp_collection ) $zp_collection = '/'.$zp_collection.'/collections'; else $zp_collection = '';
        
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections".$toplevel.$zp_collection.$zp_single."?";
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "") $zp_import_url .= "key=".$zp_account[0]->public_key."&";
        $zp_import_url .= "limit=50&start=".$zp_start;
		
        // Grab contents
		$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($GLOBALS['zp_session'][$api_user_id]['collections']['last_set']) )
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['zp_session'][$api_user_id]['collections']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['zp_session'][$api_user_id]['collections']['last_set'] = 0;
                    }
                }
            }
        }
		
        
        
        // GET COLLECTION META
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            $parent = "";
            
            // Get parent collection
            foreach($entry->getElementsByTagName("link") as $link)
            {
                if ($link->attributes->getNamedItem("rel")->nodeValue == "up")
                {
                    $parent_temp = explode("/", $link->attributes->getNamedItem("href")->nodeValue);
                    $parent = $parent_temp[count($parent_temp)-1];
                }
            }
            
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $numCollections = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numCollections")->item(0)->nodeValue;
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($zp_import_contents);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            
            // GET LIST OF ITEM KEYS - now dealt with in Items import
//            $zp_import_contents = new ZotpressRequest();
//            
//            // Build request URL
//            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections/".$item_key."/items?format=keys";
//            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "") $zp_import_url .= "&key=".$zp_account[0]->public_key;
//            
//            // Import item keys
//			$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
//            
//            $zp_collection_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
//            
//            unset($zp_import_contents);
//            unset($zp_import_url);
//            unset($zp_xml);
            
            
            
            // Prep for insert into db
            array_push($GLOBALS['zp_session'][$api_user_id]['collections']['query_params'],
				$zp_account[0]->api_user_id,
				zp_db_prep($title),
				zp_db_prep($retrieved),
				zp_db_prep($parent),
				$item_key,
				$numCollections,
				$numItems
			);
			
			$zp_collection_keys .= $item_key . ",";
            
            $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries']++;
            
            unset($title);
            unset($retrieved);
            unset($parent);
            unset($item_key);
            unset($numCollections);
            unset($numItems);
            //unset($zp_collection_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['zp_session'][$api_user_id]['collections']['last_set'] == $zp_start)
        {
            return array( "continue" => false, "collections" => rtrim( $zp_collection_keys, "," ) );
        }
        else // continue to next set of collections
        {
            return array( "continue" => true, "collections" => rtrim( $zp_collection_keys, "," ) );
        }
        
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_collections
    
    
    
    function zp_save_collections ($wpdb, $api_user_id, $not_done=false, $selective=false)
    {
        if ($GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] > 0)
        {
            $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroCollections
                    ( api_user_id, title, retrieved, parent, item_key, numCollections, numItems )
                    VALUES ( %s, %s, %s, %s, %s, %d, %d )".str_repeat(", ( %s, %s, %s, %s, %s, %d, %d )", $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries']-1), 
                $GLOBALS['zp_session'][$api_user_id]['collections']['query_params']
            ) );
            
            $wpdb->flush();
        }
        /*if ($GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] > 0)
        {
			for ($i = 0; $i <= ($GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] - 1); $i++ )
			{
				$mod = $i * 8;
				
				$collection_id = get_term_by( "name", $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][1+$mod], 'zp_collections', 'ARRAY_A' );
				if ( $collection_id === false ) $collection_id = wp_insert_term( $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][1+$mod], 'zp_collections', array( 'description' => $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][4+$mod] ) ); // description is collection key
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-api_user_id' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-api_user_id' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-api_user_id', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][0+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-retrieved' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-retrieved' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-retrieved', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][2+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-parent' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-parent' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-parent', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][3+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-item_key' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-item_key' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-item_key', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][4+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-numCollections' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-numCollections' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-numCollections', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][5+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-numItems' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-numItems' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-numItems', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][6+$mod], '', false );
				
				if ( get_option( 'zp_collection-'.$collection_id['term_id'].'-items' ) ) delete_option( 'zp_collection-'.$collection_id['term_id'].'-items' );
				add_option( 'zp_collection-'.$collection_id['term_id'].'-items', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][7+$mod], '', false );
				
				// Link collections to entries
				if ( trim($GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][7+$mod]) != "" )
				{
					foreach ( explode(',', $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][7+$mod]) as $zp_entry )
					{
						$zp_entry = get_posts( array( 'meta_key' => 'item_key', 'meta_value' => $zp_entry, 'post_type' => 'zp_entry' ) );
						wp_set_object_terms( $zp_entry[0]->ID, $collection_id['term_id'], 'zp_collections' );
					}
				}
			}
        }*/
        
		if (!$selective)
		{
			if ($not_done) // reset everything
			{
				$GLOBALS['zp_session'][$api_user_id]['collections']['query_params'] = array();
				$GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
			}
			else // unset everything
			{
				unset($GLOBALS['zp_session'][$api_user_id]['collections']);
			}
		}
    }
	
	
	
    /*function zp_link_collections ($wpdb, $api_user_id)
    {
		delete_option('zp_collections_children');
		$collection_terms = get_terms( 'zp_collections', array( 'hide_empty' => false ) );
		
		// Find parent, if exists
		foreach ($collection_terms as $collection_term)
		{
			if ( strlen(trim(get_option( 'zp_collection-'.$collection_term->term_id.'-parent' ))) > 0 )
			{
				foreach ($collection_terms as $collection_term_parent)
				{
					if ( get_option( 'zp_collection-'.$collection_term_parent->term_id.'-item_key' ) ==
							get_option( 'zp_collection-'.$collection_term->term_id.'-parent' ) )
					{
						wp_update_term( $collection_term->term_id, 'zp_collections',
							array(
								'parent' => $collection_term_parent->term_id
							)
						);
					}
				}
			}
		}
	}*/
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT TAGS
    *
    ****************************************************************************************/
    
    function zp_get_tags ($wpdb, $api_user_id, $zp_start, $zp_collection=false)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
        
        // Get import url
		if ( $zp_collection ) $zp_collection = '/collections/'.$zp_collection; else $zp_collection = '';
		
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id.$zp_collection."/tags?limit=50&start=".$zp_start;
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
            $zp_import_url .= "&key=".$zp_account[0]->public_key;
        
        // Import content
		$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($GLOBALS['zp_session'][$api_user_id]['tags']['last_set']))
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['zp_session'][$api_user_id]['tags']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['zp_session'][$api_user_id]['tags']['last_set'] = 0;
                    }
                }
            }
        }
        
        
        // PREPARE EACH ENTRY FOR DB INSERT
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($zp_import_contents);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            // GET LIST OF ITEM KEYS - now handled in Items import
//            $zp_import_contents = new ZotpressRequest();
//            
//            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags/".urlencode($title)."/items?format=keys";
//            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
//                $zp_import_url .= "&key=".$zp_account[0]->public_key;
//            
//            // Import content
//			$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
//            
//            $zp_tag_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
//            
//            unset($zp_import_contents);
//            unset($zp_import_url);
//            unset($zp_xml);
            
            
            // Prep for insert into db
            array_push($GLOBALS['zp_session'][$api_user_id]['tags']['query_params'],
                    $zp_account[0]->api_user_id,
                    zp_db_prep($title),
                    zp_db_prep($retrieved),
                    $numItems
				);
            
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries']++;
            
            unset($title);
            unset($retrieved);
            unset($numItems);
            //unset($zp_tag_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['zp_session'][$api_user_id]['tags']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of collections
        {
            return true;
        }
        
        unset($entries);
        unset($doc_citations);
        
    } // FUNCTION: zp_get_tags
    
    
    
    function zp_save_tags ($wpdb, $api_user_id, $not_done=false)
    {
        if ($GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries'] > 0)
        {
            $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroTags
                    ( api_user_id, title, retrieved, numItems )
                    VALUES ( %s, %s, %s, %d )".str_repeat(", ( %s, %s, %s, %d )", $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries']-1) ."
					ON DUPLICATE KEY UPDATE
					api_user_id = VALUES(api_user_id),
					title = VALUES(title),
					retrieved = VALUES(retrieved),
					numItems = VALUES(numItems)
				", 
                $GLOBALS['zp_session'][$api_user_id]['tags']['query_params']
            ) );
            
            $wpdb->flush();
        }
        /*if ($GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries'] > 0)
        {
			for ($i = 0; $i <= ($GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries'] - 1); $i++ )
			{
				$mod = $i * 5;
				
				$tag_id = get_term_by( "name", $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][1+$mod], 'zp_tags', 'ARRAY_A' );
				if ( $tag_id === false ) $tag_id = wp_insert_term( $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][1+$mod], 'zp_tags', array( 'description' => $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'][4+$mod] ) ); // description is list of items (note: different from collections, where description is collection key)
				
				if ( get_option( 'zp_tag-'.$tag_id['term_id'].'-api_user_id' ) ) delete_option( 'zp_tag-'.$tag_id['term_id'].'-api_user_id' );
				add_option( 'zp_tag-'.$tag_id['term_id'].'-api_user_id', $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][0+$mod], '', false );
				
				if ( get_option( 'zp_tag-'.$tag_id['term_id'].'-retrieved' ) ) delete_option( 'zp_tag-'.$tag_id['term_id'].'-retrieved' );
				add_option( 'zp_tag-'.$tag_id['term_id'].'-retrieved', $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][2+$mod], '', false );
				
				//if ( get_option( 'zp_tag-'.$tag_id['term_id'].'-numItems' ) ) delete_option( 'zp_tag-'.$tag_id['term_id'].'-numItems' );
				//add_option( 'zp_tag-'.$tag_id['term_id'].'-numItems', $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][3+$mod], '', false );
				
				if ( get_option( 'zp_tag-'.$tag_id['term_id'].'-items' ) ) delete_option( 'zp_tag-'.$tag_id['term_id'].'-items' );
				add_option( 'zp_tag-'.$tag_id['term_id'].'-items', $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][4+$mod], '', false );
				
				// Link tags to entries
				if ( trim($GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][4+$mod]) != "" )
				{
					foreach ( explode(',', $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'][4+$mod]) as $zp_entry )
					{
						$zp_entry = get_posts( array( 'meta_key' => 'item_key', 'meta_value' => $zp_entry, 'post_type' => 'zp_entry' ) );
						wp_set_object_terms( $zp_entry[0]->ID, $tag_id['term_id'], 'zp_tags', true );
					}
				}
			}
        }*/
        
        if ($not_done) // reset everything
        {
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'] = array();
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries'] = 0;
        }
        else // unset everything
        {
            unset($GLOBALS['zp_session'][$api_user_id]['tags']);
        }
    }



?>