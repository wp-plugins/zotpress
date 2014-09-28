<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );

    // Include import functions
    require_once("../admin/admin.import.functions.php");
    
    // Set up XML document
    $xml = "";
    
    
    if (isset($_GET['submit']))
    {
        // Set up error array
        $errors = array("account_empty"=>array(0,"<strong>Account</strong> was left blank."),
                                "account_format"=>array(0,"<strong>Account</strong> was incorrectly formatted."),
                                "editor_empty"=>array(0,"<strong>Editor</strong> was left blank."),
                                "editor_format"=>array(0,"<strong>Editor</strong> was incorrectly formatted."),
                                "style_empty"=>array(0,"<strong>Style</strong> was left blank."),
                                "style_format"=>array(0,"<strong>Style</strong> was incorrectly formatted."),
                                "reset_empty"=>array(0,"<strong>Reset</strong> was left blank."),
                                "reset_format"=>array(0,"<strong>Reset</strong> was incorrect."),
                                "cpt_empty"=>array(0,"<strong>Reference Widget</strong> was left blank."),
                                "cpt_format"=>array(0,"<strong>Reference Widget</strong> was incorrect."),
                                "autoupdate_empty"=>array(0,"<strong>Autoupdate</strong> was left blank."),
                                "autoupdate_format"=>array(0,"<strong>Autoupdate</strong> was incorrectly formatted."),
                                "post_empty"=>array(0,"<strong>Post ID</strong> was left blank."),
                                "post_format"=>array(0,"<strong>Post ID</strong> was incorrectly formatted."));
        
        
        
        /*
         
            SET DEFAULT ACCOUNT
            
        */
        
        if (isset($_GET['account']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['account']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['account'])) == 1)
                    $account = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['account']))));
                else
                    $errors['account_format'][0] = 1;
            else
                $errors['account_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultAccount", $account);
                $xml .= "<result success='true' account='".$account."' />\n";
            }
        } // default account
        
        
        
        /*
         
            SET REFERENCE WIDGET
            
        */
        
        else if (isset($_GET['cpt']))
        {
            // Check the post variables and record errors
            if (trim($_GET['cpt']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_,]+$/', stripslashes($_GET['cpt'])) == 1)
                    $cpt = trim($_GET['cpt']);
                else
                    $errors['account_format'][0] = 1;
            else
                $errors['account_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultCPT", $cpt);
                $xml .= "<result success='true' cpt='".$cpt."' />\n";
            }
        } // default reference widget
        
        
        
        /*
         
            SET DEFAULT FOR EDITOR FEATURES
            
        */
        
        else if (isset($_GET['editor']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['editor']) != '')
                if (preg_match('/^[\'a-zA-Z _]+$/', stripslashes($_GET['editor'])) == 1)
                    $editor = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['editor']))));
                else
                    $errors['editor_format'][0] = 1;
            else
                $errors['editor_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultEditor", $editor);
                $xml .= "<result success='true' editor='".$editor."' />\n";
            }
        } // default editor features
        
        
        
        /*
         
            SET AUTOUPDATE
            
        */
        
        else if (isset($_GET['autoupdate']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['autoupdate']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['autoupdate'])) == 1)
                    $autoupdate = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['autoupdate']))));
                else
                    $errors['autoupdate_format'][0] = 1;
            else
                $errors['autoupdate_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultAutoUpdate", strtolower($autoupdate));
                $xml .= "<result success='true' autoupdate='".strtolower($autoupdate)."' />\n";
            }
        } // autoupdate
        
        
        
        /*
         
            SET RESET
            
        */
        
        else if (isset($_GET['reset']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['reset']) == 'true')
                $reset = $_GET['reset'];
            else
                $errors['reset_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                global $wpdb;
                global $current_user;
                
                // Drop all tables except accounts/main
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemColl;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemTags;");
                
                /*// Delete entries/items
                $zp_entry_array = get_posts(
					array(
						'posts_per_page'   => -1,
						'post_type' => 'zp_entry'
					)
				);
				foreach ($zp_entry_array as $zp_entry) wp_delete_post( $zp_entry->ID, true );
                
                // Delete collections
                $zp_collections_array = get_terms(
					'zp_collections',
					array(
						'hide_empty' => false
					)
				);
				foreach ($zp_collections_array as $zp_collection_term) zp_delete_collection ($zp_collection_term->term_id);
                
                // Delete tags
				$zp_tags_array = get_terms(
					'zp_tags',
					array(
						'hide_empty' => false
					)
				);
				foreach ($zp_tags_array as $zp_tag_term) zp_delete_tag ($zp_tag_term->term_id);*/
                
                //delete_option( 'ZOTPRESS_PASSCODE' );
                delete_option( 'Zotpress_DefaultAccount' );
                delete_option( 'Zotpress_DefaultEditor' );
                delete_option( 'Zotpress_LastAutoUpdate' );
                delete_option( 'Zotpress_DefaultStyle' );
                delete_option( 'Zotpress_StyleList' );
                delete_option( 'Zotpress_DefaultAutoUpdate' );
                delete_option( 'Zotpress_update_version' );
                delete_option( 'Zotpress_main_db_version' );
                delete_option( 'Zotpress_oauth_db_version' );
                delete_option( 'Zotpress_zoteroItems_db_version' );
                delete_option( 'Zotpress_zoteroCollections_db_version' );
                delete_option( 'Zotpress_zoteroTags_db_version' );
                delete_option( 'Zotpress_zoteroRelItemColl_db_version' );
                delete_option( 'Zotpress_zoteroRelItemTags_db_version' );
                
                delete_user_meta( $current_user->ID, 'zotpress_5_2_ignore_notice' );
                delete_user_meta( $current_user->ID, 'zotpress_survey_notice_ignore' );
                
                $xml .= "<result success='true' reset='complete' />\n";
            }
        } // reset
        
        
        
        /*
         
            SET DEFAULT STYLE
            
        */
        
        else if (isset($_GET['style']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['style']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['style'])) == 1)
                    $style = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['style']))));
                else
                    $errors['style_format'][0] = 1;
            else
                $errors['style_empty'][0] = 1;
            
            // Only for post-specific
            if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                if (isset($_GET['post']) && trim($_GET['post']) != '')
                    if (preg_match('/^[\'0-9]+$/', stripslashes($_GET['post'])) == 1)
                        $post = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['post']))));
                    else
                        $errors['post_format'][0] = 1;
                else
                    $errors['post_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                // Update style list
                if (strpos(get_option("Zotpress_StyleList"), $style) === false)
                    update_option( "Zotpress_StyleList", get_option("Zotpress_StyleList") . ", " . $style);
                
                // Update default style
                if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                {
                    update_option("Zotpress_DefaultStyle_".$post, $style);
                    $xml .= "<result success='true' post='".$post."' style='".$style."' />\n";
                }
                else // Overal defaults
                {
                    update_option("Zotpress_DefaultStyle", $style);
                    $xml .= "<result success='true' style='".$style."' />\n";
                }
            }
        } // default style
        
        
        // DISPLAY ERRORS
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
        }
    
    } // isset(submit)
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<options>\n";
    echo $xml;
    echo "</options>";

?>