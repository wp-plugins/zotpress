<?php


    require("shortcode.classes.php");
    
    
    function Zotpress_zotpressLib($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // deprecated
            'userid' => false,
            'nickname' => false,
            'nick' => false,
            
        ), $atts, "zotpress"));
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id) $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid) $api_user_id = str_replace('"','',html_entity_decode($userid));
        else $api_user_id = false;
        
        if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
        if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));
		
		
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
		
		$zpLib->setAccount($api_user_id);
		
		$zpLib->getLib();
	}

    
?>