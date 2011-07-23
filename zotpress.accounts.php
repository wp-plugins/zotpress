    
    <?php
    
    // Determine if server supports OAuth
    if (in_array ('oauth', get_loaded_extensions()))
        $oauth_is_not_installed = false;
    else
        $oauth_is_not_installed = true;
    
    ?>
    
    
    <?php if (isset( $_GET['oauth'] )) { ?>
    
    <?php include("zotpress.accounts.oauth.php"); ?>
    
    <?php } else { ?>
    
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.admin.display.tabs.php'); ?>
            
            <h3>Add a Zotero Account</h3>
            
            <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" name="zp-Add" id="zp-Add">
            
                <fieldset>
                    <input id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
                
                    <div>
                        <label for="account_type" class="required">Account Type</label>
                        <select id="account_type" name="account_type">
                            <option value="users">User</option>
                            <option value="groups">Group</option>
                        </select>
                    </div>
                
                    <div>
                        <label for="api_user_id" class="zp-Help required" title="Your User ID is listed on the Zotero 'Feeds/API' page under 'Settings', right under the 'Feeds/API Settings' heading. Group IDs are found in the group's URL, after &quot;groups&quot;. Both should be a number 1-6+ digits in length."><span id="zp-ID-Label">User ID</span></label>
                        <input id="api_user_id" name="api_user_id" type="text" />
                    </div>
                
                    <div class="zp-public_key">
                        <label for="public_key" class="zp-Help<?php if ($oauth_is_not_installed){ echo " required"; } ?>" title="<?php if (!$oauth_is_not_installed){ ?><strong>You can create a key using OAuth <u>after</u> you've added your account.</strong><?php } else { ?><strong>You are rquired to create a private key to use Zotpress. You can create one on the Zotero website.</strong><?php } ?><br />If you've already created a key, it'll be listed on the 'Feeds/API' page under 'Settings' on the Zotero website. Make sure that 'Allow third party access' is checked."><span>Private Key</span></label>
                        <input id="public_key" name="public_key" type="text" />
                    </div>
                
                    <div>
                        <label for="nickname" class="zp-Help" title="Your API User/Group ID can be hard to recognize. Make it easier for yourself by giving your account a nickname."><span>Nickname</span></label>
                        <input id="nickname" name="nickname" type="text" />
                    </div>
                
                    <div class="last">
                        <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="Submit" />
                    </div>
                    
                    <div class="message">
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Errors"><p>Errors!</p></div>
                        <div class="zp-Success"><p>Success!</p></div>                
                    </div>
                    
                    
                </fieldset>
                
            </form>
            
            
            <h3>Manage Zotero Accounts</h3>
            
            <div id="zp-Accounts">
                <div id="zp-AccountsHeader">
                    <span class="account_type first">Type</span>
                    <span class="api_user_id">User ID</span>
                    <span class="public_key">Private Key</span>
                    <span class="nickname">Nickname</span>
                    <span class="delete last">Remove</span>
                </div>
                
                <div id="zp-AccountsList"></div>
            </div>
            
            <?php if (!$oauth_is_not_installed){ ?>
            <h3>What is OAuth?</h3>
            
            <p>
                OAuth helps you create the necessary private key for allowing Zotpress to read your Zotero library and display
                it for all to see. You can do this manually through the Zotero website; using OAuth in Zotpress is just a quicker, more straightforward way of going about it.
                <strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
            </p>
            <?php } else { ?>
            <h3>Where do I get a private key?</h3>
            
            <p>
               You can generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website. Go to <strong>Settings > Feeds/API</strong> and choose "Create new private key."
            </p>
            <?php } ?>
            
            
        </div>
        
    <?php } // OAuth check ?>