<?php


    // Include shortcode functions
    require("shortcode.functions.php");
    
    
    function Zotpress_func($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // depbrecated
            'userid' => false,
            'nickname' => false,
            
            'author' => false,
            'year' => false,
            
            'data_type' => false, // deprecated
            'datatype' => "items",
            
            'collection_id' => false,
            'collection' => false,
            'collections' => false,
            
            'item_key' => false,
            'item' => false,
            'items' => false,
            
            'inclusive' => "yes",
            
            'tag_name' => false,
            'tag' => false,
            'tags' => false,
            
            'content' => false, // deprecated
            'style' => false,
            'limit' => false,
            
            'sortby' => "default",
            'order' => "ASC",
            'sort' => false,
            
            'title' => "no",
            
            'image' => false,
            'showimage' => "no",
            
            'downloadable' => false,
            'download' => "no",
            
            'note' => false,
            'notes' => "no",
            
            'cite' => "no"
            
        ), $atts));
        
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id)
            $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid)
            $api_user_id = str_replace('"','',html_entity_decode($userid));
        else
            $api_user_id = false;
            
        if ($nickname)
            $nickname = str_replace('"','',html_entity_decode($nickname));
        
        // Filter by author
        $author = str_replace('"','',html_entity_decode($author));
        if (strpos($author, ",") > 0)
            $author = explode(",", $author);
        
        // Filter by year
        $year = str_replace('"','',html_entity_decode($year));
        if (strpos($year, ",") > 0)
            $year = explode(",", $year);
        
        // Format with datatype and content
        if ($data_type)
            $data_type = str_replace('"','',html_entity_decode($data_type));
        else
            $data_type = str_replace('"','',html_entity_decode($datatype));
        
        // Filter by collection
        if ($collection_id)
            $collection_id = str_replace('"','',html_entity_decode($collection_id));
        else if ($collection)
            $collection_id = str_replace('"','',html_entity_decode($collection));
        else if ($collections)
            $collection_id = str_replace('"','',html_entity_decode($collections));
        else
            $collection_id = str_replace('"','',html_entity_decode($collection));
        if (strpos($collection_id, ",") > 0)
            $collection_id = explode(",", $collection_id);
        
        // Filter by tag
        if ($tag_name)
            $tag_name = str_replace('"','',html_entity_decode($tag_name));
        else if ($tags)
            $tag_name = str_replace('"','',html_entity_decode($tags));
        else
            $tag_name = str_replace('"','',html_entity_decode($tag));
        $tag_name = str_replace("+", "", $tag_name);
        if (strpos($tag_name, ",") > 0)
            $tag_name = explode(",", $tag_name);
        
        // Filter by itemkey
        if ($item_key)
            $item_key = str_replace('"','',html_entity_decode($item_key));
        if ($items)
            $item_key = str_replace('"','',html_entity_decode($items));
        if ($item)
            $item_key = str_replace('"','',html_entity_decode($item));
        if (strpos($item_key, ",") > 0)
            $item_key = explode(",", $item_key);
        
        $content = str_replace('"','',html_entity_decode($content));
        $inclusive = str_replace('"','',html_entity_decode($inclusive));
        
        // Format style
        $style = str_replace('"','',html_entity_decode($style));
        
        // Limit
        $limit = str_replace('"','',html_entity_decode($limit));
        
        // Order / sort
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order)
            $order = str_replace('"','',html_entity_decode($order));
        else if ($sort)
            $order = str_replace('"','',html_entity_decode($sort));
        
        // Show title
        $title = str_replace('"','',html_entity_decode($title));
        if ($title == "yes" || $title == "true" || $title === true) {
            $title = true;
            $sortby = "year";
            $order= "DESC";
        }
        else {
            $title = false;
        }
        
        // Show image
        if ($showimage)
            $showimage = str_replace('"','',html_entity_decode($showimage));
        else if ($image)
            $showimage = str_replace('"','',html_entity_decode($image));
        
        if ($showimage == "yes" || $showimage == "true" || $showimage === true)
            $showimage = true;
        else
            $showimage = false;
        
        // Show download link
        if ($download)
            $download = str_replace('"','',html_entity_decode($download));
        else if ($downloadable)
            $download = str_replace('"','',html_entity_decode($downloadable));
        
        if ($download == "yes" || $download == "true" || $download === true)
            $download = true;
        else
            $download = false;
        
        // Show notes
        if ($notes)
            $notes = str_replace('"','',html_entity_decode($notes));
        else if ($note)
            $notes = str_replace('"','',html_entity_decode($note));
        
        if ($notes == "yes" || $notes == "true" || $notes === true)
            $notes = true;
        else
            $notes = false;
        
        $cite = str_replace('"','',html_entity_decode($cite));
        
        
        
        // GET ACCOUNT
        
        global $wpdb;
        
        // Get account (api_user_id)
        $zp_account = false;
        
        if ($nickname !== false)
        {
            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'", OBJECT);
            $api_user_id = $zp_account->api_user_id;
        }
        else if ($api_user_id !== false)
        {
            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
            $api_user_id = $zp_account->api_user_id;
        }
        else if ($api_user_id === false && $nickname === false)
        {
            if (get_option("Zotpress_DefaultAccount") !== false)
            {
                $api_user_id = get_option("Zotpress_DefaultAccount");
                $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id ='".$api_user_id."'", OBJECT);
            }
            else // When all else fails ...
            {
                $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
                $api_user_id = $zp_account->api_user_id;
            }
        }
        
        // Generate instance id for shortcode
        $zp_instance_id = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sortby.$order.$limit.$showimage.$download.$note.$cite);
        
        
        
        // GENERATE SHORTCODE
        
        if ($zp_account !== false)
        {
            
            
            // ITEMS
            
            if ($data_type == "items")
            {
                $zp_query = "SELECT ".$wpdb->prefix."zotpress_zoteroitems.* FROM ".$wpdb->prefix."zotpress_zoteroitems";
                
                // Filter by collection or tag
                if ($collection_id || $tag_name)
                {
                    // Set table
                    if (!$tag_name && $collection_id && $inclusive == "yes")
                        $zp_query .= ", ".$wpdb->prefix."zotpress_zoterocollections";
                    else if ($tag_name && !$collection_id && $inclusive == "yes")
                        $zp_query .= ", ".$wpdb->prefix."zotpress_zoterotags";
                    else if ($tag_name && $collection_id)
                        if ($inclusive == "yes")
                            $zp_query .= ", ".$wpdb->prefix."zotpress_zoterocollections"; // just collections to start
                        else
                            $zp_query .= ", ".$wpdb->prefix."zotpress_zoterocollections, ".$wpdb->prefix."zotpress_zoterotags"; // both
                    
                    // Collection(s)
                    if ($collection_id)
                    {
                        $zp_query .= " WHERE ";
                        
                        if ($tag_name && $collection_id) { $zp_query .= "("; }
                        
                        if (is_array($collection_id)) // multiple
                        {
                            if ($inclusive == "yes")
                            {
                                foreach ($collection_id as $i => $id) {
                                    $zp_query .= "(".$wpdb->prefix."zotpress_zoterocollections.item_key='".$id."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroitems.item_key, ".$wpdb->prefix."zotpress_zoterocollections.listitems))";
                                    if ($i != count($collection_id)-1)
                                        $zp_query .= " OR ";
                                }
                                $zp_query .= " AND ".$wpdb->prefix."zotpress_zoterocollections.api_user_id='".$api_user_id."'";
                            }
                            else // exclusive
                            {
                                $zp_collection_items = zp_get_exclusive_items ($wpdb, "collections", $collection_id);
                                
                                // Add to $item_key list
                                if ($item_key && is_array($item_key)) {
                                    array_push($item_key, $zp_collection_items);
                                }
                                else if ($item_key && !is_array($item_key)) {
                                    $item_key = array($item_key);
                                    array_push($item_key, $zp_collection_items);
                                }
                                else if (!$item_key) {
                                    $item_key = explode(",", $zp_collection_items);
                                }
                            }
                        }
                        else { // single
                            $zp_query .= "(".$wpdb->prefix."zotpress_zoterocollections.item_key='".$collection_id."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroitems.item_key, ".$wpdb->prefix."zotpress_zoterocollections.listitems))";
                            $zp_query .= " AND ".$wpdb->prefix."zotpress_zoterocollections.api_user_id='".$api_user_id."'";
                        }
                    }
                    
                    if ($tag_name && $collection_id)
                    {
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroitems.api_user_id='".$api_user_id."')";
                        
                        if ($inclusive == "yes")
                            $zp_query .= " UNION SELECT ".$wpdb->prefix."zotpress_zoteroitems.* FROM ".$wpdb->prefix."zotpress_zoteroitems, ".$wpdb->prefix."zotpress_zoterotags";
                        else
                            $zp_query .= " AND ";
                    }
                    
                    // Tag(s)
                    if ($tag_name)
                    {
                        $zp_query .= " WHERE ";
                        
                        if ($tag_name && $collection_id) { $zp_query .= "("; }
                        
                        if (is_array($tag_name)) // multiple
                        {
                            if ($inclusive == "yes")
                            {
                                foreach ($tag_name as $i => $tag) {
                                    $zp_query .= "(LOWER(".$wpdb->prefix."zotpress_zoterotags.title)='".strtolower($tag)."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroitems.item_key, ".$wpdb->prefix."zotpress_zoterotags.listitems))";
                                    if ($i != count($tag_name)-1)
                                        $zp_query .= " OR ";
                                }
                                $zp_query .= " AND ".$wpdb->prefix."zotpress_zoterotags.api_user_id='".$api_user_id."'";
                            }
                            else // exclusive
                            {
                                $zp_tag_items = zp_get_exclusive_items ($wpdb, "tags", $tag_name);
                                
                                // Add to $item_key list
                                if ($item_key && is_array($item_key)) {
                                    array_push($item_key, $zp_tag_items);
                                }
                                else if ($item_key && !is_array($item_key)) {
                                    $item_key = array($item_key);
                                    array_push($item_key, $zp_tag_items);
                                }
                                else if (!$item_key) {
                                    $item_key = explode(",", $zp_tag_items);
                                }
                            }
                        }
                        else { // single
                            $zp_query .= "(LOWER(".$wpdb->prefix."zotpress_zoterotags.title)='".strtolower($tag_name)."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroitems.item_key, ".$wpdb->prefix."zotpress_zoterotags.listitems))";
                            $zp_query .= " AND ".$wpdb->prefix."zotpress_zoterotags.api_user_id='".$api_user_id."'";
                        }
                    }
                    
                    if ($tag_name && $collection_id) {
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroitems.api_user_id='".$api_user_id."')";
                    }
                }
                
                // Filter by account
                if ($api_user_id && !($tag_name && $collection_id))
                    $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.api_user_id='".$api_user_id."'";
                
                // Filter by author
                if ($author)
                {
                    if (is_array($author)) // multiple authors
                    {
                        foreach ($author as $zp_author)
                        {
                            // Prep author
                            $zp_author = strtolower(trim($zp_author));
                            
                            if (strpos($zp_author, " ") > 0)
                                $zp_author = explode(" ", $zp_author);
                            
                            if (is_array($zp_author)) // fullname
                            {
                                $zp_authors_items = zp_get_fullname_author_items ($wpdb, $zp_author);
                                
                                // Add to $item_key list
                                if ($item_key && is_array($item_key)) {
                                    array_push($item_key, $zp_authors_items);
                                }
                                else if ($item_key && !is_array($item_key)) {
                                    $item_key = array($item_key);
                                    array_push($item_key, $zp_authors_items);
                                }
                                else if (!$item_key) {
                                    $item_key = explode(",", $zp_authors_items);
                                }
                            }
                            else // lastname only
                            {
                                $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.author LIKE '%".$zp_author."%'";
                                
                                if ($inclusive == "yes")
                                    $zp_query .= " OR ";
                            }
                        }
                    }
                    else // single
                    {
                        // Prep author
                        $zp_author = strtolower(trim($zp_author));
                        
                        if (strpos($author, " ") > 0)
                            $author = explode(" ", $author);
                        
                        if (is_array($author)) // fullname
                        {
                            $zp_authors_items = zp_get_fullname_author_items ($wpdb, $author);
                            
                            // Add to $item_key list
                            if ($item_key && is_array($item_key)) {
                                array_push($item_key, $zp_authors_items);
                            }
                            else if ($item_key && !is_array($item_key)) {
                                $item_key = array($item_key);
                                array_push($item_key, $zp_authors_items);
                            }
                            else if (!$item_key) {
                                $item_key = explode(",", $zp_authors_items);
                            }
                        }
                        else // lastname only
                        {
                            $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.author LIKE '%".$author."%'";
                        }
                    }
                }
                
                // Filter by year
                if ($year)
                {
                    if (is_array($year))
                    {
                        foreach ($year as $zp_year)
                            $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.zpdate LIKE '%".$zp_year."%' OR ";
                    }
                    else // single
                    {
                        $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.zpdate LIKE '%".$year."%'";
                    }
                }
                
                // Filter by item key
                if ($item_key)
                {
                    if (is_array($item_key)) {
                        $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.item_key IN('" . implode("','", $item_key) . "')";
                    }
                    else { // single
                        $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroitems.item_key='".$item_key."'";
                    }
                }
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "date")
                        $sortby = "year";
                    
                    if (($tag_name && $collection_id) || (is_array($year)))
                        $zp_query .= " ORDER BY ".$sortby." " . $order;
                    else
                        $zp_query .= " ORDER BY ".$wpdb->prefix."zotpress_zoteroitems.".$sortby." " . $order;
                }
                
                // Limit
                if ($limit)
                    $zp_query .= " LIMIT ".$limit;
                
                
                // Prep query and make db call
                $zp_query = replace_skip($zp_query,"WHERE","AND",1) .";";
                
                if ($tag_name || $collection_id) {
                    $zp_query = str_replace("AND  WHERE", "AND", str_replace("AND ((", "WHERE ((", str_replace("WHERE  AND", "WHERE", $zp_query)));
                }
                else if ($author || $year) {
                    $zp_query = str_replace("OR ORDER BY", "ORDER BY", str_replace("OR AND", "OR", str_replace("  ", " ", $zp_query)));
                }
                
                
                
                // GET ITEMS FROM DB
                
                //return $zp_query . "<br /><br />";
                $zp_results = $wpdb->get_results($zp_query, ARRAY_A); unset($zp_query);
                //var_dump($zp_results);
                
                
                
                /*
                  
                    DISPLAY CITATIONS - loop
                    
                */
                
                $current_title =  "";
                
                $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                //$zp_output .= "<span class=\"ZOTPRESS_UPDATE_NOTICE\">Checking ...</span>\n\n";
                
                // Add style, if set
                if ($style) { $zp_output .= "<span class=\"zp-Zotpress-Style\" style=\"display:none;\">".$style."</span>\n\n"; }
                
                foreach ($zp_results as $zp_citation)
                {
                    $citation_image = false;
                    $has_citation_image = false;
                    $citation_notes = false;
                    $zp_this_meta = json_decode( $zp_citation["json"] );
                    $zp_output .= "<span class=\"zp-Zotpress-Userid\" style=\"display:none;\">".$zp_citation['api_user_id']."</span>\n\n";
                    
                    // IMAGE
                    if ($showimage && !is_null($zp_citation["image"]) && $zp_citation["image"] != "")
                    {
                        $citation_image = "<div id='zp-Citation-".$zp_citation["item_key"]."' class='zp-Entry-Image' rel='".$zp_citation["item_key"]."'>";
                        $citation_image .= "<img src='".$zp_citation["image"]."' alt='image' />";
                        $citation_image .= "</div>\n";
                        $has_citation_image = " zp-HasImage";
                    }
                    
                    // NOTES
                    if ($notes)
                    {
                        $zp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."zotpress_zoteroitems WHERE api_user_id='".$zp_citation['api_user_id']."'
                                AND parent = '".$zp_citation["item_key"]."' AND itemType = 'note';", OBJECT);
                        
                        if (count($zp_notes) > 0)
                        {
                            $citation_notes = "<div class='zp-Citation-Notes'>\n<h4>Notes</h4>\n<ul>\n";
                            
                            foreach ($zp_notes as $note) {
                                $note_json = json_decode($note->json);
                                $citation_notes .= "<li class='zp-Citation-note'>" . $note_json->note . "\n</li>\n";
                            }
                            
                            $citation_notes .= "\n</ul>\n</div>\n\n";
                        }
                        unset($zp_notes);
                    }
                    
                    // Hyperlink URL: Working? Has to go before Download
                    if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
                        $zp_citation['citation'] = str_replace($zp_this_meta->url, "<a title='".$zp_this_meta->title."' rel='external' href='".$zp_this_meta->url."'>".$zp_this_meta->url."</a>", $zp_citation['citation']);
                    
                    // DOWNLOAD
                    if ($download)
                    {
                        $zp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."zotpress_zoteroitems WHERE api_user_id='".$zp_citation['api_user_id']."'
                                AND parent = '".$zp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                        
                        if (!is_null($zp_download_url))
                        {
                            if ($zp_download_url->linkMode == "imported_file") {
                                $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Download URL' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$zp_citation['api_user_id']."&download=".$zp_download_url->item_key."'>(Download)</a> </div>", $zp_citation['citation'], 1);
                            }
                            else {
                                $zp_download_meta = json_decode($zp_download_url->json);
                                $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Download URL' class='zp-DownloadURL' href='".$zp_download_meta->url."'>(Download)</a> </div>", $zp_citation['citation'], 1);
                            }
                        }
                        unset($zp_download_url);
                    }
                    
                    // CITE LINK
                    if ($cite)
                    {
                        $cite_url = "https://api.zotero.org/".$zp_account->account_type."/".$zp_account->api_user_id."/items/".$zp_citation["item_key"]."?format=ris";
                        $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>", $zp_citation['citation'], 1);
                    }
                    
                    // TITLE
                    if ($title)
                    {
                        if ($current_title == "" || (strlen($current_title) > 0 && $current_title != $zp_citation["year"]))
                        {
                            $current_title = $zp_citation["year"];
                            $zp_output .= "<h3>".$current_title."</h3>\n";
                        }
                    }
                    
                    // OUTPUT
                    
                    $zp_output .= "<div class='zp-Entry".$has_citation_image."' rel='".$zp_citation["item_key"]."'>\n";
                    $zp_output .= $citation_image . $zp_citation['citation'] . $citation_notes . "\n";
                    $zp_output .= "</div><!--Entry-->\n\n";
                }
                
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
            }
            
            
            
            // COLLECTIONS
            
            else if ($data_type == "collections")
            {
                $zp_query = "SELECT ".$wpdb->prefix."zotpress_zoteroCollections.* FROM ".$wpdb->prefix."zotpress_zoteroCollections ";
                $zp_query .= "WHERE api_user_id='".$api_user_id."' AND parent = '' ";
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "date" || $sortby == "author")
                        continue;
                    
                    $zp_query .= " ORDER BY ".$sortby." " . $order;
                }
                
                // Limit
                if ($limit)
                    $zp_query .= " LIMIT ".$limit;
                
                $zp_results = $wpdb->get_results($zp_query, OBJECT); unset($zp_query);
                
                
                // DISPLAY CITATIONS
                
                $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                $zp_output .= "<ul>\n";
                
                foreach ($zp_results as $zp_collection)
                {
                    $zp_output .= "<li rel=\"" . $zp_collection->item_key . "\">" . $zp_collection->title . "</li>\n";
                    
                    if ($zp_collection->numCollections > 0)
                        $zp_output .= zp_get_subcollections($wpdb, $api_user_id, $zp_collection->item_key, $sortby, $order);
                }
                
                $zp_output .= "</ul>\n";
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
            }
            
            
            
            // TAGS
            
            else if ($data_type == "tags")
            {
                $zp_query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."' ";
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "date" || $sortby == "author")
                        continue;
                    
                    $zp_query .= " ORDER BY ".$sortby." " . $order;
                }
                
                // Limit
                if ($limit)
                    $zp_query .= " LIMIT ".$limit;
                
                $zp_results = $wpdb->get_results($zp_query, OBJECT); unset($zp_query);
                
                
                // DISPLAY CITATIONS
                
                $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                $zp_output .="<ul>\n";
                
                foreach ($zp_results as $zp_tag)
                {
                    $zp_output .= "<li rel=\"" . $zp_tag->title . "\">" . $zp_tag->title . " <span class=\"zp-numItems\">(" . $zp_tag->numItems . " items)</span></li>\n";
                }
                
                $zp_output .="</ul>\n";
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
            }
            
            
            // FINISH UP
            
            // Clean up
            $wpdb->flush(); unset($zp_results);
            
            // Show theme scripts
            $GLOBALS['zp_is_shortcode_displayed'] = true;
            
            return $zp_output;
        }
        
        
        // Display notification if no citations found
        else
        {
            return "\n<div id='".$zp_instance_id."' class='zp-Zotpress'>Sorry, no citation(s) found.</div>\n";
        }
    }
    

    
?>