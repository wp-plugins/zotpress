            <h3>Add a Zotero Account</h3>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="zp-Add" name="zp-Add">
            
                <fieldset>
                    <input id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
					
                    <div class="field">
                        <label for="account_type" class="required">Account Type</label>
                        <select id="account_type" name="account_type" tabindex="1">
                            <option value="users">User</option>
                            <option value="groups">Group</option>
                        </select>
                    </div>
					
                    <div class="field">
                        <label for="api_user_id" class="required" title="API User ID">API User ID</label>
                        <input id="api_user_id" name="api_user_id" type="text" tabindex="2" />
						<aside>
							<p>
								For individual accounts, the API User ID is listed on the <strong>Zotero 'Feeds/API' page under the 'Feeds/API Settings' heading</strong>. For group accounts, there are two ways to find the API Group ID. Older Zotero groups will have it listed in the group's Zotero URL: a number 1-6+ digits in length after "groups." The API Group ID of new Zotero groups can be found in the <strong>RSS Feed URL</strong>.
							</p>
						</aside>
                    </div>
					
                    <div class="field zp-public_key">
                        <label for="public_key" class="zp-Help required" title="Private Key">Private Key</label>
                        <input id="public_key" name="public_key" type="text" tabindex="3" />
						<aside>
							<p>
								A private key is required for Zotpress to make requests to Zotero from WordPress.
								<?php if (isset($oauth_is_not_installed) && $oauth_is_not_installed === false) { ?><strong>You can create a key using OAuth <u>after</u> you've added your account.</strong><?php } else { ?>You can create a private key manually through the <a title="Zotero" rel="nofollow" href="http://www.zotero.org/">Zotero</a> website. Go to <strong><em>Settings > Feeds/API</em></strong> and choose "Create new private key."</strong><?php } ?>
								If you've already created a key, you can find it at <em>Settings > Feeds/API</em> on the <a title="Zotero" rel="nofollow" href="http://www.zotero.org/">Zotero</a> website. Make sure that <strong>"Allow read access"</strong> is checked. For groups, make sure the Default Group Permissions or Specific Group Permissions are set to "<strong>Read Only</strong>" or "Read/Write."
							</p>
						</aside>
                    </div>
					
                    <div class="field last">
                        <label for="nickname" class="zp-Help" title="Nickname"><span>Nickname</span></label>
                        <input id="nickname" name="nickname" type="text" tabindex="4" />
						<aside>
							<p>
								Your API User/Group ID can be hard to remember. Make it easier for yourself by giving your account a nickname.
							</p>
						</aside>
                    </div>
					
                    <div class="proceed">
                        <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="Validate" tabindex="5" />
                    </div>
                    
                    <div class="message">
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Errors"><p>Errors!</p></div>
                        <div class="zp-Success"><p>Success!</p></div>
                    </div>
                    
                </fieldset>
                
            </form>