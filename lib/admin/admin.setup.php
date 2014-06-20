<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="zp-Setup">
        
        <div id="zp-Zotpress-Navigation">
        
            <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
        <div id="zp-Setup-Step">
            
            <?php
            
            $zp_check_curl = intval( function_exists('curl_version') );
            $zp_check_streams = intval( function_exists('stream_get_contents') );
            $zp_check_fsock = intval( function_exists('fsockopen') );
            
            if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) <= 1 ) { ?>
            <div id="zp-Setup-Check" class="error">
                <p><strong>Warning.</strong> Zotpress requires at least one of the following to work: cURL, fopen with Streams (PHP 5), or fsockopen. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
            </div>
            <?php } ?>
            
            <div id="zp-AddAccount-Form" class="visible">
                <?php include('admin.accounts.addform.php'); ?>
            </div>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") { ?>

    <div id="zp-Setup">
        
        <div id="zp-Zotpress-Navigation">
        
            <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item nav-tab-active"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
        <div id="zp-Setup-Step">
            
            <h3>Set Default Options</h3>
            
            <?php include("admin.options.form.php"); ?>
            
            <div id="zp-Zotpress-Setup-Buttons" class="proceed">
                <input type="button" id="zp-Zotpress-Setup-Options-Next" class="button-primary" value="Next" />
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
                <div id="step-1" class="nav-item"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item nav-tab-active"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #zp-Zotpress-Navigation -->
        
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
            
        </div>
        
        <div class="zp-Setup-Step second">
            
            <div class="zp-Step-Import">
                
                <p>
                    Alternatively, you can selectively import top-level collections below. You may need to wait a few moments if you have several top-level collections.
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
            
            <div id="zp-Zotpress-Setup-Buttons" class="proceed" style="display: none;">
                <input type="button" id="zp-Zotpress-Setup-Options-Complete" class="button-primary" value="Finish" />
            </div>
            
        </div>
        
    </div>
    
<?php } ?>