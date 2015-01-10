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
			'brackets' => false,
            'etal' => false, // default (false), yes, no
            'separator' => false, // default (comma), semicolon
            'and' => false, // default (no), and, comma-and
            
            'userid' => false,
            'api_user_id' => false,
            'nickname' => false,
            'nick' => false
            
        ), $atts));
        
        
        
        // PREPARE ATTRIBUTES
        
        if ($items) $items = str_replace('"','',html_entity_decode($items));
        else if ($item) $items = str_replace('"','',html_entity_decode($item));
        
        $pages = str_replace('"','',html_entity_decode($pages));
        $format = str_replace('"','',html_entity_decode($format));
        $brackets = str_replace('"','',html_entity_decode($brackets));
        
        $etal = str_replace('"','',html_entity_decode($etal));
        if ($etal == "default") { $etal = false; }
        
        $separator = str_replace('"','',html_entity_decode($separator));
        if ($separator == "default") { $separator = false; }
        
        $and = str_replace('"','',html_entity_decode($and));
        if ($and == "default") { $and = false; }
        
        if ($userid) { $api_user_id = str_replace('"','',html_entity_decode($userid)); }
        if ($nickname) { $nickname = str_replace('"','',html_entity_decode($nickname)); }
        if ($nick) { $nickname = str_replace('"','',html_entity_decode($nick)); }
        
        
        
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
            
            if (strpos($items, "{") !== false)
            {
                if (strpos($items, "},") !== false)
                {
                    $items = explode("},", $items);
                    foreach ($items as $id => $item) $items[$id] = explode(",", str_replace("{", "", str_replace("}", "", $item)));
                }
                else
                {
                    $items = str_replace("{", "", str_replace("}", "", $items));
                    if (strpos($items, ",") !== false) $items = explode(",", $items);
                }
            }
            
            
            // PREPARE ITEM QUERY
            
            $zp_query = "SELECT items.*, ".$wpdb->prefix."zotpress_zoteroItemImages.image AS itemImage ";
            
            $zp_query .= "FROM ".$wpdb->prefix."zotpress_zoteroItems AS items ";
            $zp_query .= "LEFT JOIN ".$wpdb->prefix."zotpress_zoteroItemImages
								ON items.item_key=".$wpdb->prefix."zotpress_zoteroItemImages.item_key
								AND items.api_user_id=".$wpdb->prefix."zotpress_zoteroItemImages.api_user_id ";
            
            $zp_query .= "WHERE items.api_user_id='".$api_user_id."' AND ";
            
            /*$zp_citation_attr =
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'zp_entry',
                    'meta_key' => 'author',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'meta_query' => ''
                );
                
            $zp_citation_meta_query =
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'api_user_id',
                        'value' => $api_user_id,
                        'compare' => '='
                    )
                );*/
            
            if ( is_array($items) )
            {
                if ( count($items) == 2 && !is_array($items[0]) )
                {
                    $zp_query .= " items.item_key='" . $items[0] . "'";
                    /*array_push( $zp_citation_meta_query,
                            array(
                                'key' => 'item_key',
                                'value' => $items[0],
                                'compare' => '='
                            )
                        );*/
                }
                else
                {
                    $zp_query .= " items.item_key IN ( ";
                    foreach ($items as $id => $item)
                    {
                        $zp_query .= "'" . $item[0] . "'";
                        if (count($items)-1 != $id) $zp_query .= ",";
                        /*array_push( $zp_citation_meta_query,
                            array(
                                'key' => 'item_key',
                                'value' => $items[0],
                                'compare' => '='
                            )
                        );*/
                    }
                    $zp_query .=" )";
                }
            }
            else // single item
            {
                $zp_query .= " items.item_key='" . $items . "'";
                /*array_push( $zp_citation_meta_query,
                    array(
                        'key' => 'item_key',
                        'value' => $items,
                        'compare' => '='
                    )
                );*/
            }
            
            //$zp_citation_attr['meta_query'] = $zp_citation_meta_query;
            $zp_query .= " ORDER BY items.author ASC, items.zpdate ASC;";
            
            
            
            // QUERY DATABASE
            //var_dump($zp_query);
            $zp_results = $wpdb->get_results($zp_query, OBJECT);
            //var_dump($zp_results);
            
            $zp_intext_citation = ""; // Output for display
			$zp_intext_citation_arr = array(); // Array for sorting
			
            
            
            // FORMAT IN-TEXT CITATION
			
			$prev_num = 1;
            
            foreach ($zp_results as $id => $item)
            {
                $zp_json = json_decode( $item->json );
                
                // Determine author if "author" doesn't exist
                if ( trim($item->author) == "" )
                {
					if ( isset($zp_json->creators) && count($zp_json->creators) > 0 )
					{
						foreach ( $zp_json->creators as $i => $zp_creator )
						{
							$item->author = $zp_creator->name;
							if ( $i != (count($zp_json->creators)-1) ) $item->author .= ", ";
						}
					}
					else // assume no author exists; use title instead
					{
						$item->author .= "\"" . $item->title . "\"";
					}
                }
                
                // Shorten author ...
                if ($etal)
                {
                    if ($etal == "yes") $item->author = substr($item->author, 0, strpos($item->author, ",")) . " <em>et al.</em>";
                }
                else // default
                {
                    if (isset($GLOBALS['zp_shortcode_instances'][get_the_ID()][$api_user_id.",".$item->item_key])
                            && count(explode(",", $item->author)) > 3)
                    {
                        $item->author = substr($item->author, 0, strpos($item->author, ",")) . " <em>et al.</em>";
                    }
                }
                
                // Deal with 'and' => false, // default (no), and, comma-and
                if ($and)
                {
                    if ($and == "and")
                    {
                        if ( strrpos($item->author, ",") !== false )
                            $item->author = substr_replace( $item->author, " and", strrpos($item->author, ","), 1 );
                    }
                    else if ($and == "comma-and")
                    {
                        if ( strrpos($item->author, ",") !== false )
                            $item->author = substr_replace( $item->author, ", and", strrpos($item->author, ","), 1 );
                    }
                }
                
                // Determine %num%
                // Determine if this citation has already been referenced
                $num = false;
                if (isset($GLOBALS['zp_shortcode_instances'][get_the_ID()]) && count($zp_results) >= 1)
                {
                    $numloop = 1;
                    foreach ($GLOBALS['zp_shortcode_instances'][get_the_ID()] as $position => $instance)
                    {
                        if ($position == $api_user_id.",".$item->item_key)
                        {
                            $num = $numloop;
                            break;
                        }
                        $numloop++;
                    }
                }
                
                // Determine what %num% is if not already referenced
                if ($num === false)
                    if (isset($GLOBALS['zp_shortcode_instances'][get_the_ID()]))
                        $num = count($GLOBALS['zp_shortcode_instances'][get_the_ID()])+1;
                    else
                        $num = 1;
                
                // Fill in author, date and number
                $citation = str_replace("%num%", $num, str_replace("%a%", $item->author, str_replace("%d%", zp_get_year($item->zpdate, true), $format)));
                
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
                        else 
                        {
							// Multiple citations -- shouldn't have page numbers
                            //if (isset($items[$id][1])) {
                            //    $citation =  str_replace("%p%", $items[$id][1], $citation);
                            //}
                            //else {
                                $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                            //}
                        }
                    }
                    else // No pages
                    {
                        $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                    }
                }
                
                // Format for multiple (only expected characters)
                if (count($zp_results) > 1)
                {
                    if ($id == 0)
                        $citation = str_replace("&#93;", "", str_replace(")", "", $citation));
                    else if ($id == (count($zp_results)-1))
                        $citation = str_replace("&#91;", "", str_replace("(", " ", $citation));
                    else
                        $citation = str_replace("&#93;", "", str_replace("&#91;", "", str_replace(")", "", str_replace("(", " ", $citation))));
                }
				
				// Deal with download
				$item_download = false; if (isset($item->attachment_data)) $item_download = $item->attachment_data;
				$item_download_key = false; if (isset($item->attachment_key)) $item_download_key = $item->attachment_key;
                
				// SET SORT ARRAY
				$zp_intext_citation_arr[$api_user_id.",".$item->item_key] = array(
                        "instance_id" => $zp_instance_id,
                        "api_user_id" => $api_user_id,
                        "item_key" => $item->item_key,
                        "author" => $item->author,
                        "title" => $item->title,
                        "zpdate" => zp_get_year($item->zpdate),
                        "citation" => $citation,
						"alphacount" => ""
                    );
				
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
                        "download" => $item_download,
                        "download_key" => $item_download_key,
                        "image" => $item->itemImage,
                        "json" => $item->json,
                        "citation" => $item->citation,
                        "style" => $item->style,
						"alphacount" => ""
                    );
            }
			
			// First, sort in-text items
			//$zp_intext_citation_arr = subval_sort($zp_intext_citation_arr, "author", "asc");
			$zp_intext_citation_output_arr = array();
			
			$zp_alphacount = "";
			$zp_alphacount_author = "";
			
			// Then build output array
			
			foreach ( $zp_intext_citation_arr as $id => $item_arr )
				$zp_intext_citation_output_arr[count($zp_intext_citation_output_arr)] = $item_arr;
            
			
			foreach ( $zp_intext_citation_output_arr as $i => $item )
			{
				$zp_alphacount_this = "";
				
				if ( isset($zp_intext_citation_output_arr[$i+1]["author"])
						&& $item["author"] == $zp_intext_citation_output_arr[$i+1]["author"]
						&& $item["zpdate"] == $zp_intext_citation_output_arr[$i+1]["zpdate"] )
				{
					if ( $zp_alphacount == "" )
						$zp_alphacount_this = "a";
					else
						if ( $zp_alphacount_author != $item["author"] )
							$zp_alphacount_this = "a";
						else
							$zp_alphacount_this = ++$zp_alphacount;
					
					$zp_alphacount_author = $item["author"];
					
					// Update the counts on this and the next one
					$item["alphacount"] = $zp_alphacount_this;
					$GLOBALS['zp_shortcode_instances'][get_the_ID()][$item["api_user_id"].",".$item["item_key"]]["alphacount"] = $zp_alphacount_this;
					$GLOBALS['zp_shortcode_instances'][get_the_ID()][$zp_intext_citation_output_arr[$i+1]["api_user_id"].",".$zp_intext_citation_output_arr[$i+1]["item_key"]]["alphacount"] = ++$zp_alphacount_this;
					
					$zp_alphacount = $zp_alphacount_this;
				}
				
				$item["alphacount"] = $GLOBALS['zp_shortcode_instances'][get_the_ID()][$zp_intext_citation_output_arr[$i]["api_user_id"].",".$zp_intext_citation_output_arr[$i]["item_key"]]["alphacount"];
				
				$zp_intext_citation .= "<a title='";
				
				if ($item["author"])
				{
					// Remove author if same in a row
					if ( isset($zp_intext_citation_output_arr[$i-1]["author"])
							&& $item["author"] == $zp_intext_citation_output_arr[$i-1]["author"] )
						$item["citation"] = str_replace( $item["author"] . ", ", "", $item["citation"] );
					
					$zp_intext_citation .= htmlspecialchars(strip_tags($item["author"]), ENT_QUOTES) . " ";
				}
				else { $item["author"] = $item["title"]; $zp_intext_citation .= "No author "; }
				
				if ($item["zpdate"])
				{
					$zp_intext_citation .= "(".$item["zpdate"].$item["alphacount"]."). ";
					$item["citation"] = str_replace( $item["zpdate"], $item["zpdate"].$item["alphacount"], $item["citation"]);
				}
				
				$zp_intext_citation .= htmlspecialchars(strip_tags($item["title"]), ENT_QUOTES) . ".' id='".$item["instance_id"]."' class='zp-ZotpressInText' href='#zp-".get_the_ID()."-".$item["item_key"]."'>" . $item["citation"] . "</a>";
				$zp_intext_citation = str_replace( "al..", "al.", $zp_intext_citation);
				
				// Determine delineation for multiple citations
				if ( count($zp_intext_citation_arr) > 1 && $i != (count($zp_intext_citation_arr)-1) )
					if ( $separator && $separator == "comma" )
						$zp_intext_citation .= ",";
					else
						if ( isset($zp_intext_citation_output_arr[$i+1]["author"])
								&& $item["author"] == $zp_intext_citation_output_arr[$i+1]["author"] )
							$zp_intext_citation .= ",";
						else
							if ( $brackets )
								$zp_intext_citation .= ", ";
							else
								$zp_intext_citation .= ";";
			}
			
			// Add brackets, if necessary
			if ( $brackets ) $zp_intext_citation = "&#91;" . $zp_intext_citation . "&#93;";
			
            return $zp_intext_citation;
            
            unset($zp_query);
            unset($zp_results);
            unset($zp_intext_citation);
            unset($zp_intext_citation_arr);
            unset($zp_intext_citation_output_arr);
            
            $wpdb->flush();
        }
        
        // Display notification if no citations found
        else
        {
            return "\n<div id='".$zp_instance_id."' class='zp-Zotpress'>Sorry, no citation(s) found.</div>\n";
        }
        
        // Show theme scripts
        $GLOBALS['zp_is_shortcode_displayed'] = true;
        
    }

    
?>