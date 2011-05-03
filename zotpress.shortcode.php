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
    
    
    function Zotpress_func($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated above}
        *   $GLOBALS['zp_shortcode_attrs']
        *   $GLOBALS['zp_account']
        *   $GLOBALS['zp_instance_id']
        *
        */
        
        extract(shortcode_atts(array(
            
            'user_id' => false,
            'nickname' => false,
            'author' => false,
            'year' => false,
            
            'data_type' => "items",
            
            'collection_id' => false,
            'item_key' => false,
            'tag_name' => false,
            
            'content' => "bib",
            'style' => "apa",
            'order' => false,
            'sort' => false,
            'limit' => "50",
            
            'image' => "no",
            'download' => "no"
            
        ), $atts));
        
        // Format attributes
        $api_user_id = str_replace('"','',html_entity_decode($user_id));
        $nickname = str_replace('"','',html_entity_decode($nickname));
        $author = str_replace('"','',html_entity_decode($author));
        $year = str_replace('"','',html_entity_decode($year));
        
        $data_type = str_replace('"','',html_entity_decode($data_type));
        
        $collection_id = str_replace('"','',html_entity_decode($collection_id));
        $item_key = str_replace('"','',html_entity_decode($item_key));
        $tag_name = str_replace('"','',html_entity_decode($tag_name));
        
        $content = str_replace('"','',html_entity_decode($content));
        $style = str_replace('"','',html_entity_decode($style));
        $order = str_replace('"','',html_entity_decode($order));
        $sort = str_replace('"','',html_entity_decode($sort));
        $limit = str_replace('"','',html_entity_decode($limit));
        
        $image = str_replace('"','',html_entity_decode($image));
        $download = str_replace('"','',html_entity_decode($download));
        if ($download == "true" || $download === true)
            $download = "yes";
        
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
        
        // Create global array with the above shortcode attributes
        $GLOBALS['zp_shortcode_attrs'] = array(
                "api_user_id" => $api_user_id,
                "nickname" => $nickname,
                "account_type" => $account_type,
                "author" => $author,
                "year" => $year,
                
                "data_type" => $data_type,
                
                "collection_id" => $collection_id,
                "item_key" => $item_key,
                "tag_name" => $tag_name,
                
                "content" => $content,
                "style" => $style,
                "order" => $order,
                "sort" => $sort,
                "limit" => $limit,
                
                "image" => $image,
                "download" => $download,
        );
        
        
        // FIRST, CHECK IF REQUEST EXISTS
        
        $zp_request_query = "SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE 
                                    api_user_id='".$api_user_id."' AND 
                                    data_type='".$data_type."' AND
                                    content='".$content."' AND
                                    ";
        if ($author)
            $zp_request_query .= "author='".$author."' AND ";
        else
            $zp_request_query .= "author IS NULL AND ";
        
        if ($year)
            $zp_request_query .= "year='".$year."' AND ";
        else
            $zp_request_query .= "year IS NULL AND ";
        
        if ($collection_id)
            $zp_request_query .= "collection_id='".$collection_id."' AND ";
        else
            $zp_request_query .= "collection_id IS NULL AND ";
        
        if ($item_key)
            $zp_request_query .= "item_key='".$item_key."' AND ";
        else
            $zp_request_query .= "item_key IS NULL AND ";
        
        if ($tag_name)
            $zp_request_query .= "tag_name='".$tag_name."' AND ";
        else
            $zp_request_query .= "tag_name IS NULL AND ";
        
        if ($order)
            $zp_request_query .= "zporder='".$order."' AND ";
        else
            $zp_request_query .= "zporder IS NULL AND ";
        
        if ($sort)
            $zp_request_query .= "sort='".$sort."' AND ";
        else
            $zp_request_query .= "sort IS NULL AND ";
        
        if ($limit)
            $zp_request_query .= "zplimit='".$limit."' AND ";
        else
            $zp_request_query .= "zplimit IS NULL AND ";
        
        if ($image)
            $zp_request_query .= "image='".$image."' AND ";
        else
            $zp_request_query .= "image IS NULL AND ";
        
        $zp_request_query .= "style='".$style."'";
        $zp_request = $wpdb->get_results($zp_request_query);
        
        // Get total matching requests (should be 0 or 1)
        $zp_request_match = $wpdb->num_rows;
        
        if ($zp_request_match > 0)
        {
            $temp = "";
            
            // Display cached citation output
            foreach ($zp_request as $key => $output)
                $temp .= unicode_urldecode( html_entity_decode( $output->zpoutput ) );
            
            return "<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>".$temp."</div>\n";
        }
        
        
        // IF THE REQUEST IS NEW, PROCEED
        
        else
        {
            // Display shortcode
            if ($zp_accounts_total > 0)
            {
                if ($GLOBALS['is_shortcode_displayed'] == false)
                {
                    add_action('wp_print_footer_scripts', 'Zotpress_theme_shortcode_script_footer');
                    add_action('wp_print_footer_scripts', 'Zotpress_theme_shortcode_display_script_footer');
                }
                
                $GLOBALS['is_shortcode_displayed'] = true;
                
                ob_start();
                include( 'zotpress.shortcode.display.php' );
                $GLOBALS['zp_shortcode_instances'][$GLOBALS['zp_instance_id']] = ob_get_contents();
                ob_end_clean();
                
                // This shortcode instance's container
                $zp_content = "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'><span class='zp-Loading'><span>loading</span></span><div class='zp-ZotpressInner'></div></div>\n";
                
                return $zp_content;
            }
            
            // Display notification if no citations found
            else {
                return "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>Sorry, no citations found.</div>\n";
            }
        } // $zp_request_match
    }
    
    function Zotpress_theme_shortcode_display_script_footer()
    {
        foreach ($GLOBALS['zp_shortcode_instances'] as $id => $zp_shortcode_instance)
            echo $zp_shortcode_instance;
        
        // Load again, this time checking for updates
        echo "
        jQuery('div#".$GLOBALS['zp_instance_id']."').one('ajaxStop', function()
        {
            for (key in window.zp_ajax_calls) {
                //alert(window.zp_ajax_calls[key]+'('+key+'/'+window.zp_ajax_calls.length+')');
                jQuery.ajax({
                    url: window.zp_ajax_calls[key].replace('&step=one', ''),
                    dataType: 'XML',
                    cache: false,
                    async: false,
                    ifModified: false // Change to true when implemented on Zotero end
                });
            }
        });
    });
    
    </script>\n\n<!-- END OF ZOTPRESS CODE -->\n\n\n";
    }
    
    function Zotpress_theme_shortcode_script_footer() {
        include('zotpress.shortcode.script.php');
    }
    
?>