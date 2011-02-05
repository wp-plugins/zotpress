        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.tabs.php'); ?>
            
            <h3>Add a Zotero Account</h3>
            
            <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" name="zp-Add" id="zp-Add">
            
                <fieldset>
                    <input id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
                
                    <div>
                        <label for="account_type">Account Type</label>
                        <select id="account_type" name="account_type">
                            <option value="users">User</option>
                            <option value="groups">Group</option>
                        </select>
                    </div>
                
                    <div>
                        <label for="api_user_id"><span id="zp-ID-Label">API User</span> ID <span class="zp-Help" title="Your API User ID is listed on the Zotero 'Feeds/API' page under 'Settings', right under the 'Feeds/API Settings' heading. Group IDs are found in the group's URL, after &quot;groups&quot;. Both should be a series of five digits.">help</span></label>
                        <input id="api_user_id" name="api_user_id" type="text" />
                    </div>
                
                    <div class="zp-public_key">
                        <label for="public_key">Public Key <span class="zp-Help" title="Create a key on the Zotero 'Feeds/API' page under 'Settings'. Make sure that 'Allow third party access' is checked.">help</span></label>
                        <input id="public_key" name="public_key" type="text" />
                    </div>
                
                    <div>
                        <label for="nickname">Nickname <span class="zp-Help" title="Your API User/Group ID can be hard to recognize. Make it easier for yourself by giving your account a nickname.">help</span></label>
                        <input id="nickname" name="nickname" type="text" />
                    </div>
                
                    <div class="last">
                        <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="Submit" />
                    </div>
                    
                    <div class="last">
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Errors">errors</div>
                        <div class="zp-Success">success</div>                
                    </div>
                    
                    
                </fieldset>
                
            </form>
            
            
            <h3>Manage Zotero Accounts</h3>
            
            <div id="zp-Accounts">
                <div id="zp-AccountsHeader">
                    <span class="account_type first">Type</span>
                    <span class="api_user_id">API ID</span>
                    <span class="public_key">Public Key</span>
                    <span class="nickname">Nickname</span>
                    <span class="delete last">Remove</span>
                </div>
                
                <div id="zp-AccountsList"></div>
            </div>
            
            
        </div>