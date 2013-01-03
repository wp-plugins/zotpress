<?php


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
    //define( 'DIEONDBERROR', true ); // REMOVE
    
    // Include Special cURL
    require("../request/rss.curl.php");
    
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
        <script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                jQuery("#zp-Importing-Collections").livequery('load', function(event) { 
                    jQuery('#zp-Import-Messages', window.parent.document).text("Importing collections ...");
                });
                
                jQuery("#zp-Importing-Tags").livequery('load', function(event) { 
                    jQuery('#zp-Import-Messages', window.parent.document).text("Importing tags ...");
                }); 
            });
            
        </script>
    </head>
    
    <body>
    
<?php

    if (isset($_GET['go']) && $_GET['go'] == "true" && isset($_GET['step']) && $_GET['step'] == "items")
    {
        
        // Get account
        $zp_account = zp_get_account($wpdb);
        
        // Set current import time
        zp_set_update_time( date('Y-m-d') );
        
        // Clear last import
        zp_clear_last_import ($wpdb, $zp_account, $_GET['step']);
        
        // Figure out whether account needs a key
        $nokey = zp_get_account_haskey ($zp_account);
        
        // GET ITEM COUNT
        $zp_all_itemkeys_count = zp_get_item_count ($zp_account, $nokey);
        
        // DEBUGGING: Item count
        echo "ITEMS <br /><br />\n";
        //echo "item count: ". $zp_all_itemkeys_count. "<br /><br />\n";
        
        // IMPORT ITEMS
        zp_get_items ($wpdb, $zp_account, $nokey, $zp_all_itemkeys_count);
        
        
        
        ?><script type="text/javascript">
        
        jQuery(document).ready(function()
        {
            jQuery('#zp-Import-Messages', window.parent.document).text("Importing collections ...");
            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=items", "step=collections"));
        });
        
        </script><?php
        
    }
    
    else if (isset($_GET['go']) && $_GET['go'] == "true" && isset($_GET['step']) && $_GET['step'] == "collections")
    {
        // Get account
        $zp_account = zp_get_account ($wpdb);
        
        // Clear last import
        zp_clear_last_import ($wpdb, $zp_account, $_GET['step']);
        
        // Figure out whether account needs a key
        $nokey = zp_get_account_haskey ($zp_account);
        
        // DEBUGGING:
        echo "COLLECTIONS <br /><br />\n";
        
        // IMPORT COLLECTIONS
        zp_get_collections ($wpdb, $zp_account, $nokey);
        
        
        
        ?><script type="text/javascript">
        
        jQuery(document).ready(function()
        {
            jQuery('#zp-Import-Messages', window.parent.document).text("Importing tags ...");
            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=collections", "step=tags"));
        });
        
        </script><?php
        
    }
    
    else if (isset($_GET['go']) && $_GET['go'] == "true" && isset($_GET['step']) && $_GET['step'] == "tags")
    {
        // Get account
        $zp_account = zp_get_account ($wpdb);
        
        // Clear last import
        zp_clear_last_import ($wpdb, $zp_account, $_GET['step']);
        
        // Figure out whether account needs a key
        $nokey = zp_get_account_haskey ($zp_account);
        
        // DEBUGGING:
        echo "TAGS <br /><br />\n";
        
        // IMPORT TAGS
        zp_get_tags ($wpdb, $zp_account, $nokey);
        
        
        
        ?><script type="text/javascript">
        
        jQuery(document).ready(function()
        {
            jQuery('#zp-Import-Messages', window.parent.document).text("Complete!");
            window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress&account_id=<?php echo $zp_account[0]->id; ?>";
        });
        
        </script><?php
        
    } // GET: go, step

?>

    </body>
</html>