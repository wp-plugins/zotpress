jQuery(document).ready(function() {
    
    
    /*
        
        TRIGGER AUTO-UPDATE: Needs to be reworked
        
    */
    
    //var zp_autoupdate_xmlUri = jQuery('.ZOTPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.autoupdate.php?autoupdate=true&step=items&api_user_id=all&key=' + jQuery('.ZOTPRESS_AUTOUPDATE_KEY:first').text();
    ////alert(zp_autoupdate_xmlUri);
    //
    //// AJAX
    //jQuery.get(zp_autoupdate_xmlUri, {}, function(xml)
    //{
    //    var $result = jQuery('result', xml).attr('success');
    //    
    //    if ($result == "true") {
    //        alert("updated");
    //    }
    //    else { // Show errors
    //        alert("error - not time to update yet");
    //    }
    //});
    
    
    
    /*
     
        TRIGGER UDPATE STYLE
        
    */
    
    var zp_current_list_items = new Array();
    var zp_all_list_items = new Array();
    
    function zpCorrectOrderedList( $this )
    {
        var zp_current_list_item = 1;
        
        jQuery(".zp-Entry", $this).each(function()
        {
            var $zpEntry = jQuery(this);
            
            if (jQuery(".csl-left-margin", $zpEntry).length > 0 && jQuery(".csl-left-margin", $zpEntry).text().search(/[0-9]+/g) != -1)
            {
                jQuery(".csl-left-margin", $zpEntry).text(jQuery(".csl-left-margin", $zpEntry).text().replace(/[0-9]+/g, zp_current_list_item));
                zp_current_list_item++;
            }
        });
        
        //zp_current_list_item = 1; // reset
    }
    
    jQuery(".zp-Zotpress").each(function()
    {
        var $this = jQuery(this);
        
        // Update numbered lists
        zpCorrectOrderedList( $this );
        
        var zp_check = "";
        if (jQuery(".zp-Zotpress-Style", $this).length > 0)
            zp_check = jQuery(".zp-Zotpress-Style", $this).text();
        else
            zp_check = jQuery(".csl-bib-body:first", $this).attr("rel");
        
        var zp_update_style = false;
        jQuery(".csl-bib-body", $this).each(function() {
            if (jQuery(this).attr("rel") != zp_check)
                zp_update_style = true;
        });
        
        if (zp_update_style)
        {
            jQuery(".zp-Entry", $this).each(function()
            {
                // Retain URLs, abstract and note reference
                var zpDownloadURL = ""; if (jQuery(this).find("a.zp-DownloadURL").length > 0) { zpDownloadURL = jQuery(this).find("a.zp-DownloadURL").attr("href"); }
                var zpCiteRIS = ""; if (jQuery(this).find("a.zp-CiteRIS").length > 0) { zpCiteRIS = jQuery(this).find("a.zp-CiteRIS").attr("href"); }
                var zpNoteReference = ""; if (jQuery(this).find(".zp-Notes-Reference").length > 0) { zpNoteReference = jQuery(this).find(".zp-Notes-Reference").text(); }
                var zpAbstractReference = ""; if (jQuery(this).find(".zp-Abstract").length > 0) { zpAbstractReference = jQuery(this).find(".zp-Abstract").html(); }
                
                zp_current_list_items[jQuery(this).attr("rel")] = [ zpDownloadURL, zpCiteRIS, zpNoteReference, zpAbstractReference ];
                zp_all_list_items[zp_all_list_items.length] = jQuery(this).attr("rel");
            });
            
            var zp_count = 0;
            
            while (zp_count < zp_all_list_items.length)
            {
                var zp_style_items = "";
                
                //for (var zp_key in zp_all_list_items.slice(zp_count, zp_count+20)) {
                for (var zp_key = 0+zp_count; zp_key < zp_count+20; zp_key++) {
                    zp_style_items += zp_all_list_items[zp_key] +",";
                }
                
                zp_style_items = zp_style_items.substring(0, zp_style_items.length - 1); // get rid of last comma
                
                // Build URI
                var zp_style_xmlUri = jQuery('.ZOTPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.style.php?update=true';
                zp_style_xmlUri += '&api_user_id='+jQuery(".zp-Zotpress-Userid:first", $this).text();
                zp_style_xmlUri += '&style='+jQuery(".zp-Zotpress-Style:first", $this).text();
                zp_style_xmlUri += '&items='+zp_style_items;
                //alert(zp_style_xmlUri); // DEBUGGING
                
                // AJAX
                jQuery.get(zp_style_xmlUri, {}, function(xml)
                {
                    var $result = jQuery('result', xml).attr('success');
                    
                    if ($result == "true")
                    {
                        jQuery('item', xml).each(function()
                        {
                            // Replace with new style
                            jQuery(".zp-Entry[rel=" + jQuery(this).attr("key") + "]", $this).html( jQuery(this).text() );
                            
                            // Re-add URLs, if exist
                            var temp = "";
                            
                            if (zp_current_list_items[jQuery(this).attr("key")][2].length > 0)
                                temp += " <sup class=\"zp-Notes-Reference\">" + zp_current_list_items[jQuery(this).attr("key")][2] + "</sup>";
                            if (zp_current_list_items[jQuery(this).attr("key")][0].length > 0)
                                temp += " <a title=\"Download URL\" href=\"" + zp_current_list_items[jQuery(this).attr("key")][0] + "\">(Download)</a>";
                            if (zp_current_list_items[jQuery(this).attr("key")][1].length > 0)
                                temp += " <a title=\"Cite in RIS Format\" href=\"" + zp_current_list_items[jQuery(this).attr("key")][1] + "\">(Cite)</a>";
                            
                            jQuery(".zp-Entry[rel=" + jQuery(this).attr("key") + "] div:last", $this).append( temp );
                            
                            if (zp_current_list_items[jQuery(this).attr("key")][3].length > 0)
                            {
                                temp = "<p class='zp-Abstract'>" + zp_current_list_items[jQuery(this).attr("key")][3] + "</p>\n";
                                jQuery(".zp-Entry[rel=" + jQuery(this).attr("key") + "]", $this).append( temp );
                            }
                        });
                        
                        // Update numbered lists
                        zpCorrectOrderedList( $this );
                    }
                    //else // Show errors
                    //{
                    //    alert("error - can't update citation styles"); // DEBUGGING
                    //}
                });
                
                zp_count += 20;
            }
        } // zp_update_style
    });



});