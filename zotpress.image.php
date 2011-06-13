<?php

    if (isset($_GET['delete']))
    {
        require('../../../wp-load.php');
        
	if (!defined('WP_USE_THEMES'))
		define('WP_USE_THEMES', false);
        
        /*
         
            DELETE IMAGE
            
        */
        
        global $wpdb;
        
        $query = "DELETE FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".htmlentities(trim($_GET['delete']))."'";
        $wpdb->query($query);
        
        // Display success XML
        header('Content-Type: application/xml; charset=ISO-8859-1');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        echo "<image>\n";
        echo "<result success='true' />\n";
        echo "<image citation_id='".htmlentities(trim($_GET['delete']))."' type='delete' />\n";
        echo "</image>";
    }
    
    // Display image form
    else
    {
?>
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.display.tabs.php'); ?>
            
            <h3>Selected Citation</h3>
            
            <div class="zp-Citation"><?php echo stripslashes($_GET['citation']); ?></div>
            
            
            <h3>Upload Image</h3>
            
            <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" name="zp-Image" id="zp-Image">
            
                <?php
                
                    if (isset($_GET['update']) && $_GET['update'] == "true" && isset($_GET['image_url']))
                        echo "<div class='zp-Image-Current'><img src='".$_GET['image_url']."' alt='image' title='Current Image' /></div>\n";
                
                ?>
            
                <fieldset<?php if (isset($_GET['update'])) { ?> class="zp-Image-Current"<?php } ?>>
                    <input id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
                    <input id="account_type" name="account_type" type="hidden" value="<?php echo $_GET['account_type']; ?>" />
                    <input id="api_user_id" name="api_user_id" type="hidden" value="<?php echo $_GET['api_user_id']; ?>" />
                    <input id="citation_id" name="citation_id" type="hidden" value="<?php echo $_GET['citation_id']; ?>" />
                    <?php if (isset($_GET['update'])) { ?><input id="update" name="update" type="hidden" value="<?php echo $_GET['citation_id']; ?>" /><?php } ?>
                    
                    <div class="section">
                        <label for="upload_image" class="image">
                            <input id="upload_image" type="text" size="36" name="upload_image" value="<?php if (isset($_GET['update'])) { echo $_GET['image_url']; } ?>" />
                            <input id="upload_image_button" class="button-secondary" type="button" value="Browse" /><br />
                            <span class="help">Enter an URL or upload an image (150 &times; 150 pixels).</span>
                        </label>
                    </div>
                    
                    <div class="section">
                        <input id="zp-Submit" name="zp-Submit" class="button-primary" type="submit" value="Submit" />
                        <input id="zp-Cancel" name="zp-Cancel" class="button-secondary" type="button" value="Cancel" />
                    </div>
                    
                    <div class="section">
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Errors">errors</div>
                        <div class="zp-Success">success</div>                
                    </div>
                    
                </fieldset>
                
                <hr class='clear' />
                
            </form>
            
        </div>
        
<?php } ?>