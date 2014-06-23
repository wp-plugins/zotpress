<?php if ( isset( $_GET['selective'] ) && ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1 ) )
{
	global $wpdb;
	$api_user_id = htmlentities($_GET['api_user_id']);
	$api_user_id_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
	
?>

    <div id="zp-Setup" class="zp-Step-Selective">
		
		<?php include("admin.display.tabs.php"); ?>
        
        <div class="zp-Setup-Step">
            
            <h3 class="pair">Selective Import</h3>
			<h4 class="pair"><?php if (strlen($api_user_id_data->nickname) > 0) { echo $api_user_id_data->nickname; } else { echo $api_user_id; }?>'s Library</h4>
            
            <div class="zp-Step-Import">
                
                <p style="margin: 1em 0 1.8em;">
                    You can selectively import top-level collections (which includes their items, subcollections, and subcollection items) below. You may need to wait a few moments if you have several top-level collections.
                </p>
                
                <div id="zp-Step-Import-Collection" class="loading">
                    <iframe id="zp-Step-Import-Collection-Frame" name="zp-Step-Import-Collection-Frame" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/admin/admin.import.collection.php?api_user_id=<?php echo $api_user_id; ?>" scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe>
                </div><!-- #zp-Step-Import-Collection -->
                
                <input id="zp-Zotpress-Setup-Import-Selective" type="button" disabled="disabled" class="button-secondary" value="Import Selected" />
                
                <div class="zp-Loading-Container selective">
                    <div class="zp-Loading-Initial zp-Loading-Import selective"></div>
                    <div class="zp-Import-Messages selective">Importing selected collection(s) ...</div>
                </div>
                
            </div>
            
            <iframe id="zp-Setup-Import" name="zp-Setup-Import" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/admin/admin.import.iframe.php?api_user_id=<?php echo $api_user_id; ?>" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0"></iframe>
            
            <div id="zp-Zotpress-Setup-Buttons" class="proceed">
                <input type="button" id="zp-Zotpress-Setup-Options-Complete" class="button-primary" value="Finish" />
            </div>
            
        </div>
        
    </div>
    
<?php } ?>