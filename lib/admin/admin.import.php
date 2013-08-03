<?php

if ( isset($_GET['go']) && $_GET['go'] == "true" )
{

    /*
    *   IMPORT PSEUDOCODE:
    *
    *   Get list of all item keys
    *   Import items in sets of 50
    *   Import categories in sets of 50
    *       Get list of all item keys for each tag
    *   Import tags in sets of 50
    *       Get list of all item keys for each tag
    *
    *   Requests to Zotero given 100 of each:
    *   1 + 2 + 2 + 100 + 2 + 100 = 207
    *
    */
    
    
    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to non-logged in users
    if ( !is_user_logged_in() ) { exit("Access denied."); }
    
    // Access Wordpress db
    global $wpdb;
    
    // Ignore user abort
    ignore_user_abort(true);
    set_time_limit(60*10); // ten minutes
    
    // Include Request Functionality
    require("../request/rss.request.php");
    
    // Include Import Functions
    require("admin.import.functions.php");
    
    
?><!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    
    <head profile="http://www.w3.org/2005/11/profile">
        <title> Importing </title>
        <script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>js/jquery.livequery.min.js"></script>
    </head>
    
    <body><?php
    
    
    // START WITH ITEMS
    
    if ( isset($_GET['step']) && $_GET['step'] == "items")
    {
        $api_user_id = zp_get_api_user_id();
        
        if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        {
            // Set current import time
            zp_set_update_time( date('Y-m-d') );
            
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, $_GET['step']);
            
            // IMPORT ITEMS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                //var zpTimeout = 0;
                jQuery.ajaxSetup({timeout: 30000});
                
                function zp_get_items (zp_plugin_url, api_user_id, zp_key, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&key=" + zp_key + "&step=items&start=" + zp_start;
                    //alert(zpXMLurl);
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing items " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_items (zp_plugin_url, api_user_id, zp_key, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            <?php if (isset($_GET["singlestep"])) { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Import of items complete!");
                            jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                            jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                            jQuery(".zp-Loading-Initial", window.parent.document).hide();
                            <?php } else { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing collections 1-50 ...");
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=items", "step=collections"));
                            <?php } ?>
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing items: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_items( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . get_option('ZOTPRESS_PASSCODE'); ?>', 0);
                
            });
            
            </script><?php
        }
        else /* key fails */ { exit ("ITEMS key incorrect "); }
    }
    
    
    // THEN COLLECTIONS
    
    else if (isset($_GET['step']) && $_GET['step'] == "collections")
    {
        $api_user_id = zp_get_api_user_id();
        
        if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        {
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "collections");
            
            // IMPORT COLLECTIONS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                jQuery.ajaxSetup({timeout: 30000});
                
                function zp_get_collections (zp_plugin_url, api_user_id, zp_key, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&key=" + zp_key + "&step=collections&start=" + zp_start;
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing collections " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_collections (zp_plugin_url, api_user_id, zp_key, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            <?php if (isset($_GET["singlestep"])) { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Import of collections complete!");
                            jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                            jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                            jQuery(".zp-Loading-Initial", window.parent.document).hide();
                            <?php } else { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing tags 1-50 ...");
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=collections", "step=tags"));
                            <?php } ?>
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing collections: " + jQuery("errors", xml).text() );
                        }
                    });
                    //.fail( function() {
                    //    alert("fail collections");
                    //    zp_get_collections (zp_plugin_url, api_user_id, zp_key, (zp_start));
                    //});
                }
                
                zp_get_collections( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . get_option('ZOTPRESS_PASSCODE'); ?>', 0);
                
            });
            
            </script><?php
        }
        else /* key fails */ { exit ("COLLECTIONS key incorrect "); }
    }
    
    
    // THEN TAGS
    
    else if (isset($_GET['step']) && $_GET['step'] == "tags")
    {
        $api_user_id = zp_get_api_user_id();
        
        if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        {
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "tags");
            
            // IMPORT TAGS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                //var zpTimeout = 0;
                jQuery.ajaxSetup({timeout: 30000});
                
                function zp_get_tags (zp_plugin_url, api_user_id, zp_key, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&key=" + zp_key + "&step=tags&start=" + zp_start;
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing tags " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_tags (zp_plugin_url, api_user_id, zp_key, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            <?php if (isset($_GET["singlestep"])) { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Import of tags complete!");
                            jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                            jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                            jQuery(".zp-Loading-Initial", window.parent.document).hide();
                            <?php } else { ?>
                            jQuery('#zp-Import-Messages', window.parent.document).text("Import complete!");
                            window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress&account_id=" + api_user_id;
                            <?php } ?>
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing tags: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_tags( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . get_option('ZOTPRESS_PASSCODE'); ?>', 0);
                
            });
            
            </script><?php
        }   
        else /* key fails */ { exit ("TAG key incorrect "); }
        
    } // step
    
    ?>

    </body>
</html><?php } /* go */ ?>