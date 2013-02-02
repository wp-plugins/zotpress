<?php

    function Zotpress_zotpressInText ($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated previously}
        *
        */
        
        extract(shortcode_atts(array(
            
            'item' => false,
            'items' => false,
            
            'pages' => false,
            'format' => "(%a%, %d%, %p%)",
            
            'userid' => false,
            'api_user_id' => false,
            'nickname' => false
            
        ), $atts));
        
        
        
        // PREPARE ATTRIBUTES
        
        if ($items)
            $items = str_replace('"','',html_entity_decode($items));
        else if ($item)
            $items = str_replace('"','',html_entity_decode($item));
        
        $pages = str_replace('"','',html_entity_decode($pages));
        $format = str_replace('"','',html_entity_decode($format));
        
        if ($userid) { $api_user_id = str_replace('"','',html_entity_decode($userid)); }
        if ($nickname) { $nickname = str_replace('"','',html_entity_decode($nickname)); }
        
        
        
        // GET ACCOUNTS
        
        global $wpdb;
        
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
        $zp_instance_id = "zotpress-".md5($api_user_id.$nickname.$pages.$items.$format);
        
        if ($items !== false)
        {
            
            
            // PREPARE ITEM KEYS: Single, with or without curly bracket, or multiple
            
            if (strpos($items, "{") !== false) {
                if (strpos($items, "},") !== false) {
                    $items = explode("},", $items);
                    foreach ($items as $id => $item)
                        $items[$id] = explode(",", str_replace("{", "", str_replace("}", "", $item)));
                }
                else {
                    $items = str_replace("{", "", str_replace("}", "", $items));
                    if (strpos($items, ",") !== false)
                        $items = explode(",", $items);
                }
            }
            
            
            // PREPARE ITEM QUERY
            
            $zp_query = "SELECT item_key, author, title, citation, zpdate, image, json, style 
                    FROM ".$wpdb->prefix."zotpress_zoteroItems
                    WHERE api_user_id='".$api_user_id."' AND ";
            
            if (is_array($items))
            {
                if (count($items) == 2 && !is_array($items[0])) {
                    $zp_query .= " item_key='" . $items[0] . "'";
                }
                else
                {
                    $zp_query .= " ".$wpdb->prefix."zotpress_zoteroItems.item_key IN ( ";
                    foreach ($items as $id => $item) {
                        $zp_query .= "'" . $item[0] . "'";
                        if (count($items)-1 != $id)
                            $zp_query .= ",";
                    }
                    $zp_query .=" )";
                }
            }
            else // single item
            {
                $zp_query .= " item_key='" . $items . "'";
            }
            
            $zp_query .= " ORDER BY ".$wpdb->prefix."zotpress_zoteroItems.author ASC;";
            
            
            
            // QUERY DATABASE
            //var_dump($zp_query . "<br /><br />");
            $zp_results = $wpdb->get_results($zp_query, OBJECT);
            //var_dump($zp_results);
            
            $zp_intext_citation = "";
            
            
            
            // FORMAT IN-TEXT CITATION
            
            foreach ($zp_results as $id => $item)
            {
                // Shorten author if repeated
                if ($GLOBALS['zp_shortcode_instances'][get_the_ID()][$item->item_key] && count(explode(",", $item->author)) > 3)
                    $item->author = substr($item->author, 0, strpos($item->author, ",")) . " <em>et al.</em>";
                
                // Fill in author, date and number
                $citation = str_replace("%num%", (count($GLOBALS['zp_shortcode_instances'][get_the_ID()])+1), str_replace("%a%", $item->author, str_replace("%d%", zp_get_year($item->zpdate), $format)));
                
                // Deal with pages
                if ($pages)
                {
                    $citation = str_replace("%p%", $pages, $citation);
                }
                else // New way
                {
                    if (is_array($items))
                    {
                        if (count($items) == 2 && !is_array($items[0]))
                        {
                            $citation = str_replace("%p%", $items[1], $citation);
                        }
                        else // Multiple citations
                        {
                            if ($items[$id][1])
                                $citation =  str_replace("%p%", $items[$id][1], $citation);
                            else
                                $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                        }
                    }
                    else // No pages
                    {
                        $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                    }
                }
                
                $zp_intext_citation .= $citation;
                
                // SET BIBLIOGRAPHY CITATIONS: Per item
                $GLOBALS['zp_shortcode_instances'][get_the_ID()][$api_user_id.",".$item->item_key] = array(
                        "instance_id" => $zp_instance_id,
                        "userid" => $api_user_id,
                        "account_type" => $zp_account->account_type,
                        "public_key" => $zp_account->public_key,
                        "item_key" => $item->item_key,
                        "author" => $item->author,
                        "title" => $item->title,
                        "date" => zp_get_year($item->zpdate),
                        "download" => $item->download,
                        "image" => $item->image,
                        "json" => $item->json,
                        "citation" => $item->citation,
                        "style" => $item->style
                    );
            }
            
            return "<a title='Anchor to citation for `".$item->title."`' id='.$zp_instance_id.' class='zp-ZotpressInText' href='#zp-".get_the_ID()."-".$item->item_key."'>" . str_replace(")(", "; ", str_replace("][", ", ", $zp_intext_citation)) . "</a>";
            
            unset($zp_query);
            unset($zp_results);
            unset($zp_intext_citation);
            $wpdb->flush();
        }
        
        // Display notification if no citations found
        else {
            return "\n<div id='".$zp_instance_id."' class='zp-Zotpress'>Sorry, no citation(s) found.</div>\n";
        }
        
        // Show theme scripts
        $GLOBALS['zp_is_shortcode_displayed'] = true;
        
    }

    
?>