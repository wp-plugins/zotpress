<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{
	
?>
            <!-- START OF ACCOUNT -->
            <div class="zp-Column-1">
                <div class="zp-Column-Inner">
                    
                    <h4>Set Default Account</h4>
                    
                    <p class="note">Note: Only applicable if you have multiple synced Zotero accounts.</p>
                    
                    <div id="zp-Zotpress-Options-Account" class="zp-Zotpress-Options">
                        
                        <label for="zp-Zotpress-Options-Account">Choose Account:</label>
                        <select id="zp-Zotpress-Options-Account">
                            <?php
                            
                            global $wpdb;
                            $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
                            $zp_accounts_total = $wpdb->num_rows;
                            
                            // See if default exists
                            $zp_default_account = "";
                            if (get_option("Zotpress_DefaultAccount"))
                                $zp_default_account = get_option("Zotpress_DefaultAccount");
                            
                            foreach ($zp_accounts as $zp_account)
                                if ($zp_account->api_user_id == $zp_default_account)
                                    echo "<option id=\"".$zp_account->api_user_id."\" value=\"".$zp_account->api_user_id."\" selected='selected'>".$zp_account->api_user_id." (".$zp_account->nickname.") [".substr($zp_account->account_type, 0, strlen($zp_account->account_type)-1)."]</option>\n";
                                else
                                    echo "<option id=\"".$zp_account->api_user_id."\" value=\"".$zp_account->api_user_id."\">".$zp_account->api_user_id." (".$zp_account->nickname.") [".substr($zp_account->account_type, 0, strlen($zp_account->account_type)-1)."]</option>\n";
                            
                            ?>
                        </select>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                        
                            jQuery("#zp-Zotpress-Options-Account-Button").click(function()
                            {
                                // Plunk it together
                                var data = 'submit=true&account=' + jQuery('select#zp-Zotpress-Options-Account').val();
                                
                                // Prep for data validation
                                jQuery(this).attr('disabled','true');
                                jQuery('#zp-Zotpress-Options-Account .zp-Loading').show();
                                
                                // Set up uri
                                var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                
                                // AJAX
                                jQuery.get(xmlUri, {}, function(xml)
                                {
                                    var $result = jQuery('result', xml).attr('success');
                                    
                                    jQuery('#zp-Zotpress-Options-Account .zp-Loading').hide();
                                    jQuery('input#zp-Zotpress-Options-Account-Button').removeAttr('disabled');
                                    
                                    if ($result == "true")
                                    {
                                        jQuery('#zp-Zotpress-Options-Account div.zp-Errors').hide();
                                        jQuery('#zp-Zotpress-Options-Account div.zp-Success').show();
                                        
                                        jQuery.doTimeout(1000,function() {
                                            jQuery('#zp-Zotpress-Options-Account div.zp-Success').hide();
                                        });
                                    }
                                    else // Show errors
                                    {
                                        jQuery('#zp-Zotpress-Options-Account div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                        jQuery('#zp-Zotpress-Options-Account div.zp-Errors').show();
                                    }
                                });
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="zp-Zotpress-Options-Account-Button" class="button-secondary" value="Set Default Account" />
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Success">Success!</div>
                        <div class="zp-Errors">Errors!</div>
                        
                        <h4 class="clear" />
                        
                    </div>
                    <!-- END OF ACCOUNT -->
                    
                </div>
            </div>
            
            <div class="zp-Column-2">
                <div class="zp-Column-Inner">
                    
                    <!-- START OF STYLE -->
                    <h4>Set Default Citation Style</h4>
                    
                    <p class="note">Note: Styles must be listed <a title="Zotero Styles" href="http://www.zotero.org/styles">here</a>. Use the name found in the style's URL, e.g. modern-language-association.</p>
                    
                    <div id="zp-Zotpress-Options-Style-Container" class="zp-Zotpress-Options">
                        
                        <label for="zp-Zotpress-Options-Style">Choose Style:</label>
                        <select id="zp-Zotpress-Options-Style">
                            <?php
                            
                            if (!get_option("Zotpress_StyleList"))
                                add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");
                            
                            $zp_styles = explode(", ", get_option("Zotpress_StyleList"));
                            sort($zp_styles);
                            
                            // See if default exists
                            $zp_default_style = "apa";
                            if (get_option("Zotpress_DefaultStyle"))
                                $zp_default_style = get_option("Zotpress_DefaultStyle");
                            
                            foreach($zp_styles as $zp_style)
                                if ($zp_style == $zp_default_style)
                                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" selected='selected'>".$zp_style."</option>\n";
                                else
                                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
                            
                            ?>
                            <option id="new" value="new-style">Add another style ...</option>
                        </select>
                        
                        <div id="zp-Zotpress-Options-Style-New-Container">
                            <label for="zp-Zotpress-Options-Style-New">Add Style:</label>
                            <input id="zp-Zotpress-Options-Style-New" type="text" />
                        </div>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                            
                            // Show/hide add style input
                            jQuery("#zp-Zotpress-Options-Style").change(function()
                            {
                                if (this.value === 'new-style')
                                {
                                    jQuery("#zp-Zotpress-Options-Style-New-Container").show();
                                }
                                else
                                {
                                    jQuery("#zp-Zotpress-Options-Style-New-Container").hide();
                                    jQuery("#zp-Zotpress-Options-Style-New").val("");
                                }
                            });
                            
                            jQuery("#zp-Zotpress-Options-Style-Button").click(function()
                            {
                                var styleOption = jQuery('select#zp-Zotpress-Options-Style').val();
                                var updateStyleList = false;
                                
                                // Determine if using existing or adding new; if adding new, also update Zotpress_StyleList option
                                if ( styleOption == "new-style" )
                                {
                                    styleOption = jQuery("#zp-Zotpress-Options-Style-New").val();
                                    updateStyleList = true;
                                }
                                
                                if ( styleOption != "" )
                                {
                                    // Plunk it together
                                    var data = 'submit=true&style=' + styleOption;
                                    
                                    // Prep for data validation
                                    jQuery(this).attr('disabled','true');
                                    jQuery('#zp-Zotpress-Options-Style-Container .zp-Loading').show();
                                    
                                    // Set up uri
                                    var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                    
                                    // AJAX
                                    jQuery.get(xmlUri, {}, function(xml)
                                    {
                                        var $result = jQuery('result', xml).attr('success');
                                        
                                        jQuery('#zp-Zotpress-Options-Style-Container .zp-Loading').hide();
                                        jQuery('input#zp-Zotpress-Options-Style-Button').removeAttr('disabled');
                                        
                                        if ($result == "true")
                                        {
                                            jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').hide();
                                            jQuery('#zp-Zotpress-Options-Style-Container div.zp-Success').show();
                                            
                                            jQuery.doTimeout(1000,function()
                                            {
                                                jQuery('#zp-Zotpress-Options-Style-Container div.zp-Success').hide();
                                                
                                                if (updateStyleList === true)
                                                {
                                                    jQuery('#zp-Zotpress-Options-Style').prepend(jQuery("<option/>", {
                                                        value: styleOption,
                                                        text: styleOption,
                                                        selected: "selected"
                                                    }));
                                                    
                                                    jQuery("#zp-Zotpress-Options-Style-New-Container").hide();
                                                    jQuery("#zp-Zotpress-Options-Style-New").val("");
                                                }
                                            });
                                        }
                                        else // Show errors
                                        {
                                            jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').html(jQuery('errors', xml).text()+"\n");
                                            jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').show();
                                        }
                                    });
                                }
                                else // Show errors
                                {
                                    jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').html("No style was entered.\n");
                                    jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').show();
                                }
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="zp-Zotpress-Options-Style-Button" class="button-secondary" value="Set Default Style" />
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Success">Success!</div>
                        <div class="zp-Errors">Errors!</div>
                        
                        <h4 class="clear" />
                        
                    </div>
                    <!-- END OF STYLE -->
                    
                </div>
            </div>
            
            <?php /* autoupdate temporarily disabled */ if ( 1==2) { ?>
            <hr />
            
            <div class="zp-Column-1">
                <div class="zp-Column-Inner">
                    
                    <h4>Set Auto-Update</h4>
                    
                    <p class="note">Have Zotpress automatically sync your Zotero accounts.</p>
                    
                    <div id="zp-Zotpress-Options-AutoUpdate" class="zp-Zotpress-Options">
                        
                        <label for="zp-Zotpress-Options-AutoUpdate">Choose Interval:</label>
                        <select id="zp-Zotpress-Options-AutoUpdate">
                            <?php
                            
                            // See if default exists
                            $zp_default_autoupdate = "weekly";
                            if (get_option("Zotpress_DefaultAutoUpdate"))
                                $zp_default_autoupdate = get_option("Zotpress_DefaultAutoUpdate");
                            
                            ?>
                            <option id="daily" <?php if ($zp_default_autoupdate == "daily") { ?>selected="selected"<?php } ?>>Daily</option>
                            <option id="weekly" <?php if ($zp_default_autoupdate == "weekly") { ?>selected="selected"<?php } ?>>Weekly</option>
                        </select>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                        
                            jQuery("#zp-Zotpress-Options-AutoUpdate-Button").click(function()
                            {
                                // Plunk it together
                                var data = 'submit=true&autoupdate=' + jQuery('select#zp-Zotpress-Options-AutoUpdate').val();
                                
                                // Prep for data validation
                                jQuery(this).attr('disabled','true');
                                jQuery('#zp-Zotpress-Options-AutoUpdate .zp-Loading').show();
                                
                                // Set up uri
                                var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                
                                // AJAX
                                jQuery.get(xmlUri, {}, function(xml)
                                {
                                    var $result = jQuery('result', xml).attr('success');
                                    
                                    jQuery('#zp-Zotpress-Options-AutoUpdate .zp-Loading').hide();
                                    jQuery('input#zp-Zotpress-Options-AutoUpdate-Button').removeAttr('disabled');
                                    
                                    if ($result == "true")
                                    {
                                        jQuery('#zp-Zotpress-Options-AutoUpdate div.zp-Errors').hide();
                                        jQuery('#zp-Zotpress-Options-AutoUpdate div.zp-Success').show();
                                        
                                        jQuery.doTimeout(1000,function() {
                                            jQuery('#zp-Zotpress-Options-AutoUpdate div.zp-Success').hide();
                                        });
                                    }
                                    else // Show errors
                                    {
                                        jQuery('#zp-Zotpress-Options-AutoUpdate div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                        jQuery('#zp-Zotpress-Options-AutoUpdate div.zp-Errors').show();
                                    }
                                });
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="zp-Zotpress-Options-AutoUpdate-Button" class="button-secondary" value="Set Auto-Update Interval" />
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Success">Success!</div>
                        <div class="zp-Errors">Errors!</div>
                        
                    </div>
                    <!-- END OF ACCOUNT -->
                    
                </div>
            </div><!-- .zp-Column-1 --><?php } ?>
			
			
<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>