<?php


    // Include shortcode functions
    require("shortcode.functions.php");
    
    
    function Zotpress_func($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // deprecated
            'userid' => false,
            'nickname' => false,
            'nick' => false,
            
            'author' => false,
            'authors' => false,
            'year' => false,
            'years' => false,
            
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
            'order' => false,
            'sort' => false,
            
            'title' => "no",
            
            'image' => false,
            'images' => false,
            'showimage' => "no",
            
            'showtags' => "no",
            
            'downloadable' => "no",
            'download' => "no",
            
            'note' => false,
            'notes' => "no",
            
            'abstract' => false,
            'abstracts' => "no",
            
            'cite' => "no",
            'citeable' => false,
            
            'metadata' => false,
            
            'link' => "no",
            'linkedlist' => "no",
            
            'target' => false,
			
			'forcenumber' => false,
			
			'depth' => false
            
        ), $atts, "zotpress"));
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id) $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid) $api_user_id = str_replace('"','',html_entity_decode($userid));
        else $api_user_id = false;
        
        if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
        if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));
        
        // Filter by author
        $author = str_replace('"','',html_entity_decode($author));
        if ($authors) $author = str_replace('"','',html_entity_decode($authors));
        if (strpos($author, ",") > 0) $author = explode(",", $author);
        
        // Filter by year
        $year = str_replace('"','',html_entity_decode($year));
        if ($years) $year = str_replace('"','',html_entity_decode($years));
        if (strpos($year, ",") > 0) $year = explode(",", $year);
        
        // Format with datatype and content
        if ($data_type) $data_type = str_replace('"','',html_entity_decode($data_type));
        else $data_type = str_replace('"','',html_entity_decode($datatype));
        
        // Filter by collection
        if ($collection_id) $collection_id = str_replace('"','',html_entity_decode($collection_id));
        else if ($collection) $collection_id = str_replace('"','',html_entity_decode($collection));
        else if ($collections) $collection_id = str_replace('"','',html_entity_decode($collections));
        //else $collection_id = str_replace('"','',html_entity_decode($collection));
        
        if (strpos($collection_id, ",") > 0) $collection_id = explode(",", $collection_id);
        if ($data_type == "collections" && isset($_GET['zpcollection']) ) $collection_id = htmlentities( urldecode( $_GET['zpcollection'] ) );
        
        // Filter by tag
        if ($tag_name) $tag_name = str_replace('"','',html_entity_decode($tag_name));
        else if ($tags) $tag_name = str_replace('"','',html_entity_decode($tags));
        else $tag_name = str_replace('"','',html_entity_decode($tag));
        
        $tag_name = str_replace("+", "", $tag_name);
        if (strpos($tag_name, ",") > 0) $tag_name = explode(",", $tag_name);
        if ($data_type == "tags" && isset($_GET['zptag']) ) $tag_name = htmlentities( urldecode( $_GET['zptag'] ) );
        
        // Filter by itemkey
        if ($item_key) $item_key = str_replace('"','',html_entity_decode($item_key));
        if ($items) $item_key = str_replace('"','',html_entity_decode($items));
        if ($item) $item_key = str_replace('"','',html_entity_decode($item));
        if (strpos($item_key, ",") > 0) $item_key = explode(",", $item_key);
        
        $content = str_replace('"','',html_entity_decode($content));
        $inclusive = str_replace('"','',html_entity_decode($inclusive));
        
        // Format style
        $style = str_replace('"','',html_entity_decode($style));
        
        // Limit
        $limit = str_replace('"','',html_entity_decode($limit));
        
        // Order / sort
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order) $order = str_replace('"','',html_entity_decode($order));
        else if ($sort) $order = str_replace('"','',html_entity_decode($sort));
        if ($order === false) $order = "ASC";
        
        // Show title
        $title = str_replace('"','',html_entity_decode($title));
        if ($title == "yes" || $title == "true" || $title === true)
        {
            $title = true;
            $sortby = "year";
            $order= "DESC";
        }
        else { $title = false; }
        
        // Show image
        if ($showimage) $showimage = str_replace('"','',html_entity_decode($showimage));
        if ($image) $showimage = str_replace('"','',html_entity_decode($image));
        if ($images) $showimage = str_replace('"','',html_entity_decode($images));
        
        if ($showimage == "yes" || $showimage == "true" || $showimage === true) $showimage = true;
        else $showimage = false;
        
        // Show tags
        if ($showtags == "yes" || $showtags == "true" || $showtags === true) $showtags = true;
        else $showtags = false;
        
        // Show download link
        if ($download == "yes" || $download == "true" || $download === true
                || $downloadable == "yes" || $downloadable == "true" || $downloadable === true)
            $download = true; else $download = false;
        
        // Show notes
        if ($notes) $notes = str_replace('"','',html_entity_decode($notes));
        else if ($note) $notes = str_replace('"','',html_entity_decode($note));
        
        if ($notes == "yes" || $notes == "true" || $notes === true) $notes = true;
        else $notes = false;
        
        // Show abstracts
        if ($abstracts) $abstracts = str_replace('"','',html_entity_decode($abstracts));
        if ($abstract) $abstracts = str_replace('"','',html_entity_decode($abstract));
        
        if ($abstracts == "yes" || $abstracts == "true" || $abstracts === true) $abstracts = true;
        else $abstracts = false;
        
        // Show cite link
        if ($cite) $cite = str_replace('"','',html_entity_decode($cite));
        if ($citeable) $cite = str_replace('"','',html_entity_decode($citeable));
        
        if ($cite == "yes" || $cite == "true" || $cite === true) $cite = true;
        else $cite = false;
        
        if ( !preg_match("/^[0-9a-zA-Z]+$/", $metadata) ) $metadata = false;
        
        if ( $link == "yes" || $link == "true" || $link === true ) $link = str_replace('"','',html_entity_decode($link));
        else if ( $linkedlist == "yes" || $linkedlist == "true" || $linkedlist === true ) $link = str_replace('"','',html_entity_decode($linkedlist));
        
        if ($target == "yes" || $target == "_blank" || $target == "new" || $target == "true" || $target === true)
        $target = true; else $target = false;
        
        if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
        $forcenumber = true; else $forcenumber = false;
        
        if ($depth == "all" || $depth == "true" || $depth === true)
        $depth = true; else $depth = false;
        
        
        
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
            
            if ( $data_type == "items"
                    || ($data_type == "tags" && isset($_GET['zptag']) )
                    || ($data_type == "collections" && isset($_GET['zpcollection'])) )
            {
                $zp_query = "";
                
                if ($download)
                {
                    $wpdb->get_results(
                        "
                        CREATE TEMPORARY TABLE attachments 
                        SELECT * FROM 
                        ( 
                            SELECT 
                            ".$wpdb->prefix."zotpress_zoteroItems.parent AS parent,
                            ".$wpdb->prefix."zotpress_zoteroItems.citation AS content,
                            ".$wpdb->prefix."zotpress_zoteroItems.item_key AS item_key,
                            ".$wpdb->prefix."zotpress_zoteroItems.json AS data,
                            ".$wpdb->prefix."zotpress_zoteroItems.linkmode AS linkmode 
                            FROM ".$wpdb->prefix."zotpress_zoteroItems 
                            WHERE api_user_id='".$api_user_id."' AND 
                            ".$wpdb->prefix."zotpress_zoteroItems.linkmode IN ( 'imported_file', 'linked_url' ) 
                            ORDER BY linkmode ASC 
                        )
                        AS attachments_sub 
                        GROUP BY parent;
                        "
                    );
                }
                
                $zp_query .= "SELECT DISTINCT ".$wpdb->prefix."zotpress_zoteroItems.*";
                
                if ($download) $zp_query .= ", attachments.content AS attachment_content, attachments.item_key AS attachment_key, attachments.data AS attachment_data, attachments.linkmode AS attachment_linkmode";
				
				if ($showimage) $zp_query .= ", ".$wpdb->prefix."zotpress_zoteroItemImages.image AS itemImage";
                
                $zp_query .= " FROM ".$wpdb->prefix."zotpress_zoteroItems ";
                
                
                // JOINS: download, itemimage, collections, tags
                
                if ($download)
                    $zp_query .= " LEFT JOIN (attachments) ON  (".$wpdb->prefix."zotpress_zoteroItems.item_key=attachments.parent) ";
                
                if ($showimage)
                    $zp_query .= " LEFT JOIN (".$wpdb->prefix."zotpress_zoteroItemImages) ON  (".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroItemImages.item_key) ";
                
                if ($collection_id)
                {
					if ( is_array($collection_id) )
					{
						// create inner joins
						for ($i = 0; $i < count($collection_id); $i++)
							$zp_query .= " INNER JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl AS zpRelItemColl".$i." ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=zpRelItemColl".$i.".item_key ";
						
						// inclusive?
						if ( $inclusive != "yes" )
						{
							$zp_query .= " AND ( ";
							
							// exclusive to specific collections
							for ($i = 0; $i < count($collection_id); $i++)
							{
								if ($i != 0) $zp_query .= " AND ";
								$zp_query .= " zpRelItemColl".$i.".collection_key='".$collection_id[$i]."' ";
							}
							$zp_query .= " ) ";
						}
					}
					else // single collection
					{
						$zp_query .= " LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl ON (".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key) ";
					}
//                    if (!is_array($collection_id)
//							|| (is_array($collection_id) && $inclusive == "yes"))
//                    {
//                        $zp_query .= " LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl ON (".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key) ";
//                    }
//                    else if (is_array($collection_id) && $inclusive != "yes")
//                    {
//                        // create inner joins
//                        for ($i = 0; $i < count($collection_id); $i++)
//                            $zp_query .= " INNER JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl AS zpRelItemColl".$i." ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=zpRelItemColl".$i.".item_key ";
//                        
//                        $zp_query .= " AND ( ";
//                        
//                        // exclusive to specific collections
//                        for ($i = 0; $i < count($collection_id); $i++)
//                        {
//                            if ($i != 0) $zp_query .= " AND ";
//                            $zp_query .= " zpRelItemColl".$i.".collection_key='".$collection_id[$i]."' ";
//                        }
//                        $zp_query .= " ) ";
//                    }
                }
                
                if ($tag_name)
                {
                    if (!is_array($tag_name) || (is_array($tag_name) && $inclusive == "yes"))
                    {
                        $zp_query .= " LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemTags ON (".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemTags.item_key) ";
                    }
                    else if (is_array($tag_name) && $inclusive != "yes")
                    {
                        // create inner joins
                        for ($i = 0; $i < count($tag_name); $i++)
                            $zp_query .= " INNER JOIN ".$wpdb->prefix."zotpress_zoteroRelItemTags AS zpRelItemTags".$i." ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=zpRelItemTags".$i.".item_key ";
                        
                        $zp_query .= " AND ( ";
                        
                        // exclusive to specific tags
                        for ($i = 0; $i < count($tag_name); $i++)
                        {
                            if ($i != 0) $zp_query .= " AND ";
                            $zp_query .= " zpRelItemTags".$i.".tag_title='".$tag_name[$i]."' ";
                        }
                        $zp_query .= " ) ";
                    }
                }
                
                // WHERE
                
                $zp_query .= " WHERE ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'attachment' AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'note' ";
                
                // Filter by collection(s)
                if ($collection_id)
                {
                    // Multiple inclusive collections
                    if (is_array($collection_id))
                    {
                        if ($inclusive == "yes")
                        {
                            $zp_query .= " AND (";
                            
                            foreach ($collection_id as $i => $id)
                            {
                                $zp_query .= "zpRelItemColl0.collection_key='".$id."' "; // for some reason, only need first reference to this table
                                
                                if ($i != count($collection_id)-1) $zp_query .= " OR ";
                            }
                            $zp_query .= ") ";
                        }
                    }
                    // Single collection
                    else
                    {
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key='".$collection_id."' ";
                    }
                } // $collection_id
                
                // Filter by tag(s)
                if ($tag_name)
                {
                    // Multiple inclusive collections
                    if (is_array($tag_name))
                    {
                        if ($inclusive == "yes")
                        {
                            $zp_query .= " AND (";
                            
                            foreach ($tag_name as $i => $id)
                            {
                                $zp_query .= $wpdb->prefix."zotpress_zoteroRelItemTags.tag_title='".$id."' ";
                                
                                if ($i != count($tag_name)-1) $zp_query .= " OR ";
                            }
                            $zp_query .= ") ";
                        }
                    }
                    // Single collection
                    else
                    {
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroRelItemTags.tag_title='".$tag_name."' ";
                    }
                } // $tag_name
                
                // Filter by account
                if ($api_user_id)
                    $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id='".$api_user_id."'";
                
                // Filter by author
                if ($author)
                {
                    $zp_query .= " AND ( ";
                    
                    // Multiple authors
                    if (is_array($author))
                    {
                        foreach ($author as $i => $zp_author)
                        {
                            // Prep author
                            $zp_author = strtolower(trim($zp_author));
                            if (strpos($zp_author, " ") > 0) $zp_author = preg_split("/\s+(?=\S*+$)/", $zp_author);
                            
                            if (is_array($zp_author)) // full name
                            {
                                if ($inclusive == "yes" && $i != 0) $zp_query .= " OR ";
                                
                                $zp_query .= " ".$wpdb->prefix."zotpress_zoteroItems.author LIKE '%".$zp_author[1]."%'";
                            }
                            else // last name only
                            {
                                if ($inclusive == "yes" && $i != 0) $zp_query .= " OR ";
                                
                                $zp_query .= " ".$wpdb->prefix."zotpress_zoteroItems.author LIKE '%".$zp_author."%'";
                            }
                        }
                    }
                    else // Single author
                    {
                        // Prep author
                        $zp_author = strtolower(trim($zp_author));
                        if (strpos($author, " ") > 0) $author = preg_split("/\s+(?=\S*+$)/", $author);
                        
                        if (is_array($author)) // fullname
                            $zp_query .= " ".$wpdb->prefix."zotpress_zoteroItems.author LIKE '%".$author[1]."%'";
                        else // lastname only
                            $zp_query .= " ".$wpdb->prefix."zotpress_zoteroItems.author LIKE '%".$author."%'";
                    }
                    $zp_query .= " ) ";
                } // $author
                
                // Filter by year: zpdate or year
                if ($year)
                {
                    if (is_array($year))
                    {
                        $zp_query .= " AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroItems.year, '".implode(",", $year)."')";
                    }
                    else // single
                    {
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroItems.year LIKE '%".$year."%'";
                    }
                }
                
                // Filter by item key
                if ($item_key)
                {
                    if (is_array($item_key))
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroItems.item_key IN('" . implode("','", $item_key) . "')";
                    else // single
                        $zp_query .= " AND ".$wpdb->prefix."zotpress_zoteroItems.item_key='".$item_key."'";
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
                        $zp_query .= " ORDER BY ".$wpdb->prefix."zotpress_zoteroItems.".$sortby." " . $order;
                }
                
                // Limit
                if ($limit) $zp_query .= " LIMIT ".$limit;
                
                
                // Prep query -- still necessary?
                
                if ($item_key || $tag_name || $collection_id)
                {
                    $zp_query = str_replace("AND  AND", "AND", $zp_query);
                }
                else if ($author || $year) {
                    $zp_query = str_replace("OR ORDER BY", "ORDER BY", str_replace("OR AND", "OR", str_replace("  ", " ", $zp_query)));
                }
                
                
                
                // GET ITEMS FROM DB
                
                //var_dump( $zp_query . "<br /><br />");
                $zp_results = $wpdb->get_results($zp_query, ARRAY_A); unset($zp_query);
                //var_dump( $zp_results ); exit;
                
                
                
                /*
                  
                    DISPLAY CITATIONS - loop
                    
                */
                
                $current_title =  "";
                $citation_notes = "";
                $zp_notes_num = 1;
                
                $zp_output = "\n<div class=\"zp-Zotpress";
				
				// Force numbering despite style
				if ( $forcenumber ) $zp_output .= " forcenumber";
				
				$zp_output .= "\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                //$zp_output .= "<span class=\"ZOTPRESS_UPDATE_NOTICE\">Checking ...</span>\n\n";
                
                // Add style, if set
                if ($style) $zp_output .= "<span class=\"zp-Zotpress-Style\" style=\"display:none;\">".$style."</span>\n\n";
                
                // TAG OR COLLECTION TITLE
                if ( $data_type == "collections" && isset($_GET['zpcollection']) )
                {
                    $collection_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE item_key='".$collection_id."'");
                    $zp_output .= "<h2>" . $collection_title->title . " / <a title='Back' class='zp-BackLink' href='javascript:window.history.back();'>Back</a></h2>\n\n";
                }
                if ( $data_type == "tags" && isset($_GET['zptag']) )
                {
                    $zp_output .= "<h2>" . $tag_name . " / <a title='Back' class='zp-BackLink' href='javascript:window.history.back();'>Back</a></h2>\n\n";
                }
                
                if ( count($zp_results) > 0 )
                {
                    foreach ($zp_results as $zp_citation)
                    {
                        $citation_image = false;
                        $citation_tags = false;
                        $citation_abstract = "";
                        $has_citation_image = false;
                        $zp_this_meta = json_decode( $zp_citation["json"] );
                        $zp_output .= "<span class=\"zp-Zotpress-Userid\" style=\"display:none;\">".$zp_citation['api_user_id']."</span>\n\n";
                        //$zp_output .= "<span class=\"ZOTPRESS_AUTOUPDATE_KEY\" style=\"display:none;\">" . $_SESSION['zp_session'][$zp_citation['api_user_id']]['key'] . "</span>\n\n";
                        
                        
                        // IMAGE
                        if ($showimage && !is_null($zp_citation["itemImage"]) && $zp_citation["itemImage"] != "")
                        {
                            if ( is_numeric($zp_citation["itemImage"]) )
                            {
                                $zp_citation["itemImage"] = wp_get_attachment_image_src( $zp_citation["itemImage"], "full" );
                                $zp_citation["itemImage"] = $zp_citation["itemImage"][0];
                            }
                            
                            $citation_image = "<div id='zp-Citation-".$zp_citation["item_key"]."' class='zp-Entry-Image' rel='".$zp_citation["item_key"]."'>";
                            $citation_image .= "<img src='".$zp_citation["itemImage"]."' alt='image' />";
                            $citation_image .= "</div>\n";
                            $has_citation_image = " zp-HasImage";
                        }
                        
                        // TAGS
                        // Grab tags associated with item
                        if ( $showtags )
                        {
                            $zp_showtags_query = "SELECT DISTINCT ".$wpdb->prefix."zotpress_zoteroTags.title FROM ".$wpdb->prefix."zotpress_zoteroTags LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemTags ON ".$wpdb->prefix."zotpress_zoteroRelItemTags.tag_title=".$wpdb->prefix."zotpress_zoteroTags.title WHERE ".$wpdb->prefix."zotpress_zoteroRelItemTags.item_key='".$zp_citation["item_key"]."' ORDER BY ".$wpdb->prefix."zotpress_zoteroTags.title ASC;";
                            $zp_showtags_results = $wpdb->get_results($zp_showtags_query, ARRAY_A);
                            
                            if ( count($zp_showtags_results) > 0)
                            {
                                $citation_tags = "<p class='zp-Zotpress-ShowTags'><span class='title'>Tags:</span> ";
                                
                                foreach ($zp_showtags_results as $i => $zp_showtags_tag)
                                {
                                    $citation_tags .= "<span class='tag'>" . $zp_showtags_tag["title"] . "</span>";
                                    if ( $i != (count($zp_showtags_results)-1) ) $citation_tags .= "<span class='separator'>,</span> ";
                                }
                                $citation_tags .= "</p>\n";
                            }
                            unset($zp_showtags_query);
                            unset($zp_showtags_results);
                        }
                        
                        // ABSTRACT
                        if ( $abstracts && isset($zp_this_meta->abstractNote) && strlen(trim($zp_this_meta->abstractNote)) > 0 )
                        {
                            $citation_abstract = "<p class='zp-Abstract'><span class='zp-Abstract-Title'>Abstract:</span> " . sprintf($zp_this_meta->abstractNote) . "</p>\n";
                        }
                        
                        
                        // NOTES
                        if ($notes)
                        {
                            $zp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['api_user_id']."'
                                    AND parent = '".$zp_citation["item_key"]."' AND itemType = 'note';", OBJECT);
                            
                            if (count($zp_notes) > 0)
                            {
                                $citation_notes .= "<li>\n<ul class='zp-Citation-Item-Notes'>\n";
                                
                                foreach ($zp_notes as $note) {
                                    $note_json = json_decode($note->json);
                                    $citation_notes .= "<li class='zp-Citation-note'>" . $note_json->note . "\n</li>\n";
                                }
                                
                                $citation_notes .= "\n</ul>\n</li>\n\n";
                                
                                // Add note reference
                                $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <sup class=\"zp-Notes-Reference\">".$zp_notes_num."</sup> </div>" . '$2', $zp_citation['citation'], 1);
                                $zp_notes_num++;
                            }
                            unset($zp_notes);
                            
                        } // end notes
                        
                        
                        // Hyperlink URL: Has to go before Download
                        if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
                        {
                            $zp_url_replacement = "<a title=\"". htmlspecialchars($zp_this_meta->title) ."\" rel=\"external\" ";
                            if ( $target ) $zp_url_replacement .= "target=\"_blank\" ";
                            $zp_url_replacement .= "href=\"".urldecode(urlencode($zp_this_meta->url))."\">".urldecode(urlencode($zp_this_meta->url))."</a>";
                            
                            // Replace ampersands
                            $zp_citation['citation'] = str_replace(htmlspecialchars($zp_this_meta->url), $zp_this_meta->url, $zp_citation['citation']);
                            
                            // Then replace with linked URL
                            $zp_citation['citation'] = str_replace($zp_this_meta->url, $zp_url_replacement, $zp_citation['citation']);
                        }
                        
                        
                        // DOWNLOAD
                        if ($download)
                        {
                            //$zp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['api_user_id']."'
                            //        AND parent = '".$zp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                            
                            if ( !is_null($zp_citation['attachment_data']) )
                            {
                                $zp_download_url = json_decode($zp_citation['attachment_data']);
                                
                                if ($zp_download_url->linkMode == "imported_file")
                                {
                                    $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$zp_citation['api_user_id']."&download=".$zp_citation["attachment_key"]."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1); // Thanks to http://ideone.com/vR073
                                }
                                else
                                {
                                    $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".$zp_download_url->url."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1);
                                }
                            }
                            unset($zp_download_url);
                        }
                        
                        
                        // CITE LINK
                        if ($cite == "yes" || $cite == "true" || $cite === true)
                        {
                            $cite_url = "https://api.zotero.org/".$zp_account->account_type."/".$zp_account->api_user_id."/items/".$zp_citation["item_key"]."?format=ris";
                            $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>" . '$2', $zp_citation['citation'], 1);
                        }
                        
                        
                        // TITLE
                        if ($title)
                        {
                            if ($current_title == "" || (strlen($current_title) > 0 && $current_title != $zp_citation["year"]))
                            {
                                $current_title = $zp_citation["year"];
                                
                                if ($zp_citation["year"] == "0000")
                                    $zp_output .= "<h3>n.d.</h3>\n";
                                else // regular year
                                    $zp_output .= "<h3>".$current_title."</h3>\n";
                            }
                        }
						
						// HYPERLINK DOIs
						if ( isset($zp_this_meta->DOI) )
							$zp_citation['citation'] = str_replace( "doi:".$zp_this_meta->DOI, "<a href='http://dx.doi.org/".$zp_this_meta->DOI."'>doi:".$zp_this_meta->DOI."</a>", $zp_citation['citation'] );
                        
                        // SHOW CURRENT STYLE AS REL
                        $zp_citation['citation'] = str_replace( "class=\"csl-bib-body\"", "rel=\"".$zp_citation['style']."\" class=\"csl-bib-body\"", $zp_citation['citation'] );
                        
                        
                        // OUTPUT
                        
                        $zp_output .= "<div class='zp-Entry".$has_citation_image."' rel='".$zp_citation["item_key"]."'>\n";
                        $zp_output .= $citation_image . $zp_citation['citation'] . $citation_abstract . $citation_tags . "\n";
                        $zp_output .= "</div><!--Entry-->\n\n";
                    }
                    
                    // DISPLAY NOTES, if exist
                    if (strlen($citation_notes) > 0)
                        $zp_output .= "<div class='zp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n" . $citation_notes . "</ol>\n</div><!-- .zp-Citation-Notes -->\n\n";
                }
                
                // No items to display
                else
                {
                    $zp_output .= "<p>Sorry, there's no items to display.</p>\n";
                }
                
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
                
            } // end items
            
            
            
            // COLLECTIONS
            
            else if ($data_type == "collections" && !isset($_GET['zpcollection']))
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
                if ($limit) $zp_query .= " LIMIT ".$limit;
                
                $zp_results = $wpdb->get_results($zp_query, OBJECT); unset($zp_query);
                
                
                // DISPLAY CITATIONS
                
                $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                $zp_output .= "<ul>\n";
                
                foreach ($zp_results as $zp_collection)
                {
                    $zp_output .= "<li rel=\"" . $zp_collection->item_key . "\">";
                    if ($link == "yes")
                    {
                        $zp_output .= "<a class='zp-CollectionLink' title='" . $zp_collection->title . "' rel='" . $zp_collection->item_key . "' href='" . $_SERVER["REQUEST_URI"];
                        if ( strpos($_SERVER["REQUEST_URI"], "?") === false ) { $zp_output .= "?"; } else { $zp_output .= "&"; }
                        $zp_output .= "zpcollection=" . $zp_collection->item_key . "'>";
                    }
                    $zp_output .= $zp_collection->title;
                    if ($link == "yes") { $zp_output .= "</a>"; }
                    $zp_output .= "</li>\n";
                    
                    if ($zp_collection->numCollections > 0)
                        $zp_output .= zp_get_subcollections($wpdb, $api_user_id, $zp_collection->item_key, $sortby, $order, $link);
                }
                
                $zp_output .= "</ul>\n";
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
                
            } // end collections
            
            
            
            // TAGS
            
            else if ($data_type == "tags" && !isset($_GET['zptag']))
            {
                $zp_query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."' ";
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default") $sortby = "retrieved";
                    else if ($sortby == "date" || $sortby == "author") continue;
                    
                    $zp_query .= " ORDER BY ".$sortby." " . $order;
                }
                
                // Limit
                if ($limit) $zp_query .= " LIMIT ".$limit;
                
                $zp_results = $wpdb->get_results($zp_query, OBJECT); unset($zp_query);
                
                
                // DISPLAY CITATIONS
                
                $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
                $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
                $zp_output .="<ul>\n";
                
                foreach ($zp_results as $zp_tag)
                {
                    $zp_output .= "<li rel=\"" . $zp_tag->title . "\">";
                    if ($link == "yes")
                    {
                        $zp_output .= "<a class='zp-TagLink' title='" . $zp_tag->title . "' rel='" . $zp_tag->title . "' href='" . $_SERVER["REQUEST_URI"];
                        if ( strpos($_SERVER["REQUEST_URI"], "?") === false ) { $zp_output .= "?"; } else { $zp_output .= "&"; }
                        $zp_output .= "zptag=" . urlencode($zp_tag->title) . "'>";
                    }
                    $zp_output .= $zp_tag->title . " <span class=\"zp-numItems\">(" . $zp_tag->numItems . " items)</span>";
                    if ($link == "yes") { $zp_output .= "</a>"; }
                    $zp_output .= "</li>\n";
                }
                
                $zp_output .="</ul>\n";
                $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
                
            } // end tags
            
            
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