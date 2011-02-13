jQuery(document).ready(function() {
    
    /*
     
       FORM EFFECTS
       
    */


    jQuery('#account_type').change( function()
    {
        // GROUPS
        if (jQuery(this).val() == "groups") {
            jQuery('span#zp-ID-Label').text("Group");
            jQuery("div.zp-public_key").hide();
        }
        
        // USERS
        else {
            jQuery('span#zp-ID-Label').text("API User");
            jQuery("div.zp-public_key").show();
        }
    });
   
    
    
    /*
     
        QTIP HELP
        
    */


    jQuery('label.zp-Help[title]').qtip({
        style: {
            name: 'dark',
            tip: true,
            width: 170,
            fontSize: '11px'
        },
        position: {
            corner: {
                target: 'rightMiddle',
                tooltip: 'leftMiddle'
            }
        },
        show : {
            delay: 0,
            effect: {
                type: 'none',
                length: 0
            }
        }
    });



    /*
     
        DISPLAY ACCOUNTS
        
    */


    function DisplayAccounts()
    {
        
        var xmlUri = jQuery('input#ZOTPRESS_PLUGIN_URL').val()+'zotpress.actions.php?display=true';
        
        jQuery('div#zp-AccountsList').empty();
        jQuery('div#zp-AccountsList').addClass("zp-Loading");
        
        jQuery.get(xmlUri, {}, function(xml)
        {
            var active_accounts = "";
            
            jQuery(xml).find('account').each(function() {
                active_accounts += jQuery(this).find('code').text();
            });
            
            if (active_accounts.length > 0)
                jQuery('div#zp-AccountsList').append(active_accounts);
            else
                jQuery('div#zp-AccountsList').append("<p>No accounts found.</p>\n");
            
            jQuery('div#zp-AccountsList').removeClass('zp-Loading');
        });
    }
    
    if (jQuery('input#ZOTPRESS_PLUGIN_URL').length != 0)
        DisplayAccounts();
    
    
    
    /*
    
            ADD ACCOUNT TO ZOTERO
            
    */

    jQuery('#zp-Connect').click(function () {
        
            //organize the data properly
            var data = 'connect=true'
                        + '&account_type=' + jQuery('select[name=account_type] option:selected').val()
                        + '&api_user_id=' + jQuery('input[name=api_user_id]').val()
                        + '&public_key=' + jQuery('input[name=public_key]').val()
                        + '&nickname=' + escape(jQuery('input[name=nickname]').val());
            
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
                    jQuery('div.zp-Success').html("<p><strong>Success!</strong> You're now connected to Zotero.</p>\n");
                    
                    jQuery('div.zp-Success').show();
                    
                    jQuery.doTimeout(1000,function() {
                        jQuery('div.zp-Success').hide();
                        jQuery('form#zp-Add')[0].reset();
                        jQuery('input[name!=update], textarea, select').removeAttr('disabled');
                        
                        DisplayAccounts();
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
            
            //cancel the submit button default behaviours
            return false;
    });


    

    /*
    
            DELETE ACCOUNT
            
    */

    jQuery('#zp-Accounts').delegate("span.delete a", "click", function () {
        
            $this = jQuery(this);
            $thisProject = $this.parent().parent();
            
            var confirmDelete = confirm("Are you sure you want to remove this account?");
            
            if (confirmDelete==true)
            {
                // Set up uri
                var xmlUri = jQuery('input[name=ZOTPRESS_PLUGIN_URL]').val() + 'zotpress.actions.php?delete=' + $this.attr("href").replace("#", "");
                
                // AJAX
                jQuery.get(xmlUri, {}, function(xml)
                {
                    var $result = jQuery('result', xml).attr('success');
                
                    if ($result == "true")
                        DisplayAccounts();
                    else // Show errors
                        alert("Sorry - couldn't delete that account!");
                });
            }
        
    });
    
    

    /*
    
            CITATION IMAGE HOVER
            
    */

    jQuery('div#zp-List').delegate("div.zp-Entry-Image", "hover", function () {
        jQuery(this).toggleClass("hover");
    });
    
    
    
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



});