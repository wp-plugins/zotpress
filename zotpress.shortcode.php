<?php


    // Include shortcode functions
    require("zotpress.shortcode.functions.php");
    
    
    function Zotpress_func($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_account']
        *   $GLOBALS['zp_instance_id']
        *
        */
        
        extract(shortcode_atts(array(
            
            'user_id' => false,
            'userid' => false,
            
            'nickname' => false,
            'author' => false,
            'year' => false,
            
            'data_type' => "items",
            'datatype' => "items",
            
            'collection_id' => false,
            'collection' => false,
            
            'item_key' => false,
            'item' => false,
            
            'tag' => false,
            'tag_name' => false,
            
            'content' => "bib",
            'style' => "apa",
            'order' => false,
            'sort' => "ASC",
            'limit' => "50",
            
            'title' => "no",
            'sortby' => false,
            
            'image' => "no",
            'showimage' => "no",
            
            'download' => "no",
            'downloadable' => "no",
            
            'note' => false,
            'notes' => false
            
        ), $atts));
        
        
        // FORMAT PARAMETERS
        
        if ($user_id)
            $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else
            $api_user_id = str_replace('"','',html_entity_decode($userid));
        
        $nickname = str_replace('"','',html_entity_decode($nickname));
        $author = str_replace('"','',html_entity_decode($author));
        $year = str_replace('"','',html_entity_decode($year));
        
        if ($data_type)
            $data_type = str_replace('"','',html_entity_decode($data_type));
        else
            $data_type = str_replace('"','',html_entity_decode($datatype));
        
        if ($collection_id)
            $collection_id = str_replace('"','',html_entity_decode($collection_id));
        else if ($collection)
            $collection_id = str_replace('"','',html_entity_decode($collection));
        else
            $collection_id = str_replace('"','',html_entity_decode($collection));
        if (strpos($collection_id, ",") > 0)
            $collection_id = explode(",", $collection_id);
        
        if ($item_key)
            $item_key = str_replace('"','',html_entity_decode($item_key));
        else
            $item_key = str_replace('"','',html_entity_decode($item));
        if (strpos($item_key, ",") > 0)
            $item_key = explode(",", $item_key);
        
        if ($tag_name)
            $tag_name = str_replace('"','',html_entity_decode($tag_name));
        else
            $tag_name = str_replace('"','',html_entity_decode($tag));
        
        $content = str_replace('"','',html_entity_decode($content));
        $style = str_replace('"','',html_entity_decode($style));
        $order = str_replace('"','',html_entity_decode($order));
        $sort = str_replace('"','',html_entity_decode($sort));
        $limit = str_replace('"','',html_entity_decode($limit));
        
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($image != "no")
            $showimage = str_replace('"','',html_entity_decode($image));
        if ($showimage != "no")
            $showimage = str_replace('"','',html_entity_decode($showimage));
        
        if ($image == "true" || $image === true)
            $showimage = "yes";
        
        if ($download != "no")
            $download = str_replace('"','',html_entity_decode($download));
        if ($downloadable != "no")
            $download = str_replace('"','',html_entity_decode($downloadable));
        
        if ($download == "true" || $download === true)
            $download = "yes";
        
        $title = str_replace('"','',html_entity_decode($title));
        if ($title == "yes" || $title == "true" || $title === true)
            $title = "yes";
        else
            $title = false;
        
        if ($note)
            if ($note == "yes")
                $notes = str_replace('"','',html_entity_decode($note));
            else
                $notes = false;
        else if ($notes)
            if ($notes == "yes")
                $notes = str_replace('"','',html_entity_decode($notes));
            else
                $notes = false;
        
        
        
        // GET ACCOUNT(S)
        
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
        
        // Set api_user_id and account type
        $api_user_id = $GLOBALS['zp_account'][0]->api_user_id;
        $account_type = $GLOBALS['zp_account'][0]->account_type;
        
        // Generate instance id for shortcode
        $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sort.$order.$limit.$showimage.$download);
        
        
        
        // CHECK IF REQUEST EXISTS
        
        // Display shortcode
        if ($zp_accounts_total > 0)
        {
            // INCLUDE REQUEST FUNCTION
            
            $include = true;
            require_once("zotpress.rss.php");
            $recache = false;
            
            
            $zp_output = "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>\n";
            //$zp_output .= "<style type='text/css'>.zp-ZotpressMeta { display: none; }</style>\n\n";
            
            
            // DEAL WITH LISTS
            if (is_array($item_key))
                foreach($item_key as $item_key)
                    $zp_output .= GenerateZotpressEntries($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, $showimage, true, $recache, $GLOBALS['zp_instance_id'], $title, $sortby, $style, $download, $notes, $author, $year);
            else if (is_array($collection_id))
                foreach($collection_id as $collection_id)
                    $zp_output .= GenerateZotpressEntries($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, $showimage, true, $recache, $GLOBALS['zp_instance_id'], $title, $sortby, $style, $download, $notes, $author, $year);
            else
                $zp_output .= GenerateZotpressEntries($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, $showimage, true, $recache, $GLOBALS['zp_instance_id'], $title, $sortby, $style, $download, $notes, $author, $year);
            
            
            $zp_output .= "\n</div><!--.zp-Zotpress-->\n\n";
            
            return $zp_output;
            
            unset($zp_images);
            unset($zp_entries);
            unset($doc_citations);
            unset($doc_images);
        }
        
        // Display notification if no citations found
        else {
            return "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>Sorry, no citation(s) found.</div>\n";
        }
    }
    
    
    
    function GenerateZotpressEntries(
            $account_type=false,
            $api_user_id=false,
            $data_type=false,
            $collection_id=false,
            $item_key=false,
            $tag_name=false,
            $limit=false,
            $showimage=false,
            $include=false,
            $recache=false,
            $instance_id=false,
            $title=false,
            $sortby=false,
            $style=false,
            $download=false,
            $notes=false,
            $author=false,
            $year=false
            )
    {
        $zp_output_entries = "";
        
        
        // READ FORMATTED CITATION XML
        $zp_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $instance_id, false, false, $style);
        //var_dump($zp_xml);
        
        $doc_citations = new DOMDocument();
        libxml_use_internal_errors(true);
        
        try {
            if (!$doc_citations->loadXML($zp_xml)) {
                throw new Exception("Sorry, but Zotpress encountered an error after attempting to contact the Zotero server.");
            }
        }
        catch(Exception $e) {
            $zp_output_entries .= $e->getMessage() ." <a href='".ZOTPRESS_PLUGIN_URL."zotpress.rss.reset.php?zp_instance_id=".$instance_id."&amp;zp_return_url=".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]."'>(Try again?)</a>\n";
        }
        
        $zp_entries = $doc_citations->getElementsByTagName("entry");
        unset($zp_xml);
        
        
        
        // READ CITATION META XML
        
        $zp_meta_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $instance_id, true, false, $style);
        
        $doc_meta = new DOMDocument();
        $doc_meta->loadXML($zp_meta_xml);
        
        $zp_meta_entries = $doc_meta->getElementsByTagName("entry");
        unset($zp_meta_xml);
        
        
        
        // Prep citation array
        $zp_citations = array();
        
        // DISPLAY EACH ENTRY
        
        foreach ($zp_entries as $entry)
        {
            // Get item type
            $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
            
            // IGNORE ATTACHMENTS
            if ($item_type == "attachment")
                continue;
            
            // Get citation ID
            $citation_id = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            
            // GET META
            foreach ($zp_meta_entries as $zp_meta_entry)
                if ($zp_meta_entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue == $citation_id)
                    $zp_this_meta = json_decode( $zp_meta_entry->getElementsByTagName("content")->item(0)->nodeValue );
            
            // FILTER BY AUTHOR
            if ($author !== false && $author !== "")
            {
                $temp_continue = false;
                
                foreach ($zp_this_meta->creators as $creator) {
                    if (str_replace(" ", "+", $creator->firstName."+".$creator->lastName) == $author)
                        $temp_continue = true;
                }
                
                if ($temp_continue === false)
                    continue;
            }
            
            // Format date
            $zp_this_meta->date = preg_replace( '/-\d{1,2}/', '', $zp_this_meta->date );
            if (strlen($zp_this_meta->date) == 4)
                $zp_this_meta->date = "January 1, ".$zp_this_meta->date;
            if (is_numeric(substr($zp_this_meta->date, 0, 3))) {
                $temp = substr($zp_this_meta->date, 0, 4);
                $zp_this_meta->date = trim(substr($zp_this_meta->date, 4, strlen($zp_this_meta->date))).", ".$temp;
            }
            
            // FILTER BY YEAR
            if ($year !== false && $year !== "" && date("Y", strtotime($zp_this_meta->date)) != $year)
                continue;
            
            
            // GET CITATION CONTENT
            $citation_html = new DOMDocument();
            foreach($entry->getElementsByTagName("content")->item(0)->childNodes as $child) {
                $citation_html->appendChild($citation_html->importNode($child, true));
                $citation_content = $citation_html->saveHTML();
                $citation_content = preg_replace( '/^\s+|\n|\r|\s+$/m', '', trim( $citation_content ) );
            }
            
            // Hyperlink URL
            if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
                $citation_content = str_replace($zp_this_meta->url, "<a title='".$zp_this_meta->title."' rel='external' href='".$zp_this_meta->url."'>".$zp_this_meta->url."</a>", $citation_content);
            
            
            // GET DOWNLOAD URL
            $zp_download_url = false;
            if (isset($download) && $download == "yes")
            {
                if ($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue > 0)
                {
                    $zp_item_xml = MakeZotpressRequest($account_type, $api_user_id, "items", false, $citation_id, false, false, false, true, false, $instance_id, false, true, $style);
                    
                    $item_meta = new DOMDocument();
                    $item_meta->loadXML($zp_item_xml);
                    
                    $zp_download_url = "<a class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."zotpress.rss.file.php?account_type=".$account_type."&api_user_id=".$api_user_id."&download_url=".$item_meta->getElementsByTagName("entry")->item(0)->getElementsByTagName("link")->item(3)->getAttribute('href')."'>(Download)</a>";
                    
                    $citation_content = str_replace("</div></div>", " ".$zp_download_url."</div></div>", $citation_content);
                    
                    unset($zp_item_xml);
                    unset($item_meta);
                }
            }
            
            // GET NOTES
            $zp_notes = false;
            if (isset($notes) && $notes == "yes")
            {
                if ($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue > 0)
                {
                    $zp_note_xml = MakeZotpressRequest($account_type, $api_user_id, "items", false, $citation_id, false, false, false, true, false, $instance_id, false, true, $style);
                    
                    $item_meta = new DOMDocument();
                    $item_meta->loadXML($zp_note_xml);
                    
                    $zp_notes = $item_meta->getElementsByTagName("entry");
                    
                    foreach ($zp_notes as $note)
                    {
                        // IGNORE ATTACHMENTS
                        if ($note->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue != "note")
                            continue;
                        
                        $note_html = new DOMDocument();
                        foreach($note->getElementsByTagName("content")->item(0)->childNodes as $child) {
                            $note_html->appendChild($note_html->importNode($child, true));
                            $note_content = $note_html->saveHTML();
                            $note_content = preg_replace( '/^\s+|\n|\r|\s+$/m', '', trim( $note_content ) );
                        }
                        $citation_content .= $note_content;
                    }
                    unset($zp_note_xml);
                    unset($item_meta);
                }
            }
            
            // GET CITATION IMAGE
            $has_citation_image = false;
            $citation_image = false;
            if (isset($showimage) && $showimage == "yes")
            {
                $zp_entry_image = $wpdb->get_results("SELECT image FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".$citation_id."'");
                
                if ($wpdb->num_rows > 0)
                {
                    $citation_image .= "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image' rel='".$citation_id."'>";
                    $citation_image .= "<img src='".$zp_entry_image[0]->image."' alt='image' />";
                    $citation_image .= "</div>\n";
                    
                    $has_citation_image = " zp-HasImage";
                }
                else {
                    $citation_image = false;
                }
            }
            
            $zp_citations[count($zp_citations)] = array( 'citation_id' => $citation_id, 'author' => $zp_this_meta->creators[0]->lastName, 'date' => date( "Y-m-d", strtotime( $zp_this_meta->date ) ), 'hasImage' => $has_citation_image, 'image' => $citation_image, 'content' => $citation_content );
        }
        
        
        
        // SORT CITATIONS
        if ($sortby)
            $zp_citations = subval_sort( $zp_citations, $sortby, $sort );
        
        
        
        // OUTPUT CITATIONS
        $current_title =  "";
        foreach ($zp_citations as $zp_citation)
        {
            if (isset($title) && $title == "yes")
            {
                if ($current_title == "" || (strlen($current_title) > 0 && $current_title != date("Y", strtotime($zp_citation['date']))))
                {
                    $current_title = date("Y", strtotime($zp_citation['date']));
                    $zp_output_entries .= "<h3>".$current_title."</h3>\n";
                }
            }
            $zp_output_entries .= "<div class='zp-Entry".$zp_citation['hasImage']."' rel='".$zp_citation['citation_id']."'>\n";
            $zp_output_entries .= $zp_citation['image'] . $zp_citation['content'] . "\n";
            //$zp_output_entries .= "<span class='zp-ZotpressMeta'>".$zp_this_meta->date."</span>\n";
            $zp_output_entries .= "</div><!--Entry-->\n\n";
        }
        
        return $zp_output_entries;
    }

    
?>