<?php
    
    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS BASIC SYNC FUNCTIONS
    *
    ****************************************************************************************/
    
    function zp_autoupdate()
    {
	// Get interval
	$zp_default_autoupdate = "weekly";
	if (get_option("Zotpress_DefaultAutoUpdate"))
	    $zp_default_autoupdate = get_option("Zotpress_DefaultAutoUpdate");
        
        // Get last update date
        $zp_last_autoupdate = date('Y-m-d-');
        if (get_option("Zotpress_LastAutoUpdate"))
            $zp_last_autoupdate= get_option("Zotpress_LastAutoUpdate");
        
        // Find difference
        $diff_in_days = intval( floor((strtotime(date('Y-m-d')) - strtotime($zp_last_autoupdate))/3600/24) );
        
        $to_update_or_not = false;
        
        // Determine whether to update
        if (($zp_default_autoupdate == "weekly" && $diff_in_days > 7) ||
                ($zp_default_autoupdate == "daily" && $diff_in_days > 1))
            $to_update_or_not = true;
        
	return $to_update_or_not;
    }
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS SYNC ITEMS
    *
    ****************************************************************************************/
    
    function zp_get_local_items ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set item key as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[$item->item_key] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function zp_get_server_items ($wpdb, $api_user_id, $zp_start)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
        //$zp_account = $GLOBALS['zp_session'][$api_user_id]['zp_account'];
        
        
        // See if default exists
        $zp_default_style = "apa";
        if (get_option("Zotpress_DefaultStyle"))
            $zp_default_style = get_option("Zotpress_DefaultStyle");
        
        // Build request URL
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$api_user_id."/items?";
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
            $zp_import_url .= "key=".$zp_account[0]->public_key."&";
        $zp_import_url .= "format=atom&content=json,bib&style=".$zp_default_style."&limit=50&start=".$zp_start;
        //var_dump($zp_import_url);
        
        // Read the external data
	$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
        
        // Stop in our tracks if there's a request error
        if ($zp_import_contents->request_error)
            return $zp_import_contents->request_error;
        
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
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        
        // COMPARE EACH ENTRY TO LOCAL
        // Entries can be items or attachments (e.g. notes)
        
        foreach ($entries as $entry)
        {
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if item key exists in local
            if (array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'][$item_key]->retrieved)
                {
                    $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update'][$item_key] = $GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'][$item_key]->id;
                    //unset($GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            // Item key doesn't exist in local, or needs updating, so collect metadata and add
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
                foreach ( $json_content_decoded->creators as $creator )
                    if ($creator->creatorType == "author")
                        $author .= $creator->lastName . ", ";
                    else
                        $author_other .= $creator->lastName . ", ";
            else
                $author .= $creator->creators["lastName"];
            
            // Determine if we use author or other author type
            if (trim($author) == "")
                $author = $author_other;
            
            // Remove last comma
            $author = preg_replace('~(.*)' . preg_quote(', ', '~') . '~', '$1' . '', $author, 1);
            
            $date = $json_content_decoded->date;
            $year = zp_extract_year($date);
            
            if (trim($year) == "")
                $year = "1977";
            
            $title = $json_content_decoded->title;
            
            $numchildren = intval($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue);
            
            // DOWNLOAD: Find URL
            if ($item_type == "attachment")
            {
                if (isset($json_content_decoded->linkMode))
                    $link_mode = $json_content_decoded->linkMode;
            }
            
            // PARENT
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
            
            
            // If item key needs updating
            if (array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update'] ))
            {
                $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update'][$item_key] = array (
                        "api_user_id" => $zp_account[0]->api_user_id,
                        "item_key" => $item_key,
                        "retrieved" => zp_db_prep($retrieved),
                        "json" => zp_db_prep($json_content),
                        "author" => zp_db_prep($author),
                        "zpdate" => zp_db_prep($date),
                        "year" => zp_db_prep($year),
                        "title" => zp_db_prep($title),
                        "itemType" => $item_type,
                        "linkMode" => $link_mode,
                        "citation" => zp_db_prep($citation_content),
                        "style" => zp_db_prep($zp_default_style),
                        "numchildren" => $numchildren,
                        "parent" => $parent);
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'] ))
            {
                array_push($GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_add'],
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
                
                $GLOBALS['zp_session'][$api_user_id]['items']['query_total_items_to_add']++;
            }
            
        } // foreach entry
        
        // LAST ITEM
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
        
    } // FUNCTION: zp_get_server_items
    
    
    
    function zp_save_synced_items ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update']) > 0)
        {
            foreach ($GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'zotpress_zoteroItems', 
                    $item_params, 
                    array( 'item_key' => $item_params["item_key"], 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        if (count($GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroItems 
                    ( api_user_id, item_key, retrieved, json, author, zpdate, year, title, itemType, linkMode, citation, style, numchildren, parent )
                    VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )", $GLOBALS['zp_session'][$api_user_id]['items']['query_total_items_to_add']-1), 
                $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE: Only at the last set
        
        if ($done && count($GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items']) > 0)
        {
            $zp_delete_items = explode(",", get_option('ZOTPRESS_DELETE_'.$api_user_id));
            
            foreach ($zp_delete_items as $item_params)
            //foreach ($GLOBALS['zp_session'][$api_user_id]['items']['zp_local_items'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems
                        WHERE item_key = %s
                        AND api_user_id = %s
                        ",
                        $item_params->item_key, $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['zp_session'][$api_user_id]['items']);
        }
        else // reset add and update
        {
            $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_add'] = array();
            $GLOBALS['zp_session'][$api_user_id]['items']['zp_items_to_update'] = array();
            $GLOBALS['zp_session'][$api_user_id]['items']['query_total_items_to_add'] = 0;
        }
        
    } // FUNCTION: zp_save_synced_items
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS SYNC COLLECTIONS
    *
    ****************************************************************************************/
    
    function zp_get_local_collections ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set item key as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[$item->item_key] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function zp_get_server_collections ($wpdb, $api_user_id, $zp_start)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
        //$zp_account = $GLOBALS['zp_session'][$api_user_id]['zp_account'];
        
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?limit=50&start=".$zp_start;
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
            $zp_import_url .= "&key=".$zp_account[0]->public_key;
        
	$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
        
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        // Get last set
        if (!isset($GLOBALS['zp_session'][$api_user_id]['collections']['last_set']))
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
        
        
        // PREPARE EACH ENTRY FOR DB INSERT
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if item key exists in local
            if (array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'][$item_key]->retrieved)
                {
                    $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update'][$item_key] = $GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'][$item_key]->id;
                    //unset($GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
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
            
            $numCollections = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numCollections")->item(0)->nodeValue;
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($zp_import_contents);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            
            // GET LIST OF ITEM KEYS
            $zp_import_contents = new ZotpressRequest();
            
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections/".$item_key."/items?format=keys";
            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
                $zp_import_url .= "&key=".$zp_account[0]->public_key;
            
            // Import content
	    $zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
            
            $zp_collection_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
            
            
            
            // If item key needs updating
            if (array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update'] ))
            {
                $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update'][$item_key] = array (
                        "api_user_id" => $zp_account[0]->api_user_id,
                        "title" => zp_db_prep($title),
                        "retrieved" => zp_db_prep($retrieved),
                        "parent" => $parent,
                        "item_key" => $item_key,
                        "numCollections" => $numCollections,
                        "numItems" => $numItems,
                        "listItems" => zp_db_prep($zp_collection_itemkeys)
                        );
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( $item_key, $GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'] ))
            {
                array_push($GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_add'],
                    $zp_account[0]->api_user_id,
                    zp_db_prep($title),
                    zp_db_prep($retrieved),
                    $parent,
                    $item_key,
                    $numCollections,
                    $numItems,
                    zp_db_prep($zp_collection_itemkeys)
                    );
                $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_collections_to_add']++;
            }
            
            unset($title);
            unset($retrieved);
            unset($parent);
            unset($item_key);
            unset($numCollections);
            unset($numItems);
            unset($zp_collection_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['zp_session'][$api_user_id]['collections']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of collections
        {
            return true;
        }
        
        unset($zp_import_contents);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_server_collections
    
    
    
    function zp_save_synced_collections ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update']) > 0)
        {
            foreach ($GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'zotpress_zoteroCollections', 
                    $item_params, 
                    array( 'item_key' => $item_params["item_key"], 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        
        if (count($GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."zotpress_zoteroCollections
                        ( api_user_id, title, retrieved, parent, item_key, numCollections, numItems, listItems )
                        VALUES ( %s, %s, %s, %s, %s, %d, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %d, %d, %s )", $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_collections_to_add']-1), 
                $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if ($done && count($GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections']) > 0)
        {
            $zp_delete_items = explode(",", get_option('ZOTPRESS_DELETE_'.$api_user_id));
            
            foreach ($zp_delete_items as $item_params)
            //foreach ($GLOBALS['zp_session'][$api_user_id]['collections']['zp_local_collections'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."zotpress_zoteroCollections 
                        WHERE item_key = %s
                        AND api_user_id = %s
                        ",
                        $item_params->item_key, $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['zp_session'][$api_user_id]['collections']);
        }
        else // reset add and update
        {
            $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_update'] = array();
            $GLOBALS['zp_session'][$api_user_id]['collections']['zp_collections_to_add'] = array();
            $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_collections_to_add'] = 0;
        }
        
    } // FUNCTION: zp_save_synced_collections

    
    
    /****************************************************************************************
    *
    *     ZOTPRESS SYNC TAGS
    *
    ****************************************************************************************/
    
    function zp_get_local_tags ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set title as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[($item->title)] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function zp_get_server_tags ($wpdb, $api_user_id, $zp_start)
    {
        $zp_import_contents = new ZotpressRequest();
        $zp_account = zp_get_account($wpdb, $api_user_id);
        //$zp_account = $GLOBALS['zp_session'][$api_user_id]['zp_account'];
        
        // Build request URL
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?limit=50&start=".$zp_start;
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
            $zp_import_url .= "&key=".$zp_account[0]->public_key;
        
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
        
       $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if tags exists in local
            if (array_key_exists( trim($title), $GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'][trim($title)]->retrieved)
                {
                    $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update'][trim($title)] = $GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'][trim($title)]->id;
                    //unset($GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'][trim($title)]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'][trim($title)]); // Leave only the local ones that should be deleted
                    update_option('ZOTPRESS_DELETE_'.$api_user_id, get_option('ZOTPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($zp_import_contents);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            
            // GET LIST OF ITEM KEYS
            $zp_import_contents = new ZotpressRequest();
            
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags/".urlencode($title)."/items?format=keys";
            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
                $zp_import_url .= "&key=".$zp_account[0]->public_key;
            
            // Import content
	    $zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, false );
            
            $zp_tag_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
            
            
            
            // If item key needs updating
            if (array_key_exists( trim($title), $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update'] ))
            {
                $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update'][trim($title)] = array (
                        "api_user_id" => $zp_account[0]->api_user_id,
                        "title" => zp_db_prep($title),
                        "retrieved" => zp_db_prep($retrieved),
                        "numItems" => $numItems,
                        "listItems" => zp_db_prep($zp_tag_itemkeys)
                        );
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( trim($title), $GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'] ))
            {
                array_push($GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_add'],
                    $zp_account[0]->api_user_id,
                    zp_db_prep($title),
                    zp_db_prep($retrieved),
                    $numItems,
                    zp_db_prep($zp_tag_itemkeys)
                    );
                $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_tags_to_add']++;
            }
            
            unset($title);
            unset($retrieved);
            unset($numItems);
            unset($zp_tag_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['zp_session'][$api_user_id]['tags']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of tags
        {
            return true;
        }
        
        unset($zp_import_contents);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_server_tags
    
    
    
    function zp_save_synced_tags ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update']) > 0)
        {
            foreach ($GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'zotpress_zoteroTags', 
                    $item_params, 
                    array( 'title' => trim($item_params["title"]), 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        
        if (count($GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."zotpress_zoteroTags
                        ( api_user_id, title, retrieved, numItems, listItems )
                        VALUES ( %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %d, %s )", $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_tags_to_add']-1), 
                $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if ($done && count($GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags']) > 0)
        {
            $zp_delete_items = explode(",", get_option('ZOTPRESS_DELETE_'.$api_user_id));
            
            foreach ($zp_delete_items as $item_params)
            //foreach ($GLOBALS['zp_session'][$api_user_id]['tags']['zp_local_tags'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags 
                        WHERE title = %s
                        AND api_user_id = %s
                        ",
                        trim($item_params->title), $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['zp_session'][$api_user_id]['tags']);
        }
        else // reset add and update
        {
            $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_update'] = array();
            $GLOBALS['zp_session'][$api_user_id]['tags']['zp_tags_to_add'] = array();
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_tags_to_add'] = 0;
        }
        
    } // FUNCTION: zp_save_synced_tags



?>