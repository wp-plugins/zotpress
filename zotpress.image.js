jQuery(document).ready(function() {
    


    /*
    
           UPLOAD IMAGE FORM
            
    */

    jQuery('#zp-Submit').click(function () {
        
            // Plunk it together
            var data = 'image=true'
                        + '&account_type=' + jQuery('input[name=account_type]').val()
                        + '&api_user_id=' + jQuery('input[name=api_user_id]').val()
                        + '&citation_id=' + jQuery('input[name=citation_id]').val()
                        + '&upload_image=' + jQuery('input[name=upload_image]').val();
            
            // Disable all the text fields
            jQuery('input[name!=update], textarea, select').attr('disabled','true');
            
            // Show the loading sign
            jQuery('.zp-Errors').hide();
            jQuery('.zp-Success').hide();
            jQuery('.zp-Loading').show();
            
            // Set up uri
            var xmlUri = jQuery('input[name=ZOTPRESS_PLUGIN_URL]').val() + 'zotpress.actions.php?'+data;
            
            if (jQuery('input[name=update]').val() !== undefined)
                xmlUri += "&update=" + jQuery('input[name=update]').val();
            
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
                        window.location = "admin.php?page=Zotpress&display=true";
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
    
            CANCEL BUTTON
            
    */

    jQuery('input#zp-Cancel').click(function() {
        
        window.location = "admin.php?page=Zotpress&display=true";
        
        return false;
    });
    


    /*
     
        UPLOAD IMAGE
    */
    
    
    jQuery('#upload_image_button').click(function() {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });
    
    window.send_to_editor = function(html)
    {
        if ( jQuery(html).is("a") ) {
            //var imgurl = jQuery('img', html).attr('src');
            var imgurl = jQuery(html).attr('href');
        } else if ( jQuery(html).is("img") ) {
            var imgurl = jQuery(html).attr('src');
        }
        
        jQuery('#upload_image').val(imgurl);
        tb_remove();
    }
    




    /*
    
            DELETE IMAGE
            
    */

    jQuery('.zp-Entry-Image').delegate("a.delete", "click", function () {
        
            $this = jQuery(this);
            
            var confirmDelete = confirm("Are you sure you want to remove this image?");
            
            if (confirmDelete==true)
            {
                // Set up uri
                var xmlUri = jQuery('span#ZOTPRESS_PLUGIN_URL').text() + 'zotpress.image.php?delete=' + $this.attr("rel");
                
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