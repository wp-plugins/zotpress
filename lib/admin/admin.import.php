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

    // Access Wordpress db
    global $wpdb;
    
    // Ignore user abort
    ignore_user_abort(true);
    set_time_limit(60*10); // ten minutes
    
    // Include Special cURL
    require("../request/rss.curl.php");
    
    // Include Import Functions
    require("admin.import.functions.php");
    
    // Get session ready
    if (!session_id()) { session_start(); }
    
    
    
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
        
        if (isset($_SESSION['zp_session'][$api_user_id]['key']) && isset($_GET['key']) && $_SESSION['zp_session'][$api_user_id]['key'] == $_GET['key'])
        {
            // Get account
            $_SESSION['zp_session'][$api_user_id]['zp_account'] = zp_get_account($wpdb, $api_user_id);
            
            // Set current import time
            zp_set_update_time( date('Y-m-d') );
            
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, $_GET['step']);
            
            // Set up session item query vars
            $_SESSION['zp_session'][$api_user_id]['items']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['items']['query_total_entries'] = 0;
            
            // IMPORT ITEMS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                function zp_get_items (zp_plugin_url, api_user_id, zp_key, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&key=" + zp_key + "&step=items&start=" + zp_start;
                    
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
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing collections 1-50 ...");
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=items", "step=collections"));
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing items: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_items( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . $_SESSION['zp_session'][$api_user_id]['key']; ?>', 0);
                
            });
            
            </script><?php
        }
        else /* key fails */ { exit ("key incorrect "); }
    }
    
    
    // THEN COLLECTIONS
    
    else if (isset($_GET['step']) && $_GET['step'] == "collections")
    {
        $api_user_id = zp_get_api_user_id();
        
        if (isset($_SESSION['zp_session'][$api_user_id]['key']) && isset($_GET['key']) && $_SESSION['zp_session'][$api_user_id]['key'] == $_GET['key'])
        {
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "collections");
            
            // Set up session item query vars
            $_SESSION['zp_session'][$api_user_id]['collections']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
            
            // IMPORT COLLECTIONS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
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
                            jQuery('#zp-Import-Messages', window.parent.document).text("Importing tags 1-50 ...");
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=collections", "step=tags"));
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing collections: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_collections( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . $_SESSION['zp_session'][$api_user_id]['key']; ?>', 0);
                
            });
            
            </script><?php
        }
        else /* key fails */ { exit ("key incorrect "); }
    }
    
    
    // THEN TAGS
    
    else if (isset($_GET['step']) && $_GET['step'] == "tags")
    {
        $api_user_id = zp_get_api_user_id();
        
        if (isset($_SESSION['zp_session'][$api_user_id]['key']) && isset($_GET['key']) && $_SESSION['zp_session'][$api_user_id]['key'] == $_GET['key'])
        {
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "tags");
            
            // Set up session item query vars
            $_SESSION['zp_session'][$api_user_id]['tags']['query_params'] = array();
            $_SESSION['zp_session'][$api_user_id]['tags']['query_total_entries'] = 0;
            
            // IMPORT TAGS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
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
                            jQuery('#zp-Import-Messages', window.parent.document).text("Import complete!");
                            window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress&account_id=<?php echo $_SESSION['zp_session'][$api_user_id]['zp_account'][0]->id; ?>";
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing tags: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_tags( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id . "', '" . $_SESSION['zp_session'][$api_user_id]['key']; ?>', 0);
                
            });
            
            </script><?php
        }
        else /* key fails */ { exit ("key incorrect "); }
        
    } // step
    
    ?>

    </body>
</html><?php } /* go */ ?>