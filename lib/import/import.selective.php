<?php if ( isset( $_GET['selective'] ) && ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1 ) )
{
	global $wpdb;
	$api_user_id = htmlentities($_GET['api_user_id']);
	$api_user_id_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
	
?>

    <div id="zp-Setup" class="zp-Step-Selective">
		
		<?php include( dirname(__FILE__) . '/../admin/admin.menu.php' ); ?>
        
        <div class="zp-Setup-Step">
            
            <h3 class="pair">Selective Import</h3>
			<h4 class="pair"><?php if (strlen($api_user_id_data->nickname) > 0) { echo $api_user_id_data->nickname; } else { echo $api_user_id; }?>'s Library</h4>
            
            <div class="zp-Step-Import">
                
                <p style="margin: 1em 0 1.8em;">
                    You can selectively import top-level collections (which includes their items, subcollections, and subcollection items) below. You may need to wait a few moments if you have several top-level collections.
                </p>
                
                <div id="zp-Step-Import-Collection" class="loading">
                    <iframe id="zp-Step-Import-Collection-Frame" name="zp-Step-Import-Collection-Frame"
							src="<?php echo wp_nonce_url( ZOTPRESS_PLUGIN_URL . 'lib/import/import.collection.php?api_user_id=' . $api_user_id, 'zp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'zp_nonce' ); ?>"
							scrolling="no" frameborder="0" marginwidth="0" marginheight="0">
					</iframe>
                </div><!-- #zp-Step-Import-Collection -->
                
                <input id="zp-Zotpress-Setup-Import-Selective" type="button" disabled="disabled" class="button button-primary" value="Import Selected" />
                
                <div class="zp-Loading-Container selective">
                    <div class="zp-Loading-Initial zp-Loading-Import selective"></div>
                    <div class="zp-Import-Messages selective">Importing selected collection(s) ...</div>
                </div>
                
            </div>
            
            <iframe id="zp-Setup-Import" name="zp-Setup-Import"
				src="<?php echo wp_nonce_url( ZOTPRESS_PLUGIN_URL . 'lib/import/import.iframe.php?api_user_id=' . $api_user_id, 'zp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'zp_nonce' ); ?>"
				scrolling="yes" frameborder="0" marginwidth="0" marginheight="0">
			</iframe>
            
            <div id="zp-Zotpress-Setup-Buttons" class="proceed">
				<a title="Go to Browse" id="zp-Import-Browse-Button" class="button button-secondary" href="admin.php?page=Zotpress">Browse Library</a>
				<a title="Go to Accounts" id="zp-Import-Accounts-Button" class="button button-secondary" href="admin.php?page=Zotpress&accounts=true">Go to Accounts</a>
            </div>
            
        </div>
        
    </div>
    
<?php } ?>