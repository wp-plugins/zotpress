        <div id="zp-Zotpress" class="wrap">
            
            <?php include('admin.display.tabs.php'); ?>
            
            <h3 class="zp-HeaderGroup">Defaults</h3>
            
            <?php include('admin.options.form.php'); ?>
            
            <hr />
            
            <h3 class="zp-HeaderGroup">Reset</h3>
            
            <!-- START OF RESET -->
            <div class="zp-Column-1">
                
                <h4>Reset Zotpress</h4>
                
                <p class="note">Note: This action will clear all database entries associated with Zotpress, including account information and citations&#8212;it <strong>cannot be undone</strong>. Proceed with caution.</p>
                
                <div id="zp-Zotpress-Options-Reset" class="zp-Zotpress-Options">
                    
                    <script type="text/javascript" >
                    jQuery(document).ready(function() {
                    
                        jQuery("#zp-Zotpress-Options-Reset-Button").click(function()
                        {
                            var confirmDelete = confirm("Are you sure you want to reset Zotpress? This cannot be undone.");
                            
                            if (confirmDelete==true)
                            {
                                // Prep for data validation
                                jQuery(this).attr('disabled','true');
                                jQuery('#zp-Zotpress-Options-Reset .zp-Loading').show();
                                
                                // Set up uri
                                var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?submit=true&reset=<?php echo get_option('ZOTPRESS_PASSCODE'); ?>';
                                
                                // AJAX
                                jQuery.get(xmlUri, {}, function(xml)
                                {
                                    var $result = jQuery('result', xml).attr('success');
                                    
                                    jQuery('#zp-Zotpress-Options-Reset .zp-Loading').hide();
                                    jQuery('input#zp-Zotpress-Options-Reset-Button').removeAttr('disabled');
                                    
                                    if ($result == "true")
                                    {
                                        jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').hide();
                                        jQuery('#zp-Zotpress-Options-Reset div.zp-Success').show();
                                        
                                        jQuery.doTimeout(1000,function() {
                                            jQuery('#zp-Zotpress-Options-Reset div.zp-Success').hide();
                                            window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress";
                                        });
                                    }
                                    else // Show errors
                                    {
                                        jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                        jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').show();
                                    }
                                });
                            } // confirmDelete
                            
                            // Cancel default behaviours
                            return false;
                            
                        });
                        
                    });
                    </script>
                    
                    <input type="button" id="zp-Zotpress-Options-Reset-Button" class="button-secondary" value="Reset Zotpress" />
                    <div class="zp-Loading">loading</div>
                    <div class="zp-Success">Success!</div>
                    <div class="zp-Errors">Errors!</div>
                    
                    <h4 class="clear" />
                    
                </div>
                <!-- END OF ACCOUNT -->
                
            </div>
            
        </div>