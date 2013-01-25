<?php
    
    

    /****************************************************************************************
    *
    *     ZOTPRESS BASIC IMPORT FUNCTIONS
    *
    ****************************************************************************************/
    
    function zp_db_prep($input)
    {
        $input = str_replace("%", "%%", $input);
        return ($input);
    }
    
    
    
    function zp_extract_year($date)
    {
	preg_match_all( '/(\d{4})/', $date, $matches );
	return $matches[0][0];
    }
    
    
    
    function zp_set_update_time( $time )
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


    // REMOVE FROM EVERYWHERE
    //function zp_get_account_haskey ($zp_account)
    //{
    //    $nokey = true;
    //    
    //    // Key-less or not
    //    if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
    //        $nokey = false;
    //    
    //    return $nokey;
    //}
    
    
    
    function zp_clear_last_import ($wpdb, $api_user_id, $step)
    {
        switch ($step)
        {
            case "items":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."'");
                break;
            case "collections":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."'");
                break;
            case "tags":
                $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."'");
                break;
        }
    }
    
    
    
    //function zp_get_item_count ($api_user_id) // UNUSED?
    //{
    //    $zp_import_curl = new CURL();
    //    $zp_account = $_SESSION['zp_session'][$api_user_id]['zp_account'];
    //    
    //    // If there's no key, it's a group account
    //    if (!is_null($zp_account[0]->public_key) && trim($zp_account[0]->public_key) != "") {
    //        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?key=".$zp_account[0]->public_key."&format=keys";
    //    } else {
    //        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?format=keys";
    //    }
    //    
    //    // Import depending on method: cURL or file_get_contents
    //    if (in_array ('curl', get_loaded_extensions())) {
    //        $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
    //    } else {
    //        $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
    //    }
    //    
    //    $zp_all_itemkeys_count = count(array_filter(explode("\n", $zp_xml)));
    //    
    //    return $zp_all_itemkeys_count;
    //}
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT ITEMS
    *
    ****************************************************************************************/
    
    function zp_get_items ($api_user_id, $zp_start)
    {
        $zp_import_curl = new CURL();
        $zp_account = $_SESSION['zp_session'][$api_user_id]['zp_account'];
        
        
        // See if default style exists
        $zp_default_style = "apa";
        if (get_option("Zotpress_DefaultStyle"))
            $zp_default_style = get_option("Zotpress_DefaultStyle");
        
        // Build request URL
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?";
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "") { $zp_import_url .= "key=".$zp_account[0]->public_key."&"; }
        $zp_import_url .= "format=atom&content=json,bib&style=".$zp_default_style."&limit=50&start=".$zp_start;
        
        if (in_array ('curl', get_loaded_extensions()))
            $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
        else // Use the old way:
            $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($_SESSION['zp_session'][$api_user_id]['items']['last_set']))
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
                        $_SESSION['zp_session'][$api_user_id]['items']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $_SESSION['zp_session'][$api_user_id]['items']['last_set'] = 0;
                    }
                }
            }
        }
        
        
        // PREPARE EACH ENTRY FOR DB INSERT
        // Entries can be items or attachments (e.g. notes)
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
            
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
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
            // for attachments, look at zapi:subcontent zapi:type="json" - linkMode - either imported_file or linked_url
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
            
            
            // Prep for insert into db
            array_push($_SESSION['zp_session'][$api_user_id]['items']['query_params'],
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
            
            $_SESSION['zp_session'][$api_user_id]['items']['query_total_entries']++;
            
        } // foreach entry
        
        
        // LAST SET
        if ($_SESSION['zp_session'][$api_user_id]['items']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of items
        {
            return true;
        }
        
        unset($zp_import_curl);
        unset($zp_import_url);
        unset($zp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_items
    
    
    
    function zp_save_items ($wpdb, $api_user_id, $not_done=false)
    {
        if ($_SESSION['zp_session'][$api_user_id]['items']['query_total_entries'] > 0)
        {
            $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroItems
                    ( api_user_id, item_key, retrieved, json, author, zpdate, year, title, itemType, linkMode, citation, style, numchildren, parent )
                    VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )", $_SESSION['zp_session'][$api_user_id]['items']['query_total_entries']-1), 
                $_SESSION['zp_session'][$api_user_id]['items']['query_params']
            ) );
            
            $wpdb->flush();
        }
        
        if ($not_done) // reset everything
        {
            $_SESSION['zp_session'][$api_user_id]['items']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['items']['query_total_entries'] = 0;
        }
        else // unset everything
        {
            unset($_SESSION['zp_session'][$api_user_id]['items']);
        }
    }
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT COLLECTIONS
    *
    ****************************************************************************************/
    
    function zp_get_collections ($api_user_id, $zp_start)
    {
        $zp_import_curl = new CURL();
        $zp_account = $_SESSION['zp_session'][$api_user_id]['zp_account'];
        
        // Build request URL
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections?";
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "") {
            $zp_import_url .= "key=".$zp_account[0]->public_key."&";
        }
        $zp_import_url .= "limit=50&start=".$zp_start;
        
        // Grab contents
        if (in_array ('curl', get_loaded_extensions()))
            $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
        else // Use the old way:
            $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($_SESSION['zp_session'][$api_user_id]['collections']['last_set']))
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
                        $_SESSION['zp_session'][$api_user_id]['collections']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $_SESSION['zp_session'][$api_user_id]['collections']['last_set'] = 0;
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
            
            unset($zp_import_curl);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            
            // GET LIST OF ITEM KEYS
            $zp_import_curl = new CURL();
            
            // Build request URL
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/collections/".$item_key."/items?format=keys";
            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "") { $zp_import_url .= "&key=".$zp_account[0]->public_key; }
            
            // Import depending on method: cURL or file_get_contents
            if (in_array ('curl', get_loaded_extensions()))
                $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
            else // Use the old way:
                $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
            
            $zp_collection_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
            
            unset($zp_import_curl);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            
            // Prep for insert into db
            array_push($_SESSION['zp_session'][$api_user_id]['collections']['query_params'],
                    $zp_account[0]->api_user_id,
                    zp_db_prep($title),
                    zp_db_prep($retrieved),
                    zp_db_prep($parent),
                    $item_key,
                    $numCollections,
                    $numItems,
                    zp_db_prep($zp_collection_itemkeys));
            
            $_SESSION['zp_session'][$api_user_id]['collections']['query_total_entries']++;
            
            unset($title);
            unset($retrieved);
            unset($parent);
            unset($item_key);
            unset($numCollections);
            unset($numItems);
            unset($zp_collection_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($_SESSION['zp_session'][$api_user_id]['collections']['last_set'] == $zp_start)
        {
            return false;
        }
        else // continue to next set of collections
        {
            return true;
        }
        
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: zp_get_collections
    
    
    
    function zp_save_collections ($wpdb, $api_user_id, $not_done=false)
    {
        if ($_SESSION['zp_session'][$api_user_id]['collections']['query_total_entries'] > 0)
        {
            $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroCollections
                    ( api_user_id, title, retrieved, parent, item_key, numCollections, numItems, listItems )
                    VALUES ( %s, %s, %s, %s, %s, %d, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %d, %d, %s )", $_SESSION['zp_session'][$api_user_id]['collections']['query_total_entries']-1), 
                $_SESSION['zp_session'][$api_user_id]['collections']['query_params']
            ) );
            
            $wpdb->flush();
        }
        
        if ($not_done) // reset everything
        {
            $_SESSION['zp_session'][$api_user_id]['collections']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
        }
        else // unset everything
        {
            unset($_SESSION['zp_session'][$api_user_id]['collections']);
        }
    }
    
    
    
    /****************************************************************************************
    *
    *     ZOTPRESS IMPORT TAGS
    *
    ****************************************************************************************/
    
    function zp_get_tags ($api_user_id, $zp_start)
    {
        $zp_import_curl = new CURL();
        $zp_account = $_SESSION['zp_session'][$api_user_id]['zp_account'];
        
        // Get import url
        $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags?limit=50&start=".$zp_start;
        if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
            $zp_import_url .= "&key=".$zp_account[0]->public_key;
        
        // Import content
        if (in_array ('curl', get_loaded_extensions()))
            $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
        else // Use the old way:
            $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($zp_xml);
        
        
        // Get last set
        if (!isset($_SESSION['zp_session'][$api_user_id]['tags']['last_set']))
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
                        $_SESSION['zp_session'][$api_user_id]['tags']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $_SESSION['zp_session'][$api_user_id]['tags']['last_set'] = 0;
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
            
            unset($zp_import_curl);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            // GET LIST OF ITEM KEYS
            $zp_import_curl = new CURL();
            
            $zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/tags/".urlencode($title)."/items?format=keys";
            if (is_null($zp_account[0]->public_key) === false && trim($zp_account[0]->public_key) != "")
                $zp_import_url .= "&key=".$zp_account[0]->public_key;
            
            // Import depending on method: cURL or file_get_contents
            if (in_array ('curl', get_loaded_extensions()))
                $zp_xml = $zp_import_curl->get_curl_contents( $zp_import_url, false );
            else // Use the old way:
                $zp_xml = $zp_import_curl->get_file_get_contents( $zp_import_url, false );
            
            $zp_tag_itemkeys = rtrim(str_replace("\n", ",", $zp_xml), ",");
            
            unset($zp_import_curl);
            unset($zp_import_url);
            unset($zp_xml);
            
            
            // Prep for insert into db
            array_push($_SESSION['zp_session'][$api_user_id]['tags']['query_params'],
                    $zp_account[0]->api_user_id,
                    zp_db_prep($title),
                    zp_db_prep($retrieved),
                    $numItems,
                    zp_db_prep($zp_tag_itemkeys));
            
            $_SESSION['zp_session'][$api_user_id]['tags']['query_total_entries']++;
            
            unset($title);
            unset($retrieved);
            unset($numItems);
            unset($zp_tag_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($_SESSION['zp_session'][$api_user_id]['tags']['last_set'] == $zp_start)
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
        if ($_SESSION['zp_session'][$api_user_id]['tags']['query_total_entries'] > 0)
        {
            $wpdb->query( $wpdb->prepare( 
                "
                    INSERT INTO ".$wpdb->prefix."zotpress_zoteroTags
                    ( api_user_id, title, retrieved, numItems, listItems )
                    VALUES ( %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %d, %s )", $_SESSION['zp_session'][$api_user_id]['tags']['query_total_entries']-1), 
                $_SESSION['zp_session'][$api_user_id]['tags']['query_params']
            ) );
            
            $wpdb->flush();
        }
        
        if ($not_done) // reset everything
        {
            $_SESSION['zp_session'][$api_user_id]['tags']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['tags']['query_total_entries'] = 0;
        }
        else // unset everything
        {
            unset($_SESSION['zp_session'][$api_user_id]['tags']);
        }
    }



?>