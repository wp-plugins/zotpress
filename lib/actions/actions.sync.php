<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    
    // Include Special cURL
    require("../request/rss.curl.php");
    
    // Include Import and Sync Functions
    require("../admin/admin.import.functions.php");
    require("../admin/admin.sync.functions.php");
    
    // Get session ready
    if (!session_id()) { session_start(); }
    

    // Set up XML document
    $xml = "";
    
    

    /*
     
        AUTO-UPDATE
        
    */

    if (isset($_GET['key']))
    {
        // Set up error array
        $errors = array("api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
                        "api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."),
                        "key_blank"=>array(0,"<strong>Key</strong> was not set."),
                        "key_format"=>array(0,"<strong>Key</strong> was not formatted correctly."),
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
        
        if ($_GET['key'] != "")
            if (preg_match("/^[0-9]+$/", $_GET['key']) == 1)
                $passkey = htmlentities($_GET['key']);
            else
                $errors['key_format'][0] = 1;
        else
            $errors['key_blank'][0] = 1;
        
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
            if (preg_match("/^[0-9,]+$/", $_GET['start']) == 1)
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
            // ITEMS
            
            if ( isset($_GET['step']) && $_GET['step'] == "items")
            {
                $zp_continue = zp_get_server_items ($api_user_id, $start);
                
                if ($zp_continue === true)
                {
                    if ($start % 200 == 0) // Save, then continue
                    {
                        global $wpdb;
                        zp_save_synced_items ($wpdb, $api_user_id, false);
                        
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
                    }
                    else // just continue
                    {
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" />\n";
                    }
                }
                else // Execute import query, then move on
                {
                    global $wpdb;
                    zp_save_synced_items ($wpdb, $api_user_id);
                    
                    $xml = "<result success=\"next\" next=\"collections\" />\n";
                }
            }
            
            
            // COLLECTIONS
            
            else if ( isset($_GET['step']) && $_GET['step'] == "collections")
            {
                $zp_continue = zp_get_server_collections ($api_user_id, $start);
                
                if ($zp_continue === true)
                {
                    if ($start % 200 == 0) // Save, then continue
                    {
                        global $wpdb;
                        zp_save_synced_collections ($wpdb, $api_user_id, false);
                        
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
                    }
                    else // just continue
                    {
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" />\n";
                    }
                }
                else // Execute import query, then move on
                {
                    global $wpdb;
                    zp_save_synced_collections ($wpdb, $api_user_id);
                    
                    $xml = "<result success=\"next\" next=\"tags\" />\n";
                }
            }
            
            
            // TAGS
            
            else if ( isset($_GET['step']) && $_GET['step'] == "tags")
            {
                $zp_continue = zp_get_server_tags ($api_user_id, $start);
                
                if ($zp_continue === true)
                {
                    if ($start % 200 == 0) // Save, then continue
                    {
                        global $wpdb;
                        zp_save_synced_tags ($wpdb, $api_user_id, false);
                        
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" saved=\"true\" />\n";
                    }
                    else // just continue
                    {
                        $xml = "<result success=\"true\" next=\"" . ($start+50) . "\" />\n";
                    }
                }
                else // Execute import query, then move on
                {
                    global $wpdb;
                    zp_save_synced_tags ($wpdb, $api_user_id);
                    
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