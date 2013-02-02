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
            'showimage' => "no",
            'title' => "no",
            'download' => "no",
            'downloadable' => false,
            'notes' => false,
            'abstract' => false,
            'abstracts' => false,
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
        
        if ($abstracts)
            $abstracts = str_replace('"','',html_entity_decode($abstracts));
        else if ($abstract)
            $abstracts = str_replace('"','',html_entity_decode($abstract));
        
        $cite = str_replace('"','',html_entity_decode($cite));
        
        
        // SORT BY AND SORT ORDER
        if ($sortby != "default")
            $GLOBALS['zp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['zp_shortcode_instances'][get_the_ID()], $sortby, $order );
        
        // TITLE: Sort by date and add headings
        if (strtolower($title) == "yes" || strtolower($title) == "true")
            $GLOBALS['zp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['zp_shortcode_instances'][get_the_ID()], "date", $order );
        
        
        // DISPLAY IN-TEXT BIBLIOGRAPHY
        
        $current_title =  "";
        $citation_abstract = "";
        $citation_notes = "";
        $zp_notes_num = 1;
        
        $zp_output = "\n<div class=\"zp-Zotpress\">\n\n";
        $zp_output .= "<span class=\"ZOTPRESS_PLUGIN_URL\" style=\"display:none;\">" . ZOTPRESS_PLUGIN_URL . "</span>\n\n";
        
        //$zp_output .= "<span class=\"ZOTPRESS_UPDATE_NOTICE\">Checking ...</span>\n\n";
        
        // Add style, if set
        if ($style)
            $zp_output .= "<span class=\"zp-Zotpress-Style\" style=\"display:none;\">".$style."</span>\n\n";
        
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
                $citation_image = "<div id='zp-Citation-".$zp_citation["item_key"]."' class='zp-Entry-Image' rel='".$zp_citation["item_key"]."'>";
                $citation_image .= "<img src='".$zp_citation["image"]."' alt='image' />";
                $citation_image .= "</div>\n";
                $has_citation_image = " zp-HasImage";
            }
            
            // ABSTRACT
            if ($abstracts)
            {
                if (isset($zp_this_meta->abstractNote) && strlen(trim($zp_this_meta->abstractNote)) > 0)
                {
                    $citation_abstract = "<p class='zp-Abstract'><span class='zp-Abstract-Title'>Abstract:</span> " . $zp_this_meta->abstractNote . "</p>\n";
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
            
            // Hyperlink URL: Working? Has to go before Download
            if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0) {
                $zp_citation['citation'] = str_replace(htmlentities($zp_this_meta->url), "<a title='".$zp_this_meta->title."' rel='external' href='".htmlentities($zp_this_meta->url)."'>".htmlentities($zp_this_meta->url)."</a>", $zp_citation['citation']);
            }
            
            // DOWNLOAD
            if ($download == "yes" || $download == "true" || $download === true)
            {
                global $wpdb;
                
                $zp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_citation['userid']."'
                        AND parent = '".$zp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                
                if (!is_null($zp_download_url))
                {
                    if ($zp_download_url->linkMode == "imported_file") {
                        $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$zp_citation['userid']."&download=".$zp_download_url->item_key."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1);
                    }
                    else {
                        $zp_download_meta = json_decode($zp_download_url->json);
                        $zp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='zp-DownloadURL' href='".$zp_download_meta->url."'>(Download)</a> </div>" . '$2', $zp_citation['citation'], 1);
                    }
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
            
            // SHOW CURRENT STYLE AS REL
            $zp_citation['citation'] = str_replace( "class=\"csl-bib-body\"", "rel=\"".$zp_citation['style']."\" class=\"csl-bib-body\"", $zp_citation['citation'] );
            
            // OUTPUT
            
            $zp_output .= "<a title='Reference to citation for `".$zp_citation["title"]."`' id='zp-".get_the_ID()."-".$zp_citation["item_key"]."'></a><div class='zp-Entry".$has_citation_image."' rel='".$zp_citation["item_key"]."'>\n";
            $zp_output .= $citation_image . $zp_citation['citation'] . $citation_abstract . "\n";
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