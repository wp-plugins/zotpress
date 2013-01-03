<?php

    function Zotpress_zotpressInTextBib ($atts)
    {
        /*
        *   RELIES ON THESE GLOBAL VARIABLES:
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated previously}
        *   
        */
        
        extract(shortcode_atts(array(
            'style' => false,
            'sortby' => "default",
            'sort' => false,
            'order' => "ASC",
            'showimage' => "no",
            'title' => "no",
            'download' => "no",
            'downloadable' => false,
            'notes' => false,
            'cite' => false
        ), $atts));
        
        
        // FORMAT PARAMETERS
        $style = str_replace('"','',html_entity_decode($style));
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order)
            $order = str_replace('"','',html_entity_decode($order));
        else if ($sort)
            $order = str_replace('"','',html_entity_decode($sort));
        
        $showimage = str_replace('"','',html_entity_decode($showimage));
        $title = str_replace('"','',html_entity_decode($title));
        
        if ($download)
            $download = str_replace('"','',html_entity_decode($download));
        else if ($downloadable)
            $download = str_replace('"','',html_entity_decode($downloadable));
        
        $notes = str_replace('"','',html_entity_decode($notes));
        $cite = str_replace('"','',html_entity_decode($cite));
        
        
        // SORT BY AND SORT ORDER
        if ($sortby != "default")
            $GLOBALS['zp_shortcode_instances'] = subval_sort( $GLOBALS['zp_shortcode_instances'], $sortby, $order );
        
        // TITLE: Sort by date and add headings
        if (strtolower($title) == "yes" || strtolower($title) == "true")
            $GLOBALS['zp_shortcode_instances'] = subval_sort( $GLOBALS['zp_shortcode_instances'], "date", $order );
        
        
        // DISPLAY IN-TEXT BIBLIOGRAPHY
        
        $current_title =  "";
        
        $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
        $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
        //$zp_output .= "<span class=\"ZOTPRESS_UPDATE_NOTICE\">Checking ...</span>\n\n";
        
        // Add style, if set
        if ($style)
            $zp_output .= "<span class=\"zp-Zotpress-Style\" style=\"display:none;\">".$style."</span>\n\n";
        
        foreach ($GLOBALS['zp_shortcode_instances'] as $item => $zp_citation)
        {
            $citation_image = false;
            $has_citation_image = false;
            $citation_notes = false;
            $zp_this_meta = json_decode( $zp_citation["json"] );
            $zp_output .= "<span class=\"zp-Zotpress-Userid\" style=\"display:none;\">".$zp_citation['userid']."</span>\n\n";
            
            // IMAGE
            if ($showimage == "yes" && is_null($zp_citation["image"]) === false && $zp_citation["image"] != "")
            {
                $citation_image = "<div id='zp-Citation-".$zp_citation["item_key"]."' class='zp-Entry-Image' rel='".$zp_citation["item_key"]."'>";
                $citation_image .= "<img src='".$zp_citation["image"]."' alt='image' />";
                $citation_image .= "</div>\n";
                $has_citation_image = " zp-HasImage";
            }
            
            // NOTES
            if ($notes == "yes")
            {
                global $wpdb;
                
                $zp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['userid']."'
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
            if ($download == "yes" || $download == "true" || $download === true)
            {
                global $wpdb;
                
                $zp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['userid']."'
                        AND parent = '".$zp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                
                if (!is_null($zp_download_url))
                {
                    if ($zp_download_url->linkMode == "imported_file") {
                        $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Download URL' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$zp_citation['userid']."&download=".$zp_download_url->item_key."'>(Download)</a> </div>", $zp_citation['citation'], 1);
                    }
                    else {
                        $zp_download_meta = json_decode($zp_download_url->json);
                        $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Download URL' class='zp-DownloadURL' href='".$zp_download_meta->url."'>(Download)</a> </div>", $zp_citation['citation'], 1);
                    }
                }
            }
            
            // CITE LINK
            if ($cite == "yes" || $cite == "true" || $cite === true)
            {
                $cite_url = "https://api.zotero.org/".$zp_citation["account_type"]."/".$zp_citation['userid']."/items/".$zp_citation["item_key"]."?format=ris";
                $zp_citation['citation'] = preg_replace('/<\/div>/', " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>", $zp_citation['citation'], 1);
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
            
            // OUTPUT
            
            $zp_output .= "<div class='zp-Entry".$has_citation_image."' rel='".$zp_citation["item_key"]."'>\n";
            $zp_output .= $citation_image . $zp_citation['citation'] . $citation_notes . "\n";
            $zp_output .= "</div><!--Entry-->\n\n";
        }
        
        $zp_output .= "</div><!--.zp-Zotpress-->\n\n";
        
        // Show theme scripts
        $GLOBALS['zp_is_shortcode_displayed'] = true;
        
        return $zp_output;
        
    }

?>