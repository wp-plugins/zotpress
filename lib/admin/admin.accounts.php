<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{

	// Determine if server supports OAuth
	if (in_array ('oauth', get_loaded_extensions())) { $oauth_is_not_installed = false; } else { $oauth_is_not_installed = true; }
	
	if (isset( $_GET['oauth'] )) { include("admin.accounts.oauth.php"); } else {
	
	?>
	
		<div id="zp-Zotpress" class="wrap">
			
			<?php include('admin.display.tabs.php'); ?>
			
			
			<!-- ZOTPRESS MANAGE ACCOUNTS -->
			
			<div id="zp-ManageAccounts">
				
				<h3>Synced Zotero Accounts</h3>
				<?php if (!isset( $_GET['no_accounts'] ) || (isset( $_GET['no_accounts'] ) && $_GET['no_accounts'] != "true")) { ?><a title="Sync your Zotero account" class="zp-AddAccountButton button button-secondary" href="<?php echo admin_url("admin.php?page=Zotpress&setup=true"); ?>"><span>Add account</span></a><?php } ?>
				
				<table id="zp-Accounts" class="wp-list-table widefat fixed posts">
					
					<thead>
						<tr>
							<th class="account_type first manage-column" scope="col">Type</th>
							<th class="api_user_id manage-column" scope="col">User ID</th>
							<th class="public_key manage-column" scope="col">Private Key</th>
							<th class="nickname manage-column" scope="col">Nickname</th>
							<th class="status manage-column" scope="col">Status</th>
							<th class="actions last manage-column" scope="col">Actions</th>
						</tr>
					</thead>
					
					<tbody id="zp-AccountsList">
						<?php
							
							global $wpdb;
							//global $Zotpress_update_version;
							
							$accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress");
							$zebra = " alternate";
							
							foreach ($accounts as $num => $account)
							{
								if ($num % 2 == 0) { $zebra = " alternate"; } else { $zebra = ""; }
								
								$code = "<tr id='zp-Account-" . $account->api_user_id . "' class='zp-Account".$zebra."' rel='" . $account->api_user_id . "'>\n";
								
								// ACCOUNT TYPE
								$code .= "                          <td class='account_type first'>" . substr($account->account_type, 0, -1) . "</td>\n";
								
								// API USER ID
								$code .= "                          <td class='api_user_id'>" . $account->api_user_id . "</td>\n";
								
								// PUBLIC KEY
								$code .= "                          <td class='public_key'>";
								if ($account->public_key)
								{
									$code .= $account->public_key;
								}
								else
								{
									$code .= 'No private key entered. <a class="zp-OAuth-Button" href="'.get_bloginfo( 'url' ).'/wp-content/plugins/zotpress/lib/admin/admin.accounts.oauth.php?oauth_user='.$account->api_user_id.'&amp;return_uri='.get_bloginfo('url').'">Start OAuth?</a>';
								}
								$code .= "</td>\n";
								
								// NICKNAME
								$code .= "                          <td class='nickname'>";
								if ($account->nickname)
									$code .= $account->nickname;
								$code .= "</td>\n";
								
								// STATUS
								$code .= "                          <td class='status'>";
								if ( $account->version != $GLOBALS['Zotpress_update_db_by_version'] )
									$code .= "<span class='status_bad'>&#10007;</span>";
								else
									$code .= "<span class='status_good'>&#10004;</span>";
								$code .= "</td>\n";
								
								// ACTIONS
								$code .= "                          <td class='actions last'>\n";
								//$code .= "                              <a title='Sync' class='sync' rel='".$account->api_user_id."' href='javascript:void(0);'><span class='icon'></span>Sync</a>\n";
								$code .= "                              <a title='Selective Import' class='selective' rel='".$account->api_user_id."' href='admin.php?page=Zotpress&selective=true&api_user_id=" . $account->api_user_id . "'>Selective Import</a>\n";
								$code .= "                              <a title='(Re)Import' class='import' href='admin.php?page=Zotpress&import=true&api_user_id=" . $account->api_user_id . "'>Import</a>\n";
								$code .= "                              <a title='Remove this account' class='delete' href='#" . $account->id . "'>Remove</a>\n";
								//$code .= "                              <span class='zp-Sync-Messages'>&nbsp;</span>\n";
								$code .= "                          </td>\n";
								
								$code .= "                         </tr>\n\n";
								
								echo $code;
							}
						?>
					</tbody>
					
				</table>
				
			</div>
			
			<span id="ZOTPRESS_PLUGIN_URL" style="display: none;"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
			<span id="ZOTPRESS_PASSCODE" style="display: none;"><?php /*echo get_option('ZOTPRESS_PASSCODE'); */ ?></span>
			
			<?php if (!$oauth_is_not_installed){ ?>
				<h3>What is OAuth?</h3>
				
				<p>
					OAuth helps you create the necessary private key for allowing Zotpress to read your Zotero library and display
					it for all to see. You can do this manually through the Zotero website; using OAuth in Zotpress is just a quicker, more straightforward way of going about it.
					<strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
				</p>
			<?php } ?>
			
			
		</div>
		
<?php

	} /* OAuth check */

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>