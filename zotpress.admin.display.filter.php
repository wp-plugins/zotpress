        
        /*
            
            ZOTPRESS CACHING
            
        */
        
        <?php
        
        global $wpdb;
        
        $zp_cache = $wpdb->get_results("SELECT cache_time FROM ".$wpdb->prefix."zotpress_cache ORDER BY cache_time DESC LIMIT 1");
        
        ?>
        
        // Re-caching and clear cache
        jQuery('div#zp-Cache').append('<p>Last cached at <?php echo date("G:i", $zp_cache[0]->cache_time) . " on " . date("j M Y", $zp_cache[0]->cache_time); ?> <a class="zp-ReCache-Button" title="Click here to manually re-cache your Zotero citations" href="admin.php?page=Zotpress&amp;recache=true">Re-cache Page</a> <a class="zp-ClearCache-Button" title="Click here to manually clear your Zotpress cache" href="admin.php?page=Zotpress&amp;clearcache=true">Clear Cache</a></p>');
        
        
        // CLEAR CACHE
        
        jQuery('a.zp-ClearCache-Button').click( function() {
            var doClearCache = confirm('Are you sure you want to clear the entire cache?');
            
            if (doClearCache === true)
            {
                // Switch button with loading sign
                jQuery('.zp-ClearCache-Button').hide().after('<span class="zp-ClearCache-Loading"></span>');
                
                // Set up uri
                var xmlUri = jQuery('span#ZOTPRESS_PLUGIN_URL').text() + 'zotpress.actions.clear.php?cache=true';
                
                // AJAX
                jQuery.ajax({
                    url: xmlUri,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    success: function(xml, textStatus, jqXHR)
                    {
                        jQuery('span.zp-ClearCache-Loading').remove();
                        jQuery('.zp-ClearCache-Button').show();
                    }
                });
            }
            
            return false;
        });
        
        
        
        /*
            
            COPYING ITEM KEYS ON CLICK
            
        */
        
        jQuery('.zp-Entry-ID-Text span').click( function() {
            jQuery(this).parent().find('input').show().select();
            jQuery(this).hide();
        });
        jQuery('.zp-Entry-ID-Text input').blur( function() {
            jQuery(this).hide();
            jQuery(this).parent().find('span').show();
        });
        
        
        
        /*
            
            FILTER CITATIONS
            
        */
        
        
        // FILTER BY ACCOUNT
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByAccount", "change", function()
        {
            var id = jQuery(this).val();
            window.location = "admin.php?page=Zotpress&display=true&loaded=true&account_id="+id+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY COLLECTION
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByCollection", "change", function()
        {
            window.location = "admin.php?page=Zotpress&display=true&loaded=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery(this).val()+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY TAG
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByTag", "change", function()
        {
            window.location = "admin.php?page=Zotpress&display=true&loaded=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&tag_name="+jQuery(this).val()+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY LIMIT
        
        jQuery('input#zp-FilterByLimit').bind({
            blur: function()
            {
                window.location = "admin.php?page=Zotpress&display=true&loaded=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery('select#zp-FilterByCollection option:selected').val()+"&tag_name="+jQuery('select#zp-FilterByTag option:selected').val()+"&limit="+jQuery(this).val();
            },
            keypress: function(e)
            {
                if (e.keyCode == 13) {
                    window.location = "admin.php?page=Zotpress&display=true&loaded=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery('select#zp-FilterByCollection option:selected').val()+"&tag_name="+jQuery('select#zp-FilterByTag option:selected').val()+"&limit="+jQuery(this).val();
                }
            }
        });
        