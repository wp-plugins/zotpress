<?php if ( isset( $_GET['import'] ) && ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1 ) )
{
	global $wpdb;
	$api_user_id = htmlentities($_GET['api_user_id']);
	$api_user_id_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
	
?>

    <div id="zp-Setup" class="zp-Step-Selective">
		
		<?php include("admin.display.tabs.php"); ?>
        
        <div id="zp-Setup-Step" class="import">
            
            <?php if ($api_user_id) {
                global $wpdb;
                $temp = $wpdb->get_row("SELECT nickname FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
            ?>
            <h3>Import <?php if (strlen($temp->nickname) > 0) { echo $temp->nickname; } else { echo $api_user_id; }?>'s Library</h3>
            <?php } else { ?>
            <h3>Import Zotero Library</h3>
            <?php } ?>
            
            <div id="zp-Step-Import">
                
                <p>
                    The importing process might take a few minutes, depending on what you choose to import and the size of your Zotero library.
                </p>
                
                <div id="zp-Zotpress-Setup-Import-Buttons">
                    <input id="zp-Zotpress-Setup-Import" type="button" disabled="disabled" class="button-primary" value="Import Everything" />
                    <input id="zp-Zotpress-Setup-Import-Items" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Items" />
                    <input id="zp-Zotpress-Setup-Import-Collections" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Collections" />
                    <input id="zp-Zotpress-Setup-Import-Tags" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Tags" />
                    
                    <div class="zp-Loading-Container">
                        <div class="zp-Loading-Initial zp-Loading-Import regular"></div>
                        <div class="zp-Import-Messages regular">Importing items 1-50 ...</div>
                    </div>
                </div>
                
            </div>
            
            <iframe id="zp-Setup-Import" name="zp-Setup-Import" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/admin/admin.import.iframe.php?api_user_id=<?php echo $api_user_id; ?>" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0"></iframe>
            
            <div id="zp-Zotpress-Setup-Buttons" class="proceed" style="display: none;">
                <input type="button" id="zp-Zotpress-Setup-Options-Complete" class="button-primary" value="Finish" />
            </div>
            
        </div>
        
    </div>
    
<?php } ?>