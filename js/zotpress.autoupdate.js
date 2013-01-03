jQuery(document).ready(function() {
    
    
    /*
        
        TRIGGER AUTO-UPDATE
        
    */
    
    var zp_autoupdate_xmlUri = jQuery('.ZOTPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.autoupdate.php?autoupdate=true';
    //alert(zp_autoupdate_xmlUri);
    
    // AJAX
    jQuery.get(zp_autoupdate_xmlUri, {}, function(xml)
    {
        var $result = jQuery('result', xml).attr('success');
        
        //if ($result == "true") {
        //    alert("updated");
        //}
        //else { // Show errors
        //    alert("error - not time to update yet");
        //}
    });
    
    
    
    /*
     
        TRIGGER UDPATE STYLE
        
    */
    
    jQuery(".zp-Zotpress").each(function()
    {
        var $this = jQuery(this);
        
        if (jQuery(".zp-Zotpress-Style", $this).length > 0)
        {
            // Get items to be updated
            var zp_style_items = "";
            
            jQuery(".zp-Entry", $this).each(function() {
                zp_style_items += jQuery(this).attr("rel") +",";
            });
            
            zp_style_items = zp_style_items.substring(0, zp_style_items.length - 1); // get rid of last comma
            
            // Build URI
            var zp_style_xmlUri = jQuery('.ZOTPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.style.php?update=true';
            zp_style_xmlUri += '&api_user_id='+jQuery(".zp-Zotpress-Userid:first", $this).text();
            zp_style_xmlUri += '&style='+jQuery(".zp-Zotpress-Style:first", $this).text();
            zp_style_xmlUri += '&items='+zp_style_items;
            //alert(zp_style_xmlUri);
            
            // AJAX
            jQuery.get(zp_style_xmlUri, {}, function(xml)
            {
                var $result = jQuery('result', xml).attr('success');
                
                if ($result == "true")
                {
                    jQuery('item', xml).each(function()
                    {
                        jQuery(".zp-Entry[rel=" + jQuery(this).attr("key") + "]", $this).html( jQuery(this).text() );
                    });
                }
                else // Show errors
                {
                    //alert("error - can't update citation styles");
                }
            });
        }
    });



});