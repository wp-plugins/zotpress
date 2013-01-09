<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    

    // Set up XML document
    $xml = "";
    
    

    /*
     
        ADD ZOTERO ACCOUNT
        
    */

    if (isset($_GET['connect'])) // Delete slide
    {
        // Set up error array
        $errors = array("api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
                        "api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."),
                        "public_key_blank"=>array(0,"<strong>Public Key</strong> was left blank."),
                        "public_key_format"=>array(0,"<strong>Public Key</strong> was formatted incorrectly."),
                        "nickname_format"=>array(0,"<strong>Nickname</strong> was formatted incorrectly.")
                        );
        
        
        // Check the post variables and record errors
        
        // ACCOUNT TYPE
        
        if ($_GET['account_type'] != "")
            if ($_GET['account_type'] == "groups")
                $account_type = "groups";
            else
                $account_type = "users";
        else
            $account_type = "users";
        
        // API USER ID
        
        if ($_GET['api_user_id'] != "")
            if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
                $api_user_id = htmlentities($_GET['api_user_id']);
            else
                $errors['api_user_id_format'][0] = 1;
        else
            $errors['api_user_id_blank'][0] = 1;
        
        // PUBLIC KEY
        if ($_GET['public_key'] != "")
            if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['public_key']) == 1)
                $public_key = htmlentities(trim($_GET['public_key']));
            else
                if ($account_type == "users")
                    $errors['public_key_format'][0] = 1;
        else
            if ($account_type == "users")
                $errors['public_key_blank'][0] = 1;
        
        // NICKNAME 
        
        if (isset($_GET['nickname']) && trim($_GET['nickname']) != '')
            if (preg_match('/^[\'0-9a-zA-Z ]+$/', stripslashes($_GET['nickname'])) == 1)
                $nickname = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['nickname']))));
            else
                $errors['nickname_format'][0] = 1;
        
        
        // CHECK ERRORS
        
        $errorCheck = false;
        foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                        $errorCheck = true;
                        break;
                }
        }
        
        
        // ADD ACCOUNT
        
        if ($errorCheck == false)
        {
            $query = "INSERT INTO ".$wpdb->prefix."zotpress (account_type, api_user_id, public_key";
            if ($nickname)
                $query .= ", nickname";
            $query .= ") ";
            $query .= "VALUES ('$account_type', '$api_user_id', '$public_key'";
            if ($nickname)
                $query .= ", '$nickname'";
            $query .= ")";
            
            // Insert new list item into the list:
            $wpdb->query($query);
            
            // Display success XML
            $xml .= "<result success='true' api_user_id='".$api_user_id."' public_key='".$public_key."' />\n";
        }
        
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                    if ($error[0] == 1)
                            $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }
    
    
    
    /*
     
        DELETE ACCOUNT
        
    */

    else if (isset($_GET['delete']))
    {
        if (preg_match("/^[0-9]+$/", $_GET['delete']))
        {
            // Get api_user_id
            $api_user_id = $wpdb->get_var("SELECT api_user_id FROM ".$wpdb->prefix."zotpress WHERE id='".$_GET['delete']."'");
            
            // Delete account and items
            $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress WHERE id='".$_GET['delete']."'");
            $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."'");
            $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."'");
            $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."'");
            
            $wpdb->flush();
            unset($api_user_id);
            
            // Display success XML
            $xml .= "<result success='true' />\n";
            $xml .= "<account id='".str_replace("#","",$_GET['delete'])."' type='delete' />\n";
        }
        else // die
        {
            exit();
        }
    }



    /*
     
       ADD OR UPDATE IMAGE
        
    */

    else if (isset($_GET['image']))
    {
        // Set up error array
        $errors = array("upload_image_blank"=>array(0,"<strong>Image</strong> was left blank."),
                                "upload_image_format"=>array(0,"<strong>Image</strong> was formatted incorrectly."));
        
        
        // BASIC VARS
        $citation_id = false;
        if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['citation_id']))
            $citation_id = htmlentities(trim($_GET['citation_id']));
        else
            $errorCheck = true;
        
        $api_user_id = false;
        if (preg_match("/^[0-9]+$/", $_GET['api_user_id']))
            $api_user_id = htmlentities(trim($_GET['api_user_id']));
        else
            $errorCheck = true;
        
        
       // UPLOAD IMAGE
        if ($_GET['upload_image'] != "")
            if (preg_match('/^http:\/\/[^&"\'\s]+$/', $_GET['upload_image']) == 1)
                $image = htmlentities(trim($_GET['upload_image']));
            else
                $errors['upload_image_blank'][0] = 1;
        else
            $errors['upload_image_blank'][0] = 1;
        
        
        // CHECK ERRORS
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // ADD IMAGE
        
        if ($errorCheck == false)
        {
            $query = "UPDATE ".$wpdb->prefix."zotpress_zoteroItems ";
            $query .= "SET image='$image' WHERE api_user_id='".$api_user_id."' AND item_key='".$citation_id."';";
            
            // Insert new list item into the list:
            $wpdb->query($query);
            
            // Display success XML
            $xml .= "<result success='true' citation_id='".$citation_id."' />\n";
        }
        
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }
    
    
    
    /*
     
       REMOVE IMAGE
        
    */

    else if (isset($_GET['remove']))
    {
        // CHECK ERRORS
        $errorCheck = false;
        
        $citation_id = false;
        if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['remove']))
            $citation_id = htmlentities(trim($_GET['remove']));
        else
            $errorCheck = true;
        
        $api_user_id = false;
        if (preg_match("/^[0-9]+$/", $_GET['api_user_id']))
            $api_user_id = htmlentities(trim($_GET['api_user_id']));
        else
            $errorCheck = true;
        
        
        if ($errorCheck == false)
        {
            $query = "UPDATE ".$wpdb->prefix."zotpress_zoteroItems ";
            $query .= "SET image='' WHERE api_user_id='".$api_user_id."' AND item_key='".$citation_id."';";
            
            // Update db
            $wpdb->query($query);
            
            // Display success XML
            $xml .= "<result success='true' citation_id='".$citation_id."' />\n";
        }
    }
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<accounts>\n";
    echo $xml;
    echo "</accounts>";

?>