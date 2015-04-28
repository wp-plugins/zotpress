<?php

    global $wpdb;
    
    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    $zp_accounts_total = $wpdb->num_rows;
    
	
	// Display Browse page if there's at least one Zotero account synced
	
    if ( $zp_accounts_total > 0 )
    {
		// FILTER PARAMETERS
		
		// API User ID
		
		global $api_user_id;
		$account_name = false;
		
		if ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) )
		{
			$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);
			$api_user_id = $zp_account->api_user_id;
			$account_name = $zp_account->nickname;
		}
		else
		{
			if ( get_option("Zotpress_DefaultAccount") )
			{
				$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".get_option("Zotpress_DefaultAccount")."'", OBJECT);
				
				if ( count($zp_account) > 0 )
				{
					$api_user_id = $zp_account->api_user_id;
					$account_name = $zp_account->nickname;
				}
				else
				{
					$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");
					
					if (count($zp_account) > 0)
					{
						$account_name = $zp_account->nickname;
						$account_type = $zp_account->account_type;
						$api_user_id = $zp_account->api_user_id;
						$public_key = $zp_account->public_key;
						$nickname = $zp_account->nickname;
					}
					else
					{
						$api_user_id = false;
					}
				}
			}
			else
			{
				$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");
				
				if (count($zp_account) > 0)
				{
					$account_name = $zp_account->nickname;
					$account_type = $zp_account->account_type;
					$api_user_id = $zp_account->api_user_id;
					$public_key = $zp_account->public_key;
					$nickname = $zp_account->nickname;
				}
				else
				{
					$api_user_id = false;
				}
			}
		}
		
		
		// ACCOUNT DEFAULTS
		
		if (count($zp_account) == 1)
		{
			$account_type = $zp_account->account_type;
			$api_user_id = $zp_account->api_user_id;
			$public_key = $zp_account->public_key;
			$nickname = $zp_account->nickname;
		}
		
		
		// Use Browse class
		
		$zpLib = new zotpressBrowse;
		$zpLib->setAccount($api_user_id);
		$zpLib->setType("dropdown");
	?>
    
    <div id="zp-Zotpress" class="wrap">
        
        <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>
        
        <div id="zp-Browse-Wrapper">
            
            <h3><?php if ( count($zp_accounts) == 1 ): echo "Your Library"; else: ?>
            
				<div id="zp-Browse-Accounts">
					<label for="zp-FilterByAccount">Account:</label>
					<select id="zp-FilterByAccount">
						<?php
						
						// DISPLAY ACCOUNTS
						
						foreach ($zp_accounts as $zp_account)
						{
							// DETERMINE CURRENTLY ACTIVE ACCOUNT
							if ($api_user_id && $api_user_id == $zp_account->api_user_id)
							{
								$account_type = $zp_account->account_type;
								$public_key = $zp_account->public_key;
								$nickname = $zp_account->nickname;
							}
							
							// DISPLAY ACCOUNTS IN DROPDOWN
							echo "<option ";
							if ($api_user_id && $api_user_id == $zp_account->api_user_id) echo "selected='selected' ";
							echo "rel='".$zp_account->api_user_id."' value='".$zp_account->api_user_id."'>";
							if ($zp_account->nickname) echo $zp_account->nickname; else echo $zp_account->api_user_id;
							echo "'s Library</option>\n";
						}
						
						?>
					</select>
				</div>
			
			<?php endif; ?></h3>
			
			<div id="zp-Browse-Account-Options">
				
				<?php $is_default = false; if ( get_option("Zotpress_DefaultAccount") && get_option("Zotpress_DefaultAccount") == $api_user_id ) { $is_default = true; } ?>
				<a href="admin.php?page=Zotpress&selective=true&api_user_id=<?php echo $api_user_id; ?>" class="zp-Browse-Account-Import button button-secondary">Selectively Import</a>
				<a href="javascript:void(0);" rel="<?php echo $api_user_id; ?>" class="zp-Browse-Account-Default button button-secondary<?php if ( $is_default ) { echo " selected disabled"; } ?>"><?php if ( $is_default ) { echo "Default"; } else { echo "Set as Default"; } ?></a>
				
			</div>
            
            <span id="ZOTPRESS_PLUGIN_URL"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
            
            <?php $zpLib->getLib(); ?>
			
        </div><!-- #zp-Browse-Wrapper -->
        
    </div>
    
    
<?php } else { ?>
    
    <div id="zp-Zotpress">
        
        <div id="zp-Setup">
            
            <div id="zp-Zotpress-Navigation">
            
                <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
                
                <div class="nav">
                    <div id="step-1" class="nav-item nav-tab-active">System Check</div>
                </div>
            
            </div><!-- #zp-Zotpress-Navigation -->
            
            <div id="zp-Setup-Step">
                
                <h3>Welcome to Zotpress</h3>
                
                <div id="zp-Setup-Check">
                    
                    <p>
                        Before we get started, let's make sure your system can support Zotpress:
                    </p>
                    
                    <?php
                    
                    $zp_check_curl = intval( function_exists('curl_version') );
                    $zp_check_streams = intval( function_exists('stream_get_contents') );
                    $zp_check_fsock = intval( function_exists('fsockopen') );
                    
                    if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) <= 1 ) { ?>
                    
                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> Zotpress requires at least one of the following: <strong>cURL, fopen with Streams (PHP 5), or fsockopen</strong>. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
                    </div>
                    
                    <?php } else { ?>
                    
                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em>Hurrah!</em></strong> Your system meets the requirements necessary for Zotpress to communicate with Zotero from WordPress.</p>
                    </div>
                    
                    <p>Sometimes systems aren't configured to allow communication with external websites. Let's check by accessing WordPress.org:
                    
                    <?php
                    
                    $response = wp_remote_get( "https://wordpress.org", array( 'headers' => array("Zotero-API-Version: 2") ) );
                    
                    if ( $response["response"]["code"] == 200 ) { ?>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#zp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Zotpress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em>Great!</em></strong> We successfully connected to WordPress.org.</p>
                    </div>
                    
                    <p>Everything appears to check out. Let's continue setting up Zotpress by adding your Zotero account. Click "Next."
                    
                    <?php } else { ?>
                    
                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> Zotpress was not able to connect to WordPress.org.</p>
                    </div>
                    
                    <p>Unfortunately, Zotpress ran into an error. Here's what WordPress has to say: <?php if ( is_wp_error($response) ) { echo $response->get_error_message(); } else { echo "Sorry, but there's no details on the error." ; } ?></p>
                    
                    <p>First, try reloading. If the error recurs, your system may not be set up to run Zotpress. Please contact your system administrator or website host and ask about allowing PHP scripts to access content like RSS feeds from external websites through cURL, fopen with Streams (PHP 5), or fsockopen.</p>
                    
                    <p>You can still try to use Zotpress, but it may not work and/or you may encounter further errors.</p>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#zp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Zotpress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <?php }
                    } ?>
                    
                </div>
                
                <div class="proceed">
                    <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="Next" tabindex="5" disabled="disabled" />
                </div>
                
            </div>
            
        </div>
        
    </div>
    
<?php } ?>