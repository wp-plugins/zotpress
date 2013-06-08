<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="zp-Setup">
        
        <div id="zp-Zotpress-Navigation">
        
            <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><strong>1:</strong> Sync Account</div>
                <div id="step-2" class="nav-item"><strong>2:</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3:</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
        <div id="zp-Setup-Step">
            
            <?php
            
            $zp_check_curl = intval( function_exists('curl_version') );
            $zp_check_streams = intval( function_exists('stream_get_contents') );
            $zp_check_fsock = intval( function_exists('fsockopen') );
            
            if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) < 1 ) { ?>
            <div id="zp-Setup-Check" class="error">
                <p><strong>Warning:</strong> Zotpress requires at least one of the following to work: cURL, fopen with Streams (PHP 5), or fsockopen. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
            </div>
            <?php } ?>
            
            <div id="zp-AddAccount-Form" class="visible">
                <?php include('admin.accounts.addform.php'); ?>
            </div>
            
            <h4>Where do I get a private key?</h4>
            
            <p>
               You can generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website. Go to <strong>Settings > Feeds/API</strong> and choose "Create new private key."
            </p>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") { ?>

    <div id="zp-Setup">
        
        <div id="zp-Zotpress-Navigation">
        
            <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1:</strong> Sync Account</div>
                <div id="step-2" class="nav-item nav-tab-active"><strong>2:</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3:</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
        <div id="zp-Setup-Step">
            
            <h3>Set Default Options</h3>
            
            <?php include("admin.options.form.php"); ?>
            
            <div id="zp-Zotpress-Setup-Buttons">
                <input type="button" id="zp-Zotpress-Setup-Options-Next" class="button-primary" value="Next" />
                <hr class="clear" />
            </div>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "three") { ?>

    <?php
    
        if (isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
        {
            $api_user_id = htmlentities($_GET['api_user_id']);
        }
        else // not set, so select last added
        {
            global $wpdb;
            $api_user_id = $wpdb->get_var( "SELECT api_user_id FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1" );
        }
        
    ?>


    <div id="zp-Setup">
        
        <div id="zp-Zotpress-Navigation">
        
            <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1:</strong> Sync Account</div>
                <div id="step-2" class="nav-item"><strong>2:</strong> Default Options</div>
                <div id="step-3" class="nav-item nav-tab-active"><strong>3:</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
        <div id="zp-Setup-Step">
            
            <?php if ($api_user_id) {
                global $wpdb;
                $temp = $wpdb->get_row("SELECT nickname FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
            ?>
            <h3>Import <?php if (strlen($temp->nickname) > 0) { echo $temp->nickname; } else { echo $api_user_id; }?>'s Library</h3>
            <?php } else { ?>
            <h3>Import Zotero Library</h3>
            <?php } ?>
            
            <p>
                The importing process might take a few minutes, depending on the size of your Zotero library.
                Don't worry&mdash;you should only have to do this once. You'll be automatically forwarded to
                the "Browse" screen when it's done.
            </p>
            
            <div id="zp-Zotpress-Setup-Import-Buttons">
                <input id="zp-Zotpress-Setup-Import" type="button" disabled="disabled" class="button-primary" value="Import Everything" />
                <input id="zp-Zotpress-Setup-Import-Items" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Items" />
                <input id="zp-Zotpress-Setup-Import-Collections" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Collections" />
                <input id="zp-Zotpress-Setup-Import-Tags" type="button" disabled="disabled" class="button-secondary zp-Import-Button" value="Import Tags" />
            <div class="zp-Loading-Initial zp-Loading-Import"></div>
            <div id="zp-Import-Messages">Importing items 1-50 ...</div>
            </div>
            
            <hr class="clear" />
            
            <iframe id="zp-Setup-Import" name="zp-Setup-Import" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/admin/admin.import.php?api_user_id=<?php echo $api_user_id; ?>&key=<?php echo get_option('ZOTPRESS_PASSCODE'); ?>" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0"></iframe>
            
            <div id="zp-Zotpress-Setup-Buttons" style="display: none;">
                <input type="button" id="zp-Zotpress-Setup-Options-Complete" class="button-primary" value="Finish" />
                <hr class="clear" />
            </div>
            
        </div>
        
    </div>
    
<?php } ?>