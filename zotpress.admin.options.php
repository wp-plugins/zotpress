        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.admin.display.tabs.php'); ?>
            
            <h3>Defaults</h3>
            
            <h4>Set Default Style</h4>
            
            <!-- START OF STYLE -->
            <div id="zp-Zotpress-Options-Style" class="zp-Zotpress-Options">
                
                <label for="zp-Zotpress-Options-Style">Choose Style:</label>
                <select id="zp-Zotpress-Options-Style">
                    <?php
                    
                    $zp_styles = "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, mla, nlm, nature, vancouver";
                    $zp_styles = explode(", ", $zp_styles);
                    
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
                </select>
                
                <script type="text/javascript" >
                jQuery(document).ready(function() {
                
                    jQuery("#zp-Zotpress-Options-Style-Button").click(function()
                    {
                        // Plunk it together
                        var data = 'submit=true&style=' + jQuery('select#zp-Zotpress-Options-Style').val();
                        
                        // Prep for data validation
                        jQuery(this).attr('disabled','true');
                        jQuery('.zp-Loading').show();
                        
                        // Set up uri
                        var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>/zotpress.widget.metabox.actions.php?'+data;
                        
                        // AJAX
                        jQuery.get(xmlUri, {}, function(xml)
                        {
                            var $result = jQuery('result', xml).attr('success');
                            
                            jQuery('.zp-Loading').hide();
                            jQuery('input#zp-Zotpress-Options-Style-Button').removeAttr('disabled');
                            
                            if ($result == "true")
                            {
                                jQuery('div.zp-Errors').hide();
                                jQuery('div.zp-Success').show();
                                
                                jQuery.doTimeout(1000,function() {
                                    jQuery('div.zp-Success').hide();
                                });
                            }
                            else // Show errors
                            {
                                jQuery('div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                jQuery('div.zp-Errors').show();
                            }
                        });
                        
                        // Cancel default behaviours
                        return false;
                        
                    });
                    
                });
                </script>
                
                <input type="button" id="zp-Zotpress-Options-Style-Button" class="button-secondary" value="Set Default Style" />
                <div class="zp-Loading">loading</div>
                <div class="zp-Success">Success!</div>
                <div class="zp-Errors">Errors!</div>
                
            </div>
            <!-- END OF STYLE -->
            
        </div>