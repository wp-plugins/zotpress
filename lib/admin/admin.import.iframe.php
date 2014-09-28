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

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
    
    // Access WordPress db
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
    
    // SET UP FUNCTIONS
    
    if ( isset($_GET['step']) )
    {
		global $current_user;
		if ( !get_user_meta($current_user->ID, 'zotpress_5_2_ignore_notice') )
			add_user_meta($current_user->ID, 'zotpress_5_2_ignore_notice', 'true', true);
	
	?><script type="text/javascript">
        
        jQuery(document).ready(function()
        {
            jQuery.ajaxSetup({timeout: 60000});
            
            
            /*
             *
             *  JQUERY IMPORT FUNCTIONS
             *
             */
            
            function zp_get_items (zp_plugin_url, api_user_id, zp_start, zp_selective)
            {
                var zp_type = "regular";
				var zp_selective_collection = "";
                
                if ( typeof(zp_selective) === "undefined" ) {
                    zp_selective = "";
                }
                else {
					zp_selective_collection = zp_selective;
                    zp_selective = "&selective="+zp_selective;
                    zp_type = "selective";
                }
				
                var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&step=items&start=" + zp_start + zp_selective;
				//alert(zpXMLurl);
                
                jQuery.get( zpXMLurl, {}, function(xml)
                {
                    var $result = jQuery("result", xml);
                    
                    if ($result.attr("success") == "true") // Move on to the next 50
                    {
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Importing items " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
						
                        if ( zp_selective_collection != "" )
						{
							zp_get_items (zp_plugin_url, api_user_id, $result.attr("next"), zp_selective_collection);
						}
						else // regular
						{
							zp_get_items (zp_plugin_url, api_user_id, $result.attr("next"));
						}
                    }
                    else if ($result.attr("success") == "next")
                    {
                        <?php if (isset($_GET["singlestep"])) { ?>
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Import of items complete!");
                        jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                        jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                        jQuery(".zp-Loading-Initial", window.parent.document).hide();
                        <?php } else { ?>
						
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Importing collections 1-50 ...");
                        
						if ( zp_type == "selective" )
                        {
                            zp_get_collections (zp_plugin_url, api_user_id, '0', zp_selective_collection);
                        }
                        else // regular
                        {
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=items", "step=collections"));
                        }
                        <?php } ?>
                    }
                    else // Show errors
                    {
                        alert( "Sorry, but there was a problem importing items: " + jQuery("errors", xml).text() );
                    }
                }).error(function(jqXHR, textStatus, errorThrown)
                {
                    alert("Sorry, but there was a problem importing items: " + errorThrown);
                });
            }
            
			
			
			
            function zp_get_collections (zp_plugin_url, api_user_id, zp_start, zp_selective)
            {
                var zp_type = "regular";
				var zp_selective_collection = "";
                
                if ( typeof(zp_selective) === "undefined" ) {
                    zp_selective = "";
                }
                else {
					zp_selective_collection = zp_selective;
                    zp_selective = "&selective="+zp_selective;
                    zp_type = "selective";
                }
                
                var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&step=collections&start=" + zp_start + zp_selective;
                
                jQuery.get( zpXMLurl, {}, function(xml)
                {
                    var $result = jQuery("result", xml);
                    
                    if ($result.attr("success") == "true") // Move on to the next 50
                    {
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Importing collections " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
						
                        if ( zp_selective_collection != "" )
						{
							// Add subcollections (if any) to collection list
							if ( jQuery("subcollections", xml) && jQuery("subcollections", xml).text() != "" )
							{
								zpCollections = zpCollections.concat( (jQuery("subcollections", xml).text()).split(',') );
							}
							// Move on to the next 50
							zp_get_collections (zp_plugin_url, api_user_id, $result.attr("next"), zp_selective_collection);
						}
						else // regular
						{
							zp_get_collections (zp_plugin_url, api_user_id, $result.attr("next"));
						}
                    }
                    else if ($result.attr("success") == "next")
                    {
                        <?php if (isset($_GET["singlestep"])) { ?>
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Import of collections complete!");
                        jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                        jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                        jQuery(".zp-Loading-Initial", window.parent.document).hide();
                        <?php } else { ?>
						
                        jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Importing tags 1-50 ...");
						
                        if ( zp_type == "selective" )
						{
							// Add subcollections (if any) to collection list
							if ( jQuery("subcollections", xml) && jQuery("subcollections", xml).text() != "" )
							{
								zpCollections = zpCollections.concat( (jQuery("subcollections", xml).text()).split(',') );
							}
							// Move on to tags
                            zp_get_tags (zp_plugin_url, api_user_id, '0', zp_selective_collection);
                        }
						else // regular
						{
                            jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#zp-Setup-Import", window.parent.document).attr('src').replace("step=collections", "step=tags"));
                        }
						<?php } ?>
                    }
                    else // Show errors
                    {
                        alert( "Sorry, but there was a problem importing collections: " + jQuery("errors", xml).text() );
                    }
                    
//                    // Add subcollections
//                    if ( jQuery("subcollections", xml) && jQuery("subcollections", xml).text() != "" )
//                    {
//						alert(jQuery("subcollections", xml).text());
//                        zpCollections = zpCollections.concat( (jQuery("subcollections", xml).text()).split(',') );
//                    }
                }).error(function(jqXHR, textStatus, errorThrown)
                {
                    alert("Sorry, but there was a problem importing collections: " + errorThrown);
                });
            }
            
			
			
			
            function zp_get_tags (zp_plugin_url, api_user_id, zp_start, zp_selective)
            {
                var zp_type = "regular";
				var zp_selective_collection = "";
                
                if ( typeof(zp_selective) === "undefined" ) {
                    zp_selective = "";
                }
                else {
					zp_selective_collection = zp_selective;
                    zp_selective = "&selective="+zp_selective;
                    zp_type = "selective";
                }
                
                var zpXMLurl = zp_plugin_url + "lib/actions/actions.import.php?api_user_id=" + api_user_id + "&step=tags&start=" + zp_start + zp_selective;
                
                jQuery.ajax({
                    url: zpXMLurl,
                    datatype: "xml",
                    retrycount: 0,
                    success: function (xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Importing tags " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
							
                            if ( zp_selective_collection != "" )
								zp_get_tags (zp_plugin_url, api_user_id, $result.attr("next"), zp_selective_collection);
							else // regular
								zp_get_tags (zp_plugin_url, api_user_id, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            <?php if (isset($_GET["singlestep"])) { ?>
                            jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Import of tags complete!");
                            jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                            jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).removeAttr("style");
                            jQuery(".zp-Loading-Initial", window.parent.document).hide();
                            <?php } else { ?>
							
                            if ( zp_type == "selective" )
                            {
                                //var zp_collection = zp_selective.substr( zp_selective.indexOf('=')+1, zp_selective.length );
								//alert("Remove from list: " + zp_selective_collection);
                                //jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Checking import status ...");
                                
                                // Remove collection from list
                                zpCollections.splice( jQuery.inArray(zp_selective_collection, zpCollections), 1 );
								//alert(zpCollections);
                                
                                // If no more (sub)collections, then finish
                                if ( zpCollections == "" )
                                {
                                    jQuery(".selective.zp-Import-Messages", window.parent.document).text("Import of selected top level collections complete!");
                                    jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
                                    jQuery(".selective.zp-Loading-Initial", window.parent.document).hide();
                                    jQuery("#zp-Zotpress-Setup-Buttons", window.parent.document).show();
                                }
                                else // Keep going ...
                                {
	                                jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Preparing to import items 1-50 from the next collection ...");
                                    zp_get_items (zp_plugin_url, api_user_id, '0', zpCollections[0]);
                                }
                            }
                            else // regular
                            {
                                jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Import complete!");
                                window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress&api_user_id=" + api_user_id;
                            }<?php } ?>
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem importing tags: " + jQuery("errors", xml).text() );
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        if ( textStatus != "timeout" )
                        {
                            alert("Sorry, but there was a problem importing tags: " + errorThrown);
                        }
                        else // timeout error
                        {
                            if ( this.retrycount < 3 )
                            {
                                jQuery.ajax(this);
                                this.retrycount++;
                            }
                            else
                            {
                                this.retrycount = 0;
                                jQuery('.'+zp_type+'.zp-Import-Messages', window.parent.document).text("Import of tags failed. Please try again.");
                                alert("Sorry, but Zotpress was unable to import all tags.");
                            }
                            return;
                        }
                    }
                });
            }
        
    <?php
        
        
        // START WITH ITEMS
        
        if ( isset($_GET['step']) && $_GET['step'] == "items")
        {
            $api_user_id = zp_get_api_user_id();
            
            // Set current import time
            zp_set_update_time( date('Y-m-d') );
            
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, $_GET['step']);
            
            ?>
            
            zp_get_items( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
            
            <?php
			
			//global $Zotpress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."zotpress", 
				array( 'version' => $GLOBALS['Zotpress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
        }
        
        
        // THEN COLLECTIONS
        
        else if (isset($_GET['step']) && $_GET['step'] == "collections")
        {
            $api_user_id = zp_get_api_user_id();
            
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "collections");
            
            ?>
            
            zp_get_collections( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
            
            <?php
			
			//global $Zotpress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."zotpress", 
				array( 'version' => $GLOBALS['Zotpress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
        }
        
        
        // THEN TAGS
        
        else if (isset($_GET['step']) && $_GET['step'] == "tags")
        {
            $api_user_id = zp_get_api_user_id();
            
            // Clear last import
            zp_clear_last_import ($wpdb, $api_user_id, "tags");
            
            ?>
            
            zp_get_tags( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
            
            <?php
			
			//global $Zotpress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."zotpress", 
				array( 'version' => $GLOBALS['Zotpress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
            
        } // step
        
        
        
        // OR SELECTIVELY IMPORT BY COLLECTION
        
        // For each collection selected for import:
        
        // <userOrGroupPrefix>/collections/<collectionKey>/collections -- subcollections -- check if they have their own subcollections and loop through for each
        // <userOrGroupPrefix>/collections/<collectionKey>/items
        // <userOrGroupPrefix>/collections/<collectionKey>/tags
        
        else if (isset($_GET['step']) && $_GET['step'] == "selective")
        {
            // Check selected top level collections
            if ( isset($_GET['collections']) && preg_match("/[0-9a-zA-Z,]+/", $_GET['collections']) )
            {
                $api_user_id = zp_get_api_user_id();
                
                // Clear last import
                zp_clear_last_import ($wpdb, $api_user_id, "selective", $_GET['collections']);
                
                // Import top level collections' data
                $GLOBALS['zp_session'][$api_user_id]['collections']['query_params'] = array();
                $GLOBALS['zp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
				
                foreach ( explode(",", $_GET['collections']) as $zp_single_collection )
                    zp_get_collections ($wpdb, $api_user_id, 0, false, false, $zp_single_collection);
                zp_save_collections ($wpdb, $api_user_id, false, false);
                
                ?>
                    var zpCollections = '<?php echo $_GET['collections']; ?>';
                    zpCollections = zpCollections.split(',');
                    
					zp_get_items( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', '0', zpCollections[0] );
					
				<?php
				
				$wpdb->update( 
					$wpdb->prefix."zotpress", 
					array( 'version' => $GLOBALS['Zotpress_update_db_by_version'] ), 
					array( 'api_user_id' => $api_user_id ), 
					array( '%s' ), 
					array( '%s' ) 
				);
                
            } else {
                ?>alert("Sorry, but the collection(s) were missing or weren't formatted correctly.");<?php
            }
            
        } // step
        ?>
        
        });
        
    </script><?php
    
    } // step
    
    ?>

    </body>
</html><?php } /* go */ ?>