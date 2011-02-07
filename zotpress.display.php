        
        /*
            FILTER CITATIONS
        */
        
        function FilterCitations(account, init, collection_id, limit)
        {
            
            // COLLECTION
            collection_id = (collection_id != "" && collection_id != undefined && collection_id != "Select a collection" && collection_id != "-------------------") ? collection_id : "";
            
            // LIMIT
            limit = (limit != "" && limit != undefined && limit != "0" && limit != 0) ? limit : 5;
            
            // LOADING
            jQuery('div#zp-List').empty();
            jQuery('div#zp-List').addClass("zp-Loading");
            
            if (account.id != current_account) {
                jQuery('select#zp-FilterByCollection').empty();
                jQuery('div#zp-FilterByCollection-Section').append('<span id="zp-FilterByCollection-Loading">loading</span>');
            }
            
            // GET CITATIONS
            
            var xmlUriCitations = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                        + 'account_type='+account.account_type
                                        + '&api_user_id='+account.api_user_id
                                        + '&public_key='+account.public_key
                                        + '&collection_id='+collection_id
                                        + '&limit='+limit;
            
            // Grab Citations
            jQuery.get(xmlUriCitations, {}, function(xml)
            {
                if (browser_is_IE)
                {
                    xml = createXmlDOMObject (xml);
                }
                
                jQuery(xml).find("entry").each(function()
                {
                    var zpcontent = (browser_is_IE) ? jQuery(jQuery(this).context.xml).find("content").html() : jQuery(this).find("content").html();
                    
                    var code = "<div class='zp-Entry'>\n"
                            + "<div id='zp-Citation-"+jQuery(this).find("zapi\\:key").text()+"' class='zp-Entry-Image' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n"
                            + "<a href='admin.php?page=Zotpress&amp;image=true&amp;account_type="+account.account_type+"&amp;api_user_id="+account.api_user_id+"&amp;citation_id="+jQuery(this).find("zapi\\:key").text()+"&amp;citation="+escape(zpcontent)+"'>"
                            + "<span>Upload Image</span>"
                            + "</a>\n"
                            + "<div class='bg'></div>"
                            + "</div>\n"
                            + zpcontent
                            + "<span class='zp-Entry-ID'><span>Item Key (Citation ID):</span> "+jQuery(this).find("zapi\\:key").text()+"</span>\n"
                            +"</div>\n\n";
                    
                    jQuery('div#zp-List').append(code);
                });
                
                jQuery('div#zp-List').removeClass("zp-Loading");
                
            }, "XML");
            
            
            // GET IMAGES
            
            var xmlUriCitationImages = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                        + 'account_type='+account.account_type
                                        +'&api_user_id='+account.api_user_id
                                        +'&public_key='+account.public_key
                                        +'&displayImages=true';
                                        //alert(xmlUriCitationImages);
            
            // Grab Images
            jQuery.get(xmlUriCitationImages, {}, function(xml)
            {
                jQuery(xml).find("zpimage").each(function()
                {
                    var zpimage = jQuery(this);
                    
                    jQuery('div#zp-Citation-'+zpimage.attr('citation_id')).livequery(function() {
                        jQuery(this).append("<img src='"+zpimage.attr('image_url')+"' alt='image' />\n");
                    });
                    jQuery('div.zp-Entry-Image[rel='+jQuery(this).attr('citation_id')+'] a').livequery(function() {
                        jQuery(this).attr("href", jQuery('div.zp-Entry-Image[rel='+zpimage.attr('citation_id')+'] a').attr("href")+"&update=true&image_url="+zpimage.attr('image_url'));
                    });
                });
            });
            
            
            // GET COLLECTIONS
            
            if (account.id != current_account)
            {
                var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                                + 'account_type='+account.account_type
                                                +'&api_user_id='+account.api_user_id
                                                +'&public_key='+account.public_key
                                                +'&data_type=collections';
                
                // Grab Collections
                jQuery.get(xmlUriCollections, {}, function(xml)
                {
                    if (browser_is_IE)
                    {
                        xml = createXmlDOMObject (xml);
                    }
                    
                    jQuery('select#zp-FilterByCollection').empty();
                    
                    var total_collection_citations = 0;
                    
                    jQuery(xml).find("entry").each(function() {
                        jQuery('select#zp-FilterByCollection').append("<option class='collection' value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+" ["+jQuery(this).find("zapi\\:key").text()+"]</option>\n");
                        total_collection_citations++;
                    });
                    
                    jQuery('span#zp-FilterByCollection-Loading').remove();
                    
                    if (total_collection_citations > 0) {
                        jQuery('select#zp-FilterByCollection').prepend("<option>-------------------</option>\n");
                        jQuery('select#zp-FilterByCollection').prepend("<option selected='selected'>Select a collection</option>\n");
                    }
                    
                    if (init == true)
                        current_collection = jQuery('select#zp-FilterByCollection option:selected').val();
                    
                }, "XML");
                
                if (init == false)
                    current_account = account.id;
            }
        };
        
        FilterCitations(accounts[0], true);
        
        var current_account = accounts[0].id;
        
        
        
        // FILTER BY ACCOUNT
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByAccount", "change", function()
        {
            var id = jQuery(this).val();
            FilterCitations(accounts[id], false, "", jQuery('input#zp-FilterByLimit').val());
        });
        
        
        // FILTER BY COLLECTION
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByCollection", "change", function()
        {
            var id = jQuery('select#zp-FilterByAccount option:selected').val();
            
            FilterCitations(accounts[id], false, jQuery(this).val(), jQuery('input#zp-FilterByLimit').val());
        });
        
        
        // FILTER BY LIMIT
        
        jQuery('input#zp-FilterByLimit').bind({
            blur: function()
            {
                var id = jQuery('select#zp-FilterByAccount option:selected').val();
                FilterCitations(accounts[id], false, jQuery('select#zp-FilterByCollection option:selected').val(), jQuery(this).val());
            },
            keypress: function(e)
            {
                if (e.keyCode == 13) {
                    var id = jQuery('select#zp-FilterByAccount option:selected').val();
                    FilterCitations(accounts[id], false, jQuery('select#zp-FilterByCollection option:selected').val(), jQuery(this).val());
                }
            }
        });
        