<?php


    
    function Zotpress_zotpressInText ($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated previously}
        *   $GLOBALS['zp_shortcode_attrs']
        *   $GLOBALS['zp_account']
        *   $GLOBALS['zp_instance_id']
        *
        */
        
        extract(shortcode_atts(array(
            
            'item' => false,
            'pages' => false,
            
            'userid' => false,
            'nickname' => false
            
        ), $atts));
        
        
        // FORMAT PARAMETERS
        
        if ($item)
            $item = str_replace('"','',html_entity_decode($item));
        else
            $item = "n/a";
        
        if ($userid)
            $api_user_id = str_replace('"','',html_entity_decode($userid));
        else
            $api_user_id = false;
        
        if ($nickname)
            $nickname = str_replace('"','',html_entity_decode($nickname));
        else
            $nickname = false;
        
        
        // GET ACCOUNTS
        
        global $wpdb;
        
        // Get account and private key
        if ($api_user_id != false)
            $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'");
        else if ($nickname != false)
            $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'");
        else if ($api_user_id == false && $nickname == false)
            $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");
        
        // Get total accounts
        $zp_accounts_total = $wpdb->num_rows;
        
        // Generate instance id for shortcode
        $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname."items".$item."bib"."apa"."ASC"."50"."no"."no");
        
        
        // GET ITEM META DATA FROM ZOTERO
        
        if ($zp_accounts_total > 0)
        {
            // INCLUDE REQUEST FUNCTION
            
            $include = true;
            require_once("zotpress.rss.php");
            
            
            // Default style, per post or overall
            $zp_default_style = "apa";
            if (get_option("Zotpress_DefaultStyle_". get_the_ID()))
                $zp_default_style = get_option("Zotpress_DefaultStyle_". get_the_ID());
            else
                if (get_option("Zotpress_DefaultStyle"))
                    $zp_default_style = get_option("Zotpress_DefaultStyle");
            
            
            // READ FORMATTED CITATION HTML
            
            $zp_xml = MakeZotpressRequest($GLOBALS['zp_account'][0]->account_type, $GLOBALS['zp_account'][0]->api_user_id, "items", false, $item, false, 1, false, true, false, $GLOBALS['zp_instance_id'], false, false, $zp_default_style);
            
            $doc_citations = new DOMDocument();
            libxml_use_internal_errors(true);
            
            try {
                if (!$doc_citations->loadXML($zp_xml)) {
                    throw new Exception("Sorry, but Zotpress encountered an error after attempting to contact the Zotero server.");
                }
                
            }
            catch(Exception $e) {
                $zp_output .= $e->getMessage() ." <a href='".ZOTPRESS_PLUGIN_URL."zotpress.rss.reset.php?zp_instance_id=".$GLOBALS['zp_instance_id']."&amp;zp_return_url=".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]."'>(Try again?)</a>\n";
            }
            
            $zp_entries = $doc_citations->getElementsByTagName("entry");
            
            unset($zp_xml);
            
            
            // READ CITATION META XML
            
            $zp_meta_xml = MakeZotpressRequest($GLOBALS['zp_account'][0]->account_type, $GLOBALS['zp_account'][0]->api_user_id, "items", false, $item, false, 1, false, true, false, $GLOBALS['zp_instance_id'], true, false, $zp_default_style);
            
            $doc_meta = new DOMDocument();
            $doc_meta->loadXML($zp_meta_xml);
            
            $zp_meta_entries = $doc_meta->getElementsByTagName("entry");
            
            unset($zp_meta_xml);
            
            
            // GET META
            foreach ($zp_meta_entries as $zp_meta_entry)
                if ($zp_meta_entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue == $item)
                    $zp_this_meta = json_decode( $zp_meta_entry->getElementsByTagName("content")->item(0)->nodeValue );
            
            
            // GET CITATION CONTENT
            $citation_content = "";
            foreach ($zp_entries as $entry)
            {
                $citation_html = new DOMDocument();
                foreach($entry->getElementsByTagName("content")->item(0)->childNodes as $child) {
                    $citation_html->appendChild($citation_html->importNode($child, true));
                    $citation_content = $citation_html->saveHTML();
                    $citation_content = preg_replace( '/^\s+|\n|\r|\s+$/m', '', trim( $citation_content ) );
                }
                
                // Hyperlink URL
                if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
                    $citation_content = str_replace($zp_this_meta->url, "<a title='".$zp_this_meta->title."' rel='external' href='".$zp_this_meta->url."'>".$zp_this_meta->url."</a>", $citation_content);
            }
            
            
            // DISPLAY IN-TEXT CITATION
            $zp_output = "<span rel='".$item."' class='zp-ZotpressInText'>(";
            
            // Author
            $zp_output .= $zp_this_meta->creators[0]->lastName;
            if (count($zp_this_meta->creators) > 1)
                $zp_output .= " et al.";
            $zp_output .= ", ";
            
            // Date
            $zp_this_meta->date = preg_replace( '/-\d{1,2}/', '', $zp_this_meta->date );
            $zp_output .= $zp_this_meta->date;
            
            // Pages, if specified
            if ($pages)
                if (strpos($pages, "-") !== false)
                    $zp_output .= ", pp. ".$pages;
                else
                    $zp_output .= ", p. ".$pages;
            
            $zp_output .= ")</span>";
            
            
            // Add to shortcode instances
            $GLOBALS['zp_shortcode_instances'][count($GLOBALS['zp_shortcode_instances'])] = "<div class=\"zp-Entry\" rel=\"".$item."\">" . preg_replace('/[\r\n]+/', "", $citation_content) . "</div>\n";
        }
        
        return $zp_output;
    }

    
?>