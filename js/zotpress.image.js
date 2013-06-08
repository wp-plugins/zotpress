jQuery(document).ready(function() {
    


    /*
        
        UPLOAD IMAGE FORM
        
    */

    jQuery('#zp-Submit').click(function ()
    {
        var data = 'image=true'
                    + '&api_user_id=' + jQuery('input[name=api_user_id]').val()
                    + '&citation_id=' + jQuery('input[name=citation_id]').val()
                    + '&upload_image=' + jQuery('input[name=upload_image]').val();
        
        // Disable all fields
        jQuery('input[name!=update], textarea, select').attr('disabled','true');
        
        // Show the loading sign
        jQuery('.zp-Errors').hide();
        jQuery('.zp-Success').hide();
        jQuery('.zp-Loading').show();
        
        // Set up uri
        var xmlUri = jQuery('input[name=ZOTPRESS_PLUGIN_URL]').val() + 'lib/actions/actions.php?'+data;
        
        // AJAX
        jQuery.get(xmlUri, {}, function(xml)
        {
            var $result = jQuery('result', xml).attr('success');
            
            if ($result == "true")
            {
                jQuery('div.zp-Errors').hide();
                jQuery('.zp-Loading').hide();
                jQuery('div.zp-Success').html("<p><strong>Success!</strong> The image has been linked to your citation.</p>\n");
                
                jQuery('div.zp-Success').show();
                
                jQuery.doTimeout(1000,function() {
                    location.href = document.referrer;
                });
            }
            else // Show errors
            {
                jQuery('input, textarea, select').removeAttr('disabled');
                jQuery('div.zp-Errors').html("<p><strong>Oops!</strong> "+jQuery('errors', xml).text()+"</p>\n");
                jQuery('div.zp-Errors').show();
                jQuery('.zp-Loading').hide();
            }
        });
        
        // Cancel default behaviours
        return false;
    });
    
    

    /*
        
        UPLOAD IMAGE FORM CANCEL BUTTON
        
    */

    jQuery('input#zp-Cancel').click(function() {
        
        window.history.back();
        
        return false;
    });
    


    /*
     
        UPLOAD IMAGE SELECTION DIALOGUE
        
    */
    
    
    jQuery('#upload_image_button').click(function() {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });
    
    window.send_to_editor = function(html)
    {
        if ( jQuery(html).is("a") ) {
            var imgurl = jQuery(html).attr('href');
        } else if ( jQuery(html).is("img") ) {
            var imgurl = jQuery(html).attr('src');
        }
        
        jQuery('#upload_image').val(imgurl);
        tb_remove();
    }
    




    /*
        
        ADMIN UPLOAD IMAGE 
        
    */
    
    jQuery(".zp-Entry-Image").each(function() {
        if (jQuery(this).find("img").length > 0)
            jQuery(this).addClass("hasImage");
    });




    /*
        
        DELETE IMAGE
        
    */

    jQuery('.zp-Entry-Image').delegate("a.delete", "click", function () {
        
            $this = jQuery(this);
            
            var confirmDelete = confirm("Are you sure you want to remove this image?");
            
            if (confirmDelete==true)
            {
                // Set up uri
                var xmlUri = jQuery('span#ZOTPRESS_PLUGIN_URL').text() + $this.attr("rel");
                
                // AJAX
                jQuery.get(xmlUri, {}, function(xml)
                {
                    var $result = jQuery('result', xml).attr('success');
                    
                    if ($result == "true")
                        window.location.reload(true);
                    else // Show errors
                        alert("Sorry - couldn't remove that image!");
                });
            }
        
    });



});