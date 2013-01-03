<?php
    
    
    
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
    
    
    
    function zp_get_local_items ($wpdb, $zp_account, $limit=0)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_account[0]->api_user_id."'";
        if ($limit > 0)
            $query .= " LIMIT ".$limit;
        
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
    
    
    
    function zp_get_server_items ($wpdb, $zp_account, $nokey, $zp_all_itemkeys_count, $zp_local_items, $limit=0)
    {
        $zpi = 0;
        $zp_items_to_update = array();
        $query_total_items_to_add = 0;
        $zp_items_to_add = array();
        
        
        // DEBUGGING:
        if ($limit > 0) {
            $zp_all_itemkeys_count = $limit;
            echo "Server total: " . $zp_all_itemkeys_count . "<br />n";
            echo "Local total: " . count($zp_local_items) . "<br /><br />\n\n";
        }
        
        
        // Query each group at Zotero
        while ($zpi < $zp_all_itemkeys_count)
        {
            $zp_import_curl = new CURL();
            
            // See if default exists
            $zp_default_style = "apa";
            if (get_option("Zotpress_DefaultStyle"))
                $zp_default_style = get_option("Zotpress_DefaultStyle");
            
            if ($nokey === true)
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?";
            else // normal with key
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?key=".$zp_account[0]->public_key."&";
            $zp_import_url .= "format=atom&content=json,bib&style=".$zp_default_style."&limit=50&start=".$zpi;
            
            // Read the external data
            if (in_array ('curl', get_loaded_extensions()))
                $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
            else // Use the old way:
                $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
            
            // Make it DOM-traversable 
            $doc_citations = new DOMDocument();
            $doc_citations->loadXML($zp_xml);
            
            $entries = $doc_citations->getElementsByTagName("entry");
            
            
            // COMPARE EACH ENTRY TO LOCAL
            // Entries can be items or attachments (e.g. notes)
            
            foreach ($entries as $entry)
            {
                $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
                $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
                
                // Check to see if item key exists in local
                if (array_key_exists( $item_key, $zp_local_items ))
                {
                    // Check to see if it needs updating
                    if ($retrieved != $zp_local_items[$item_key]->retrieved)
                    {
                        $zp_items_to_update[$item_key] = $zp_local_items[$item_key]->id;
                        unset($zp_local_items[$item_key]); // Leave only the local ones that should be deleted
                    }
                    else // ignore
                    {
                        unset($zp_local_items[$item_key]); // Leave only the local ones that should be deleted
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
                $date = "";
                $year = "";
                $title = "";
                $numchildren = 0;
                $parent = "";
                $link_mode = "";
                
                if (count($json_content_decoded->creators) > 0)
                    foreach ( $json_content_decoded->creators as $creator )
                        $author .= $creator->lastName . ", ";
                else
                    $author .= $creator->creators["lastName"] . ", ";
                
                $author = substr ($author, 0, strlen($author)-2);
                
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
                if (array_key_exists( $item_key, $zp_items_to_update ))
                {
                    $zp_items_to_update[$item_key] = array (
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
                else if (!array_key_exists( $item_key, $zp_local_items ))
                {
                    array_push($zp_items_to_add,
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
                    $query_total_items_to_add++;
                }
                
            } // entry
            
            unset($zp_import_curl);
            unset($zp_import_url);
            unset($zp_xml);
            unset($doc_citations);
            unset($entries);
            
            // Move to the next set
            $zpi += 50;
            
        } // while loop - every 50 items
        
        
        // RUN QUERIES: UPDATE
        
        if (count($zp_items_to_update) > 0)
        {
            foreach ($zp_items_to_update as $item_params)
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
        
        if (count($zp_items_to_add) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroItems 
                    ( api_user_id, item_key, retrieved, json, author, zpdate, year, title, itemType, linkMode, citation, style, numchildren, parent )
                    VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )", $query_total_items_to_add-1), 
                $zp_items_to_add
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if (count($zp_local_items) > 0)
        {
            foreach ($zp_local_items as $item_params)
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
        
        unset($zp_items_to_update);
        unset($query_total_items_to_add);
        unset($zp_items_to_add);
        unset($zp_local_items);
        
    } // FUNCTION: zp_get_server_items
    
    
    
    function zp_get_local_collections ($wpdb, $zp_account, $limit=0)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$zp_account[0]->api_user_id."'";
        if ($limit > 0)
            $query .= " LIMIT ".$limit;
        
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
    
    
    
    function zp_get_server_collections ($wpdb, $zp_account, $nokey, $zp_local_collections, $limit=0)
    {
        $zp_collections_to_update = array();
        $zp_collections_to_add = array();
        $query_total_collections_to_add = 0;
        
        
        // IMPORT COLLECTIONS
        
        $zp_import_curl = new CURL();
        
        if ($nokey === true)
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?limit=50";
        else
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?key=".$zp_account[0]->public_key."&limit=50";
        
        if (in_array ('curl', get_loaded_extensions()))
            $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
        else // Use the old way:
            $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
        
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        // Get request pages to loop through
        $max_page = "";
        $current_page = 0;
        $links = $doc_citations->getElementsByTagName("link");
        
        foreach ($links as $link)
        {
            if ($link->getAttribute('rel') == "last") {
                $max_page = explode("start=", $link->getAttribute('href'));
                $max_page = intval($max_page[1])+50;
                break;
            }
        }
        
        while ($current_page != $max_page)
        {
            // PREPARE EACH ENTRY FOR DB INSERT
            
            $entries = $doc_citations->getElementsByTagName("entry");
            
            foreach ($entries as $entry)
            {
                $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
                $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
                
                // Check to see if item key exists in local
                if (array_key_exists( $item_key, $zp_local_collections ))
                {
                    // Check to see if it needs updating
                    if ($retrieved != $zp_local_collections[$item_key]->retrieved)
                    {
                        $zp_collections_to_update[$item_key] = $zp_local_collections[$item_key]->id;
                        unset($zp_local_collections[$item_key]); // Leave only the local ones that should be deleted
                    }
                    else // ignore
                    {
                        unset($zp_local_collections[$item_key]); // Leave only the local ones that should be deleted
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
                
                unset($zp_import_curl);
                unset($zp_import_url);
                unset($zp_xml);
                
                
                
                // GET LIST OF ITEM KEYS
                $zp_import_curl = new CURL();
                
                if ($nokey === true)
                    $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections/".$item_key."/items?format=keys";
                else
                    $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections/".$item_key."/items?key=".$zp_account[0]->public_key."&format=keys";
                
                // Import depending on method: cURL or file_get_contents
                if (in_array ('curl', get_loaded_extensions()))
                    $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
                else // Use the old way:
                    $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
                
                $zp_collection_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
                
                
                
                // If item key needs updating
                if (array_key_exists( $item_key, $zp_collections_to_update ))
                {
                    $zp_collections_to_update[$item_key] = array (
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
                else if (!array_key_exists( $item_key, $zp_local_collections ))
                {
                    array_push($zp_collections_to_add,
                        $zp_account[0]->api_user_id,
                        zp_db_prep($title),
                        zp_db_prep($retrieved),
                        $parent,
                        $item_key,
                        $numCollections,
                        $numItems,
                        zp_db_prep($zp_collection_itemkeys)
                        );
                    $query_total_collections_to_add++;
                }
                
            } // entry
            
            
            // MOVE ON TO THE NEXT REQUEST PAGE
            
            $current_page += 50;
            $zp_import_curl = new CURL();
            
            if ($nokey === true)
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?limit=50&start=$current_page";
            else
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?key=".$zp_account[0]->public_key."&limit=50&start=$current_page";
            
            if (in_array ('curl', get_loaded_extensions()))
                $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
            else // Use the old way:
                $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
            
            // Make it DOM-traversable 
            $doc_citations = new DOMDocument();
            $doc_citations->loadXML($zp_xml);
            
        } // while
        
        
        // RUN QUERIES: UPDATE
        
        if (count($zp_collections_to_update) > 0)
        {
            foreach ($zp_collections_to_update as $item_params)
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
        
        if (count($zp_collections_to_add) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."zotpress_zoteroCollections
                        ( api_user_id, title, retrieved, parent, item_key, numCollections, numItems, listItems )
                        VALUES ( %s, %s, %s, %s, %s, %d, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %d, %d, %s )", $query_total_collections_to_add-1), 
                $zp_collections_to_add
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if (count($zp_local_collections) > 0)
        {
            foreach ($zp_local_collections as $item_params)
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
        
        unset($zp_collections_to_update);
        unset($query_total_collections_to_add);
        unset($zp_collections_to_add);
        unset($zp_local_collections);
        unset($zp_import_query);
        unset($zp_import_curl);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_server_collections    
    
    
    
    function zp_get_local_tags ($wpdb, $zp_account, $limit=0)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$zp_account[0]->api_user_id."'";
        if ($limit > 0)
            $query .= " LIMIT ".$limit;
        
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
    
    
    
    function zp_get_server_tags ($wpdb, $zp_account, $nokey, $zp_local_tags, $limit=0)
    {
        $zp_tags_to_update = array();
        $zp_tags_to_add = array();
        $query_total_tags_to_add = 0;
        
        
        // Get first 50 tags
        $zp_import_curl = new CURL();
        
        if ($nokey === true)
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?limit=50";
        else
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?key=".$zp_account[0]->public_key."&limit=50";
        
        if (in_array ('curl', get_loaded_extensions()))
            $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
        else // Use the old way:
            $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        // Get request pages to loop through
        $max_page = "";
        $current_page = 0;
        $links = $doc_citations->getElementsByTagName("link");
        
        foreach ($links as $link)
        {
            if ($link->getAttribute('rel') == "last") {
                $max_page = explode("start=", $link->getAttribute('href'));
                $max_page = intval($max_page[1])+50;
                break;
            }
        }
        
        while ($current_page != $max_page)
        {
            $entries = $doc_citations->getElementsByTagName("entry");
            
            foreach ($entries as $entry)
            {
                $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
                $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
                
                // Check to see if tags exists in local
                if (array_key_exists( trim($title), $zp_local_tags ))
                {
                    // Check to see if it needs updating
                    if ($retrieved != $zp_local_tags[trim($title)]->retrieved)
                    {
                        $zp_tags_to_update[trim($title)] = $zp_local_tags[trim($title)]->id;
                        unset($zp_local_tags[trim($title)]); // Leave only the local ones that should be deleted
                    }
                    else // ignore
                    {
                        unset($zp_local_tags[trim($title)]); // Leave only the local ones that should be deleted
                        continue;
                    }
                }
                
                $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
                
                unset($zp_import_curl);
                unset($zp_import_url);
                unset($zp_xml);
                
                
                
                // GET LIST OF ITEM KEYS
                $zp_import_curl = new CURL();
                
                if ($nokey === true)
                    $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags/".urlencode($title)."/items?format=keys";
                else
                    $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags/".urlencode($title)."/items?key=".$zp_account[0]->public_key."&format=keys";
                
                // Import depending on method: cURL or file_get_contents
                if (in_array ('curl', get_loaded_extensions()))
                    $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
                else // Use the old way:
                    $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
                
                $zp_tag_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
                
                
                
                // If item key needs updating
                if (array_key_exists( trim($title), $zp_tags_to_update ))
                {
                    $zp_tags_to_update[trim($title)] = array (
                            "api_user_id" => $zp_account[0]->api_user_id,
                            "title" => zp_db_prep($title),
                            "retrieved" => zp_db_prep($retrieved),
                            "numItems" => $numItems,
                            "listItems" => zp_db_prep($zp_tag_itemkeys)
                            );
                }
                // If item key isn't in local, add it
                else if (!array_key_exists( trim($title), $zp_local_tags ))
                {
                    array_push($zp_tags_to_add,
                        $zp_account[0]->api_user_id,
                        zp_db_prep($title),
                        zp_db_prep($retrieved),
                        $numItems,
                        zp_db_prep($zp_tag_itemkeys)
                        );
                    $query_total_tags_to_add++;
                }
                
                unset($title);
                unset($retrieved);
                unset($numItems);
                unset($zp_tag_itemkeys);
                
            } // entry
            
            
            // MOVE ON TO THE NEXT REQUEST PAGE
            
            $current_page += 50;
            $zp_import_curl = new CURL();
            
            if ($nokey === true)
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?limit=50&start=$current_page";
            else
                $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?key=".$zp_account[0]->public_key."&limit=50&start=$current_page";
            
            if (in_array ('curl', get_loaded_extensions()))
                $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
            else // Use the old way:
                $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
            
            // Make it DOM-traversable 
            $doc_citations = new DOMDocument();
            $doc_citations->loadXML($zp_xml);
            
        } // while page
        
        
        // RUN QUERIES: UPDATE
        
        if (count($zp_tags_to_update) > 0)
        {
            foreach ($zp_tags_to_update as $item_params)
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
        
        if (count($zp_tags_to_add) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."zotpress_zoteroTags
                        ( api_user_id, title, retrieved, numItems, listItems )
                        VALUES ( %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %d, %s )", $query_total_tags_to_add-1), 
                $zp_tags_to_add
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if (count($zp_local_tags) > 0)
        {
            foreach ($zp_local_tags as $item_params)
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
        
        unset($zp_tags_to_update);
        unset($query_total_tags_to_add);
        unset($zp_tags_to_add);
        unset($zp_local_tags);
        unset($zp_import_query);
        unset($zp_import_curl);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_server_tags



?>