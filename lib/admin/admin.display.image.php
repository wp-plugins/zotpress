<?php

    $zp_errors = false;
    
    // Check vars
    $zp_api_user_id = false;
    if (preg_match("/^[a-zA-Z0-9]+$/", stripslashes($_GET['api_user_id'])))
	$zp_api_user_id = stripslashes($_GET['api_user_id']);
    else
	$zp_errors = true;
    
    $zp_citation_id = false;
    if (preg_match("/^[a-zA-Z0-9]+$/", stripslashes($_GET['citation_id'])))
	$zp_citation_id = stripslashes($_GET['citation_id']);
    else
	$zp_errors = true;
    
    
    if ($zp_errors === false):
?>
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('admin.display.tabs.php'); ?>
            
            <h3>Selected Citation</h3>
            
            <div class="zp-Citation"><?php echo do_shortcode("[zotpress userid=".$zp_api_user_id." item=".$zp_citation_id."]"); ?></div>
            
            
            <h3>Upload Image</h3>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="zp-Image" id="zp-Image">
            
                <?php
                
                    if (isset($_GET['update']) && $_GET['update'] == "true")
		    {
			global $wpdb;
			$image = $wpdb->get_row("SELECT image FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$zp_api_user_id."' AND item_key='".$zp_citation_id."'", OBJECT);
                        echo "<div class='zp-Image-Current'><img src='".$image->image."' alt='image' title='Current Image' /></div>\n";
			unset($image);
		    }
                
                ?>
                
                <fieldset<?php if (isset($_GET['update'])) { ?> class="zp-Image-Current"<?php } ?>>
                    <input id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
                    <input id="account_type" name="account_type" type="hidden" value="<?php echo $zp_account_type; ?>" />
                    <input id="api_user_id" name="api_user_id" type="hidden" value="<?php echo $zp_api_user_id; ?>" />
                    <input id="citation_id" name="citation_id" type="hidden" value="<?php echo $zp_citation_id; ?>" />
                    <?php if (isset($_GET['update'])) { ?><input id="update" name="update" type="hidden" value="<?php echo $zp_citation_id; ?>" /><?php } ?>
                    
                    <div class="section">
                        <label for="upload_image" class="image">
                            <input id="upload_image" type="text" size="36" name="upload_image" value="<?php if (isset($_GET['update'])) { echo $zp_image_url; } ?>" />
                            <input id="upload_image_button" class="button-secondary" type="button" value="Browse" /><br />
                            <span class="help">Enter an URL or upload an image (150 &times; 150 pixels).</span>
                        </label>
                    </div>
                    
                    <div class="section">
                        <input id="zp-Submit" name="zp-Submit" class="button-primary" type="submit" value="Submit" />
                        <input id="zp-Cancel" name="zp-Cancel" class="button-secondary" type="button" value="Cancel" />
                        <div class="zp-Loading">loading</div>
                    </div>
                    
                    <div class="section">
                        <div class="zp-Errors">errors</div>
                        <div class="zp-Success">success</div>                
                    </div>
                    
                </fieldset>
                
                <hr class='clear' />
                
            </form>
            
        </div>
        
<?php

endif;
    
?>