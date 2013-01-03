<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    
    // Access Wordpress db
    global $wpdb;
    
    // Include Special cURL
    require("../request/rss.curl.php");
    
    // Include Import and Sync Functions
    require("admin.import.functions.php");
    require("admin.sync.functions.php");
    

    // Set up XML document
    $xml = "";
    
    

    /*
     
        IMPORT UPDATE
        
    */

    if (isset($_GET['api_user_id']))
    {
        // Set up error array
        $errors = array("api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
                "api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."));
        
        
        // CHECK API USER ID
        
        if ($_GET['api_user_id'] != "")
            if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
                $api_user_id = htmlentities($_GET['api_user_id']);
            else
                $errors['api_user_id_format'][0] = 1;
        else
            $errors['api_user_id_blank'][0] = 1;
        
        // CHECK ERRORS
        
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // IMPORT AND SYNC ITEMS
        
        if ($errorCheck == false)
        {
            $debug_limit = 0;
            
            // Set current import time
            zp_set_update_time( date('Y-m-d') );
            
            // Get account
            $zp_account = zp_get_account($wpdb);
            
            // Figure out whether account needs a key
            $nokey = zp_get_account_haskey ($zp_account);
            
            // IMPORT AND SYNC ITEMS
            zp_get_server_items ($wpdb, $zp_account, $nokey, zp_get_item_count ($zp_account, $nokey), zp_get_local_items ($wpdb, $zp_account, $debug_limit), $debug_limit);
            
            // IMPORT AND SYNC COLLECTIONS
            zp_get_server_collections ($wpdb, $zp_account, $nokey, zp_get_local_collections ($wpdb, $zp_account, $debug_limit), $debug_limit);
            
            // IMPORT AND SYNC TAGS
            zp_get_server_tags ($wpdb, $zp_account, $nokey, zp_get_local_tags ($wpdb, $zp_account, $debug_limit), $debug_limit);
            
            // Display success XML
            $xml .= "<result success=\"true\" api_user_id=\"".$api_user_id."\" />\n";
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