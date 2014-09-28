<?php

    function Zotpress_zotpressInTextBib ($atts)
    {
        /*
        *   RELIES ON THESE GLOBAL VARIABLES:
        *
        *   $GLOBALS['zp_shortcode_instances'][get_the_ID()] {instantiated previously}
        *   
        */
        
        extract(shortcode_atts(array(
            'style' => false,
            'sortby' => "default",
            'sort' => false,
            'order' => "ASC",
            
            'image' => false,
            'images' => false,
            'showimage' => "no",
            
            'showtags' => "no",
            'title' => "no",
            'download' => "no",
            'downloadable' => false,
            'notes' => false,
            'abstract' => false,
            'abstracts' => false,
            'cite' => false,
            'citeable' => false,
            'target' => false,
            'forcenumber' => false
        ), $atts));
        
        
        
        // FORMAT PARAMETERS
        $style = str_replace('"','',html_entity_decode($style));
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order) $order = str_replace('"','',html_entity_decode($order));
        else if ($sort) $order = str_replace('"','',html_entity_decode($sort));
        
        // Show image
        if ($showimage) $showimage = str_replace('"','',html_entity_decode($showimage));
        if ($image) $showimage = str_replace('"','',html_entity_decode($image));
        if ($images) $showimage = str_replace('"','',html_entity_decode($images));
        
        if ($showimage == "yes" || $showimage == "true" || $showimage === true) $showimage = true;
        else $showimage = false;
        
        // Show tags
        if ($showtags == "yes" || $showtags == "true" || $showtags === true) $showtags = true;
        else $showtags = false;
        
        $title = str_replace('"','',html_entity_decode($title));
        
        if ($download) $download = str_replace('"','',html_entity_decode($download));
        else if ($downloadable) $download = str_replace('"','',html_entity_decode($downloadable));
        if ($download == "yes" || $download == "true" || $download === true) $download = true; else $download = false;
        
        $notes = str_replace('"','',html_entity_decode($notes));
        
        if ($abstracts) $abstracts = str_replace('"','',html_entity_decode($abstracts));
        else if ($abstract) $abstracts = str_replace('"','',html_entity_decode($abstract));
        
        if ($cite) $cite = str_replace('"','',html_entity_decode($cite));
        else if ($citeable) $cite = str_replace('"','',html_entity_decode($citeable));
        
        if ($target == "new" || $target == "yes" || $target == "_blank" || $target == "true" || $target === true) $target = true;
        else $target = false;
        
        if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
        $forcenumber = true; else $forcenumber = false;
        
        
        // SORT BY AND SORT ORDER
        if ($sortby != "default")
            $GLOBALS['zp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['zp_shortcode_instances'][get_the_ID()], $sortby, $order );
        
        // TITLE: Sort by date and add headings
        if (strtolower($title) == "yes" || strtolower($title) == "true")
            $GLOBALS['zp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['zp_shortcode_instances'][get_the_ID()], "date", $order );
        
        
        // DISPLAY IN-TEXT BIBLIOGRAPHY
        
        $current_title =  "";
        $citation_abstract = "";
        $citation_tags = "";
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
        
        foreach ($GLOBALS['zp_shortcode_instances'][get_the_ID()] as $item => $zp_citation)
        {
            $citation_image = false;
            $has_citation_image = false;
            $zp_this_meta = json_decode( $zp_citation["json"] );
            $zp_output .= "<span class=\"zp-Zotpress-Userid\" style=\"display:none;\">".$zp_citation['userid']."</span>\n\n";
            
            // AUTOUPDATE
            //if (!isset($_SESSION['zp_session'][$zp_citation['userid']]['key']))
            //    $_SESSION['zp_session'][$zp_citation['userid']]['key'] = substr(number_format(time() * rand(),0,'',''),0,10); /* Thanks to http://elementdesignllc.com/2011/06/generate-random-10-digit-number-in-php/ */
            //$zp_output .= "<span class=\"ZOTPRESS_AUTOUPDATE_KEY\" style=\"display:none;\">" . $_SESSION['zp_session'][$zp_citation['userid']]['key'] . "</span>\n\n";
            
            // IMAGE
            if ($showimage == "yes" && is_null($zp_citation["image"]) === false && $zp_citation["image"] != "")
            {
                if ( is_numeric($zp_citation["image"]) )
                {
                    $zp_citation["image"] = wp_get_attachment_image_src( $zp_citation["image"], "full" );
                    $zp_citation["image"] = $zp_citation["image"][0];
                }
                
                $citation_image = "<div id='zp-Citation-".$zp_citation["item_key"]."' class='zp-Entry-Image' rel='".$zp_citation["item_key"]."'>";
                $citation_image .= "<img src='".$zp_citation["image"]."' alt='image' />";
                $citation_image .= "</div>\n";
                $has_citation_image = " zp-HasImage";
            }
            
            // TAGS
            // Grab tags associated with item
            if ( $showtags )
            {
                global $wpdb;
                
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
                unset($zp_showtags_results);
                unset($zp_showtags_query);
            }
            
            // ABSTRACT
            if ($abstracts)
            {
                if (isset($zp_this_meta->abstractNote) && strlen(trim($zp_this_meta->abstractNote)) > 0)
                {
                    $citation_abstract = "<p class='zp-Abstract'><span class='zp-Abstract-Title'>Abstract:</span> " . sprintf($zp_this_meta->abstractNote) . "</p>\n";
                }
            }
            
            // NOTES
            if ($notes == "yes")
            {
                global $wpdb;
                
                $zp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['userid']."'
                        AND parent = '".$zp_citation["item_key"]."' AND itemType = 'note';", OBJECT);
                
                if (count($zp_notes) > 0)
                {
                    $citation_notes = "<li>\n<ul class='zp-Citation-Item-Notes'>\n";
                    
                    foreach ($zp_notes as $note) {
                        $note_json = json_decode($note->json);
                        $citation_notes .= "<li class='zp-Citation-Note'>" . $note_json->note . "\n</li>\n";
                    }
                    
                    $citation_notes .= "\n</ul>\n</li>\n\n";
                    
                    // Add note reference
                    $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <sup class=\"zp-Notes-Reference\">".$zp_notes_num."</sup> </div>" . '$2', $zp_citation['citation'], 1);
                    $zp_notes_num++;
                }
                unset($zp_notes);
            }
            
            // Hyperlink URL: Has to go before Download
            if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
            {
                $zp_url_replacement = "<a title='".$zp_this_meta->title."' rel='external' ";
                if ( $target ) $zp_url_replacement .= "target='_blank' ";
                $zp_url_replacement .= "href='".urldecode(urlencode(htmlentities($zp_this_meta->url)))."'>".urldecode(urlencode(htmlentities($zp_this_meta->url)))."</a>";
                
                // Replace ampersands
                $zp_citation['citation'] = str_replace(htmlspecialchars($zp_this_meta->url), $zp_this_meta->url, $zp_citation['citation']);
                
                // Then replace with linked URL
                $zp_citation['citation'] = str_replace($zp_this_meta->url, $zp_url_replacement, $zp_citation['citation']);
            }
            
            // DOWNLOAD
            if ( $download )
            {
                global $wpdb;
                //
                //$zp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['userid']."'
                //        AND parent = '".$zp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                
                //$zp_download_url = json_decode($zp_citation["download"]);
                
                $zp_download = $wpdb->get_results(
                        "
                        SELECT * FROM 
                        ( 
                            SELECT 
                            ".$wpdb->prefix."zotpress_zoteroItems.parent AS parent,
                            ".$wpdb->prefix."zotpress_zoteroItems.citation AS content,
                            ".$wpdb->prefix."zotpress_zoteroItems.item_key AS item_key,
                            ".$wpdb->prefix."zotpress_zoteroItems.json AS data,
                            ".$wpdb->prefix."zotpress_zoteroItems.linkmode AS linkmode 
                            FROM ".$wpdb->prefix."zotpress_zoteroItems 
                            WHERE api_user_id='".$zp_citation["userid"]."'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.parent = '".$zp_citation["item_key"]."' 
                            AND ".$wpdb->prefix."zotpress_zoteroItems.linkmode IN ( 'imported_file', 'linked_url' ) 
                            ORDER BY linkmode ASC 
                        )
                        AS attachments_sub 
                        GROUP BY parent;
                        "
                        , OBJECT
                    );
                
                if ( count($zp_download) > 0 )
                {
                    $zp_download_url = json_decode($zp_download[0]->data);
                    
                    if ($zp_download_url->linkMode == "imported_file")
                    {
                        $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$zp_citation['api_user_id']."&download=".$zp_citation["attachment_key"]."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1); // Thanks to http://ideone.com/vR073
                    }
                    else
                    {
                        $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".$zp_download_url->url."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1);
                    }
                    
                    unset($zp_download_url);
                    unset($zp_download);
                }
            }
            
            // CITE LINK
            if ($cite == "yes" || $cite == "true" || $cite === true)
            {
                $cite_url = "https://api.zotero.org/".$zp_citation["account_type"]."/".$zp_citation['userid']."/items/".$zp_citation["item_key"]."?format=ris";
                $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>" . '$2', $zp_citation['citation'], 1);
            }
            
            // TITLE
            if (strtolower($title) == "yes" || strtolower($title) == "true")
            {
                if ($current_title == "" || (strlen($current_title) > 0 && $current_title != $zp_citation["date"]))
                {
                    $current_title = $zp_citation["date"];
                    $zp_output .= "<h3>".$current_title."</h3>\n";
                }
            }
            
            // HYPERLINK DOIs
            if ( isset($zp_this_meta->DOI) )
                $zp_citation['citation'] = str_replace( "doi:".$zp_this_meta->DOI, "<a href='http://dx.doi.org/".$zp_this_meta->DOI."'>doi:".$zp_this_meta->DOI."</a>", $zp_citation['citation'] );
                
            // SHOW CURRENT STYLE AS REL
            $zp_citation['citation'] = str_replace( "class=\"csl-bib-body\"", "rel=\"".$zp_citation['style']."\" class=\"csl-bib-body\"", $zp_citation['citation'] );
            
            // OUTPUT
            $zp_output .= "<a title='Reference to citation for `".$zp_citation["title"]."`' id='zp-".get_the_ID()."-".$zp_citation["item_key"]."'></a><div class='zp-Entry".$has_citation_image."' rel='".$zp_citation["item_key"]."'>\n";
            $zp_output .= $citation_image . $zp_citation['citation'] . $citation_abstract . $citation_tags . "\n";
            $zp_output .= "</div><!--Entry-->\n\n";
        }
        
        // DISPLAY NOTES, if exist
        if (strlen($citation_notes) > 0)
            $zp_output .= "<div class='zp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n" . $citation_notes . "</ol>\n</div><!-- .zp-Citation-Notes -->\n\n";
        
        $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
        
        // Show theme scripts
        $GLOBALS['zp_is_shortcode_displayed'] = true;
        
        return $zp_output;
    }

?>