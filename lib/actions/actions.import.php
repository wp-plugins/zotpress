<?php

    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
    
    // Include Request Functionality
    require("../request/rss.request.php");
    
    // Include Import and Sync Functions
    require("../admin/admin.import.functions.php");
    

    // Set up XML document
    $xml = "";
    
    

    /*
     
        AUTO-UPDATE
        
    */

    if (isset($_GET['step']))
    {
        // Set up error array
        $errors = array("api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
                        "api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."),
                        //"key_blank"=>array(0,"<strong>Key</strong> was not set."),
                        //"key_format"=>array(0,"<strong>Key</strong> was not formatted correctly."),
                        "step_blank"=>array(0,"<strong>Step</strong> was not set."),
                        "step_format"=>array(0,"<strong>Step</strong> was not formatted correctly."),
                        "start_blank"=>array(0,"<strong>Start</strong> were not set."),
                        "start_format"=>array(0,"<strong>Start</strong> were not formatted correctly.")
                        );
        
        
        // CHECK API USER ID
        
        if ($_GET['api_user_id'] != "")
            if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
                $api_user_id = htmlentities($_GET['api_user_id']);
            else
                $errors['api_user_id_format'][0] = 1;
        else
            $errors['api_user_id_blank'][0] = 1;
        
		
        // CHECK KEY
        
        //if ($_GET['key'] != "")
        //    if (preg_match("/^[0-9]+$/", $_GET['key']) == 1)
        //        $passkey = htmlentities($_GET['key']);
        //    else
        //        $errors['key_format'][0] = 1;
        //else
        //    $errors['key_blank'][0] = 1;
        
		
        // CHECK STEP
        
        if ($_GET['step'] != "")
            if (preg_match("/^[a-z]+$/", $_GET['step']) == 1)
                $step = htmlentities($_GET['step']);
            else
                $errors['step_format'][0] = 1;
        else
            $errors['step_blank'][0] = 1;
        
		
        // CHECK START
        
        if ($_GET['start'] != "")
            if (preg_match("/^[a-zA-Z0-9,]+$/", $_GET['start']) == 1)
				if ( $step == "selective")
	                $start = htmlentities($_GET['start']);
				else
	                $start = intval(htmlentities($_GET['start']));
            else
                $errors['start_format'][0] = 1;
        else
            $errors['start_blank'][0] = 1;
        
        
        // CHECK ERRORS
        
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // IMPORT
        
        if ($errorCheck == false)
        {
            // Setup
            
            $GLOBALS['zp_session'][$api_user_id]['items']['query_params'] = array();
            $GLOBALS['zp_session'][$api_user_id]['items']['query_total_entries'] = 0;
            
            $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'] = array();
            $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
            
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_params'] = array();
            $GLOBALS['zp_session'][$api_user_id]['tags']['query_total_entries'] = 0;
            
            
            // ITEMS
            
            if ( isset($_GET['step']) && $_GET['step'] == "items")
            {
                global $wpdb;
				
				$zp_selective = false;
				if ( isset($_GET['selective']) && preg_match("/^[a-zA-Z0-9,]+$/", $_GET['selective']) )
					$zp_selective = $_GET['selective'];
				
                $zp_continue = zp_get_items ($wpdb, $api_user_id, $start, $zp_selective);
                
                if ($zp_continue === true)
                {
					zp_save_items ($wpdb, $api_user_id, true);
					
					$xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
                }
                else if ($zp_continue === false)  // Execute import query, then move on
                {
                    zp_save_items ($wpdb, $api_user_id, false);
                    
                    $xml = "<result success=\"next\" next=\"collections\" />\n";
                }
                else // error
                {
                    $xml = "<result success=\"false\" />\n";
                    $xml = "<errors>". $zp_continue ."</errors>\n";
                }
            }
            
            
            // COLLECTIONS
            
            else if ( isset($_GET['step']) && $_GET['step'] == "collections")
            {
                global $wpdb;
				
				$zp_selective = false;
				if ( isset($_GET['selective']) && preg_match("/^[a-zA-Z0-9,]+$/", $_GET['selective']) ) $zp_selective = $_GET['selective'];
				
                $zp_continue = zp_get_collections ($wpdb, $api_user_id, $start, false, $zp_selective);
                
                if ($zp_continue["continue"] === true)
                {
					zp_save_collections ($wpdb, $api_user_id, true);
					
					$xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
					
					if ( isset($_GET['selective']) ) $xml .= "<subcollections>" . $zp_continue["collections"] . "</subcollections>\n";
                }
                else // Execute import query, then move on
                {
                    zp_save_collections ($wpdb, $api_user_id, false);
					//zp_link_collections ($wpdb, $api_user_id); for wp custom types
                    
                    $xml = "<result success=\"next\" next=\"tags\" />\n";
					
					if ( isset($_GET['selective']) ) $xml .= "<subcollections>" . $zp_continue["collections"] . "</subcollections>\n";
                }
            }
            
            
            // TAGS
            
            else if ( isset($_GET['step']) && $_GET['step'] == "tags")
            {
                global $wpdb;
				
				$zp_selective = false;
				if ( isset($_GET['selective']) && preg_match("/^[a-zA-Z0-9,]+$/", $_GET['selective']) ) $zp_selective = $_GET['selective'];
				
                $zp_continue = zp_get_tags ($wpdb, $api_user_id, $start, $zp_selective);
                
                if ($zp_continue === true)
                {
					zp_save_tags ($wpdb, $api_user_id, true);
					
					$xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
                }
                else // Execute import query, then move on
                {
                    zp_save_tags ($wpdb, $api_user_id, false);
                    
                    $xml = "<result success=\"next\" next=\"complete\" />\n";
                }
            }
            
        }
        
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success=\"false\" />\n";
            $xml .= "<import>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</import>\n";
        }
    }
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<import>\n";
    echo $xml;
    echo "</import>";

?>