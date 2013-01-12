<?php

// Determine if server supports OAuth
if (in_array ('oauth', get_loaded_extensions())) { $oauth_is_not_installed = false; } else { $oauth_is_not_installed = true; }

if (isset( $_GET['oauth'] )) { include("admin.accounts.oauth.php"); } else {

?>

    <div id="zp-Zotpress" class="wrap">
        
        <?php include('admin.display.tabs.php'); ?>
        
        
        <!-- ZOTPRESS MANAGE ACCOUNTS -->
        
        <div id="zp-ManageAccounts">
            
            <h3>Synced Zotero Accounts</h3>
            <?php if (!isset( $_GET['no_accounts'] ) || (isset( $_GET['no_accounts'] ) && $_GET['no_accounts'] != "true")) { ?><a title="Sync your Zotero account" class="zp-AddAccountButton" href="<?php echo admin_url("admin.php?page=Zotpress&setup=true"); ?>"><span>Add account</span></a><?php } ?>
            
            <div id="zp-Accounts">
                <div id="zp-Accounts-Inner" class="widefat">
                
                    <div id="zp-AccountsHeader">
                        <span class="account_type first">Type</span>
                        <span class="api_user_id">User ID</span>
                        <span class="public_key">Private Key</span>
                        <span class="nickname">Nickname</span>
                        <span class="delete last">Actions</span>
                    </div>
                    
                    <div id="zp-AccountsList">
                        <?php
                            
                            global $wpdb;
                            $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress");
                            
                            foreach ($accounts as $num => $account)
                            {
                                // Set up sync sessions
                                $zp_session_key = "";
                                if (!isset($_SESSION['zp_session'][$account->api_user_id]['key']))
                                {
                                    $zp_session_key = substr(number_format(time() * rand(),0,'',''),0,10); /* Thanks to http://elementdesignllc.com/2011/06/generate-random-10-digit-number-in-php/ */
                                    $_SESSION['zp_session'][$account->api_user_id]['key'] = $zp_session_key;
                                }
                                else // already set
                                {
                                    $zp_session_key = $_SESSION['zp_session'][$account->api_user_id]['key'];
                                }
                                
                                $zebra = " stripe";
                                if ($num % 2 == 0)
                                    $zebra = "";
                                    
                                $code = "<div id='zp-Account-" . $account->api_user_id . "' class='zp-Account".$zebra."' rel='" . $account->api_user_id . "'>\n";
                                
                                // ACCOUNT TYPE
                                $code .= "                          <span class='account_type first'>" . $account->account_type . "</span>\n";
                                
                                // API USER ID
                                $code .= "                          <span class='api_user_id'>" . $account->api_user_id . "</span>\n";
                                
                                // PUBLIC KEY
                                $code .= "                          <span class='public_key'>";
                                if ($account->public_key) {
                                    $code .= $account->public_key;
                                }
                                else {
                                    if ($account->account_type == "users")
                                        $code .= 'No private key entered. <a class="zp-OAuth-Button" href="'.get_bloginfo( 'url' ).'/wp-content/plugins/zotpress/lib/admin/admin.accounts.oauth.php?oauth_user='.$account->api_user_id.'&amp;return_uri='.get_bloginfo('url').'">Start OAuth?</a>';
                                    else
                                        $code .= '<del>N/A</del>';
                                }
                                $code .= "&nbsp;</span>\n";
                                
                                // NICKNAME
                                $code .= "                          <span class='nickname'>";
                                if ($account->nickname)
                                    $code .= $account->nickname;
                                $code .= "&nbsp;</span>\n";
                                
                                // ACTIONS
                                $code .= "                          <span class='delete last'>\n";
                                $code .= "                              <a title='Sync' class='sync' rel='".$account->api_user_id."' href='javascript:void(0);'>Sync<span style=\"display:none;\">".$zp_session_key."</a>\n";
                                $code .= "                              <a title='(Re)Import' class='import' href='admin.php?page=Zotpress&setup=true&setupstep=three&api_user_id=" . $account->api_user_id . "'>Import</a>\n";
                                $code .= "                              <a title='Remove this account' class='delete' href='#" . $account->id . "'>Remove</a>\n";
                                $code .= "                              <span class='zp-Sync-Messages'>&nbsp;</span>\n";
                                $code .= "                          </span>\n";
                                
                                $code .= "                         </div>\n\n";
                                
                                echo $code;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <span id="ZOTPRESS_PLUGIN_URL" style="display: none;"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
        
        <?php if (!$oauth_is_not_installed){ ?>
            <h3>What is OAuth?</h3>
            
            <p>
                OAuth helps you create the necessary private key for allowing Zotpress to read your Zotero library and display
                it for all to see. You can do this manually through the Zotero website; using OAuth in Zotpress is just a quicker, more straightforward way of going about it.
                <strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
            </p>
        <?php } ?>
        
        
    </div>
    
<?php } /* OAuth check */ ?>