<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        
        // GET ACCOUNTS
        var accounts = new Array();
        
        <?php
        
        foreach ($zp_accounts as $zp_account)
            echo "accounts[accounts.length] = {'account_type': '".$zp_account->account_type."', 'api_user_id': '".$zp_account->api_user_id."', 'public_key': '".$zp_account->public_key."', 'nickname': '".addslashes($zp_account->nickname)."'};\n";
            
        ?>
        
        // DISPLAY ACCOUNTS
        
        jQuery.each(accounts, function(id, account) {
            if (account.nickname != "")
                jQuery('select#zp-FilterByAccount').prepend("<option value='"+id+"'>"+account.nickname+"</option>\n");
            else
                jQuery('select#zp-FilterByAccount').prepend("<option value='"+id+"'>"+account.api_user_id+"</option>\n");
        });
        
        jQuery('span#zp-FilterByAccount-Loading').remove();

        
        /*
            DISPLAY CITATIONS
        */
        
        function DisplayCitations(account)
        {
            
            // GET CITATIONS
            
            var xmlUriCitations = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                        + 'account_type='+account.account_type
                                        +'&api_user_id='+account.api_user_id
                                        +'&public_key='+account.public_key
                                        +'&limit='+jQuery('input#zp-FilterByLimit').val();
            
            jQuery('div#zp-List').empty();
            jQuery('div#zp-List').addClass("zp-Loading");
            
            // Grab Citations
            jQuery.get(xmlUriCitations, {}, function(xml)
            {
                // CITATIONS
                
                jQuery(xml).find("entry").each(function()
                {
                    var code = "<div class='zp-Entry'>\n"
                            + "<div class='zp-Entry-Image' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n"
                            + "<a href='admin.php?page=Zotpress&amp;image=true&amp;account_type="+account.account_type+"&amp;api_user_id="+account.api_user_id+"&amp;citation_id="+jQuery(this).find("zapi\\:key").text()+"&amp;citation="+escape(jQuery(this).find("content").html())+"'>"
                            + "<span>Upload Image</span>"
                            + "</a>\n"
                            + "<div class='bg'></div>"
                            + "</div>\n"
                            + jQuery(this).find("content").html()
                            + "<span class='zp-Entry-ID'><span>Item Key (Citation ID):</span> "+jQuery(this).find("zapi\\:key").text()+"</span>\n"
                            +"</div>\n\n";
                    
                    jQuery('div#zp-List').append(code);
                });
                
                
                // IMAGES
                jQuery(xml).find("zpimage").each(function()
                {
                    jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+']').append("<img src='"+jQuery(this).attr('image_url')+"' alt='image' />\n");
                    jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+'] a').attr("href", jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+'] a').attr("href")+"&update=true&image_url="+jQuery(this).attr('image_url'));
                });
                
                jQuery('div#zp-List').removeClass("zp-Loading");
            });
            
            
            // GET COLLECTIONS
            
            var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                            + 'account_type='+account.account_type
                                            +'&api_user_id='+account.api_user_id
                                            +'&public_key='+account.public_key
                                            +'&data_type=collections';
            
            // Grab Collections
            jQuery.get(xmlUriCollections, {}, function(xml)
            {
                jQuery(xml).find("entry").each(function() {
                    jQuery('select#zp-FilterByCollection').append("<option class='collection' value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+" ["+jQuery(this).find("zapi\\:key").text()+"]</option>\n");
                });
                
                jQuery('span#zp-FilterByCollection-Loading').remove();
                
                jQuery('select#zp-FilterByCollection').prepend("<option>-------------------</option>\n");
                jQuery('select#zp-FilterByCollection').prepend("<option selected='selected'>Select a collection</option>\n");
            });
        };
        
        DisplayCitations(accounts[0]);
        
        
        /*
            FILTER CITATIONS
        */
        
        function FilterCitations(account, collection_id, limit)
        {
            // COLLECTION
            collection_id = (collection_id != "" && collection_id != "Select a collection" && collection_id != "-------------------") ? collection_id : "";
            
            // LIMIT
            limit = (limit != "" && limit != "0" && limit != 0) ? limit : 5;
            
            var xmlUriCitationsByFilter = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?account_type='+account.account_type+'&api_user_id='+account.api_user_id+'&public_key='+account.public_key+'&collection_id='+collection_id+'&limit='+limit;
            
            jQuery('div#zp-List').empty();
            jQuery('div#zp-List').addClass("zp-Loading");
            
            // Grab citations
            jQuery.get(xmlUriCitationsByFilter, {}, function(xml)
            {
                // CITATIONS
                jQuery(xml).find("entry").each(function()
                {
                    var code = "<div class='zp-Entry'>\n"
                            + "<div class='zp-Entry-Image' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n"
                            + "<a href='admin.php?page=Zotpress&amp;image=true&amp;account_type="+account.account_type+"&amp;api_user_id="+account.api_user_id+"&amp;citation_id="+jQuery(this).find("zapi\\:key").text()+"&amp;citation="+escape(jQuery(this).find("content").html())+"'>"
                            + "<span>Upload Image</span>"
                            + "</a>\n"
                            + "<div class='bg'></div>"
                            + "</div>\n"
                            + jQuery(this).find("content").html()
                            + "<span class='zp-Entry-ID'><span>Item Key (Citation ID):</span> "+jQuery(this).find("zapi\\:key").text()+"</span>\n"
                            +"</div>\n\n";
                    jQuery('div#zp-List').append(code);
                });
                
                // IMAGES
                jQuery(xml).find("zpimage").each(function()
                {
                    jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+']').append("<img src='"+jQuery(this).attr('image_url')+"' alt='image' />\n");
                    jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+'] a').attr("href", jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+'] a').attr("href")+"&update=true&image_url="+jQuery(this).attr('image_url'));
                });

                
                jQuery('div#zp-List').removeClass("zp-Loading");
            });
        }
        
        // FILTER BY ACCOUNT
        
        jQuery('select#zp-FilterByAccount').delegate("option", "click", function()
        {
            var id = jQuery(this).val();
            FilterCitations(accounts[id], "", jQuery('input#zp-FilterByLimit').val());
        });
        
        // FILTER BY COLLECTION
        
        jQuery('select#zp-FilterByCollection').delegate("option.collection", "click", function()
        {
            var id = jQuery('select#zp-FilterByAccount option:selected').val();
            FilterCitations(accounts[id], jQuery(this).val(), jQuery('input#zp-FilterByLimit').val());
        });
        
        // FILTER BY LIMIT
        
        jQuery('input#zp-FilterByLimit').bind({
            blur: function()
            {
                var id = jQuery('select#zp-FilterByAccount option:selected').val();
                FilterCitations(accounts[id], jQuery('select#zp-FilterByCollection option:selected').val(), jQuery(this).val());
            },
            keypress: function(e)
            {
                if (e.keyCode == 13) {
                    var id = jQuery('select#zp-FilterByAccount option:selected').val();
                    FilterCitations(accounts[id], jQuery('select#zp-FilterByCollection option:selected').val(), jQuery(this).val());
                }
            }
        });
        
        
    });
</script>