<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="zp-Setup">
        
        <h1>Zotpress Setup</h1>
        
        <div id="zp-Setup-ProcessBar">
            <div id="step-1" class="current">Step 1: Sync Account</div>
            <div id="step-2">Step 2: Default Options</div>
            <div id="step-3">Step 3: Import</div>
        </div>
        
        <div id="zp-Setup-Step">
            
            <div id="zp-AddAccount-Form" class="visible">
                <?php include('admin.accounts.addform.php'); ?>
            </div>
            
            <h3>Where do I get a private key?</h3>
            
            <p>
               You can generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website. Go to <strong>Settings > Feeds/API</strong> and choose "Create new private key."
            </p>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") { ?>

    <div id="zp-Setup">
        
        <h1>Zotpress Setup</h1>
        
        <div id="zp-Setup-ProcessBar">
            <div id="step-1">Step 1: Sync Account</div>
            <div id="step-2" class="current">Step 2: Default Options</div>
            <div id="step-3">Step 3: Import</div>
        </div>
        
        <div id="zp-Setup-Step">
            
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
        else // not set, so ...
        {
            global $wpdb;
            $api_user_id = $wpdb->get_var( "SELECT api_user_id FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1" );
        }
        
    ?>
    
    <?php $_SESSION['zp_session'][$api_user_id]['key'] = substr(number_format(time() * rand(),0,'',''),0,10); /* Thanks to http://elementdesignllc.com/2011/06/generate-random-10-digit-number-in-php/ */ ?>


    <div id="zp-Setup">
        
        <h1>Zotpress Setup<?php if ($api_user_id) { echo ": ". $api_user_id; } ?></h1>
        
        <div id="zp-Setup-ProcessBar">
            <div id="step-1">Step 1: Sync Account</div>
            <div id="step-2">Step 2: Default Options</div>
            <div id="step-3" class="current">Step 3: Import</div>
        </div>
        
        <div id="zp-Setup-Step">
            
            <h3>Import Zotero Library</h3>
            
            <p>The importing process might take a few minutes, depending on the size of your Zotero library. Don't worry&mdash;you'll only have to do this once.</p>
            
            <input id="zp-Zotpress-Setup-Import" type="button"  disabled="disabled" class="button-primary" value="Start Import" />
            <div class="zp-Loading-Initial zp-Loading-Import"></div>
            <span id="zp-Import-Messages">Importing items 1-50 ...</span>
            
            <hr class="clear" />
            
            <iframe id="zp-Setup-Import" name="zp-Setup-Import" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/admin/admin.import.php?api_user_id=<?php echo $api_user_id; ?>&key=<?php echo $_SESSION['zp_session'][$api_user_id]['key']; ?>" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0"></iframe>
            
        </div>
        
    </div>
    
<?php } ?>