<?php

    // Thanks to rosty dot kerei at gmail dot com at php.net
    function unicode_urldecode($url)
    {
        preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
       
        foreach ($a[1] as $uniord)
        {
            $dec = hexdec($uniord);
            $utf = '';
           
            if ($dec < 128)
            {
                $utf = chr($dec);
            }
            else if ($dec < 2048)
            {
                $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
            else
            {
                $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
           
            $url = str_replace('%u'.$uniord, $utf, $url);
        }
       
        return urldecode($url);
    }
    
    
    // Thanks to http://www.firsttube.com/read/sorting-a-multi-dimensional-array-with-php/
    function subval_sort($a, $subkey, $sort) {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
        if (strtolower($sort) == "asc")
            asort($b);
        else
            arsort ($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
    }
    
    
    function Zotpress_func($atts)
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
            'downloadable' => "no"
            
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
        else
            $collection_id = str_replace('"','',html_entity_decode($collection));
        
        if ($item_key)
            $item_key = str_replace('"','',html_entity_decode($item_key));
        else
            $item_key = str_replace('"','',html_entity_decode($item));
        
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
            $image = str_replace('"','',html_entity_decode($image));
        if ($showimage != "no")
            $image = str_replace('"','',html_entity_decode($showimage));
        
        if ($image == "true" || $image === true)
            $image = "yes";
        
        if ($download != "no")
            $download = str_replace('"','',html_entity_decode($download));
        if ($downloadable != "no")
            $download = str_replace('"','',html_entity_decode($downloadable));
        
        if ($download == "true" || $download === true)
            $download = "yes";
        
        if ($title != "no") {
            $title = str_replace('"','',html_entity_decode($title));
            $current_title = "";
        }
        
        if ($title == "true" || $title === true)
            $title = "yes";
        
        
        // GET ACCOUNTS
        
        // Connect to database
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
        $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sort.$order.$limit.$image.$download);
        
        
        // FIRST, CHECK IF REQUEST EXISTS
        
        // Display shortcode
        if ($zp_accounts_total > 0)
        {
            // INCLUDE REQUEST FUNCTION
            
            $include = true;
            require_once("zotpress.rss.php");
            $recache = false;
            $zp_output = "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>";
            
            
            // READ FORMATTED CITATION XML
            
            $zp_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $GLOBALS['zp_instance_id'], false, false, $style);
            
            $doc_citations = new DOMDocument();
            $doc_citations->loadXML($zp_xml);
            
            $zp_entries = $doc_citations->getElementsByTagName("entry");
            
            unset($zp_xml);
            
            
            // READ CITATION META XML
            
            $zp_meta_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $GLOBALS['zp_instance_id'], true, false, $style);
            
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
                        $zp_item_xml = MakeZotpressRequest($account_type, $api_user_id, "items", false, $citation_id, false, false, false, true, false, $GLOBALS['zp_instance_id'], false, true, $style);
                        
                        $item_meta = new DOMDocument();
                        $item_meta->loadXML($zp_item_xml);
                        
                        $zp_download_url = "<a class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."zotpress.rss.file.php?account_type=".$account_type."&api_user_id=".$api_user_id."&download_url=".$item_meta->getElementsByTagName("entry")->item(0)->getElementsByTagName("link")->item(3)->getAttribute('href')."'>(Download)</a>";
                        
                        $citation_content = str_replace("</div></div>", " ".$zp_download_url."</div></div>", $citation_content);
                        
                        unset($zp_item_xml);
                        unset($item_meta);
                    }
                }
                
                // GET CITATION IMAGE
                $has_citation_image = false;
                $citation_image = false;
                if (isset($image) && $image == "yes")
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
                
                $zp_citations[count($zp_citations)] = array( 'author' => $zp_this_meta->creators[0]->lastName, 'date' => date( "Y-m-d", strtotime( $zp_this_meta->date ) ), 'hasImage' => $has_citation_image, 'image' => $citation_image, 'content' => $citation_content );
            }
            
            // SORT CITATIONS
            if ($sortby)
            {
                $zp_citations = subval_sort( $zp_citations, $sortby, $sort );
            }
            
            // OUTPUT CITATIONS
            foreach ($zp_citations as $zp_citation) {
                if (isset($current_title) && $current_title == "") {
                    $current_title = date("Y", strtotime($zp_citation['date']));
                    $zp_output .= "<h3>".$current_title."</h3>\n";
                }
                else if (isset($current_title) && strlen($current_title) > 0 && $current_title != date("Y", strtotime($zp_citation['date']))) {
                    $current_title = date("Y", strtotime($zp_citation['date']));
                    $zp_output .= "<h3>".$current_title."</h3>\n";
                }
                $zp_output .= "<div class='zp-Entry".$zp_citation['hasImage']."'>\n" . $zp_citation['image'] . $zp_citation['content'] . "\n</div><!--Entry-->\n\n";
            }
            
            $zp_output .= "\n</div>\n\n";
            //$zp_output .= "\n</div>\n</div>\n\n";
            
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

    
?>