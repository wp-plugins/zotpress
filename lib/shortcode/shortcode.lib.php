<?php


    require("shortcode.classes.php");
    
    
    function Zotpress_zotpressLib($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // deprecated
            'userid' => false,
            'nickname' => false,
            'nick' => false,
			
			'type' => false, // dropdown, searchbar
			'searchby' => false, // searchbar only - all [default], collections, items, tags
			'minlength' => 3, // searchbar only - 3 [default]
			'maxresults' => 100,
			'maxperpage' => 10,
			
			'cite' => false,
			'citeable' => false,
			'download' => false,
			'downloadable' => false
            
        ), $atts, "zotpress"));
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id) $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid) $api_user_id = str_replace('"','',html_entity_decode($userid));
        else $api_user_id = false;
        
        if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
        if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));
		
		
		// Type of display
		if ( $type ) $type = str_replace('"','',html_entity_decode($type));
		else $type = "dropdown";
		
		// Enqueue autocomplete UI scripts if type is "searchbar"
		if ( $type == "searchbar" )
		{
			wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'zotpress.lib.searchbar.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.lib.searchbar.js', array( 'jquery' ) );
		}
		
		
		// Filters
		if ( $searchby ) $searchby = str_replace('"','',html_entity_decode($searchby));
		
		// Min length
		if ( $minlength ) $minlength = str_replace('"','',html_entity_decode($minlength));
		
		// Max results
		if ( $maxresults ) $maxresults = str_replace('"','',html_entity_decode($maxresults));
		
		// Max per page
		if ( $maxperpage ) $maxperpage = str_replace('"','',html_entity_decode($maxperpage));
		
		// Citeable
		if ( $cite ) $cite = str_replace('"','',html_entity_decode($cite));
		if ( $citeable ) $cite = str_replace('"','',html_entity_decode($citeable));
		
		// Downloadable
		if ( $download ) $download = str_replace('"','',html_entity_decode($download));
		if ( $downloadable ) $download = str_replace('"','',html_entity_decode($downloadable));
		
		
		// Get API User ID
		
		global $wpdb;
		
        if ($nickname !== false)
        {
            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'", OBJECT);
			
			if ( is_null($zp_account) ): echo "<p>Sorry, but the selected Zotpress nickname can't be found.</p>"; return false; endif;
			
            $api_user_id = $zp_account->api_user_id;
        }
        else if ($api_user_id !== false)
        {
            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
			
			if ( is_null($zp_account) ): echo "<p>Sorry, but the selected Zotpress account can't be found.</p>"; return false; endif;
			
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
		
		
		// Use Browse class
		
		$zpLib = new zotpressBrowse;
		
		$zpLib->setAccount($zp_account);
		$zpLib->setType($type);
		if ( $searchby ) $zpLib->setFilters($searchby);
		$zpLib->setMinLength($minlength);
		$zpLib->setMaxResults($maxresults);
		$zpLib->setMaxPerPage($maxperpage);
		$zpLib->setCiteable($cite);
		$zpLib->setDownloadable($download);
		
		$zpLib->getLib();
	}

    
?>