jQuery(document).ready(function() {



    /*
     
        QTIP HELP
        
    */

    jQuery('label.zp-Help[title]').qtip({
        style: {
            classes: 'ui-tooltip-shadow ui-tooltip-tipsy',
            width: 300
        },
        position: {
            my: 'bottom center',
            at: 'top center'
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
     
        SETUP "NEXT" BUTTON 
        
    */

    jQuery("input#zp-Zotpress-Setup-Options-Next").click(function()
    {
        window.parent.location = "admin.php?page=Zotpress&setup=true&setupstep=three";
        return false;
    });
    
    
    
    /*
        
        SYNC ACCOUNT WITH ZOTPRESS
        
    */

    jQuery('#zp-Connect').click(function ()
    {
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
        var xmlUri = jQuery('input[name=ZOTPRESS_PLUGIN_URL]').val() + 'lib/actions/actions.php?'+data;
        
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
                
                // SETUP or regular
                if (jQuery("div#zp-Setup").length > 0)
                {
                    jQuery.doTimeout(1000,function() {
                        window.parent.location = "admin.php?page=Zotpress&setup=true&setupstep=two";
                    });
                }
                else // REGULAR
                {
                    jQuery.doTimeout(1000,function()
                    {
                        jQuery('div#zp-AddAccount').slideUp("fast");
                        jQuery('form#zp-Add')[0].reset();
                        jQuery('input[name!=update], textarea, select').removeAttr('disabled');
                        jQuery('div.zp-Success').hide();
                        
                        DisplayAccounts();
                    });
                }
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
     
        OAUTH MODAL
        
    */
    
    jQuery('a.zp-OAuth-Button').livequery('click', function() { 
        tb_show('', jQuery(this).attr('href')+'&TB_iframe=true');
        return false;
    });


    

    /*
        
        DELETE ACCOUNT
        
    */

    jQuery('#zp-Accounts').delegate("span.delete a.delete", "click", function () {
        
        $this = jQuery(this);
        $thisProject = $this.parent().parent();
        
        var confirmDelete = confirm("Are you sure you want to remove this account?");
        
        if (confirmDelete==true)
        {
            // Set up uri
            var xmlUri = jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/actions/actions.php?delete=' + $this.attr("href").replace("#", "");
            
            // AJAX
            jQuery.get(xmlUri, {}, function(xml)
            {
                var $result = jQuery('result', xml).attr('success');
                
                if ($result == "true")
                    //DisplayAccounts();
                    window.location.reload();
                else // Show errors
                    alert("Sorry - couldn't delete that account!");
            });
        }
        
    });
    
    
    
    /*
     
        SET UP IMPORT BUTTON
        
    */
    
    jQuery("iframe#zp-Setup-Import").ready(function()
    {
        jQuery("input#zp-Zotpress-Setup-Import").removeAttr('disabled');
        
        jQuery("input#zp-Zotpress-Setup-Import").click(function()
        {
            jQuery("div.zp-Loading-Initial").show();
            jQuery("#zp-Import-Messages").show();
            jQuery(this).attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=items");
            
            return false;
        });
    });
    
    
    
    /*
        
        SET UP SYNC BUTTON
        
    */

    jQuery('div#zp-AccountsList div.zp-Account span.delete a.sync').click(function(e)
    {
        var $this = jQuery(this);
        
        // Disable sync link until done
        e.preventDefault();
        
        // Prep and show loading sign
        $this.removeClass("success");
        $this.removeClass("error");
        $this.addClass("syncing");
        
        // Add sync iframe to DOM
        if (jQuery("iframe#zp-Sync-" + jQuery("span", $this).text()).length == 0)
        {
            jQuery('<iframe/>', {
                id: 'zp-Sync-' + jQuery("span", $this).text(),
                class: 'zp-Setup-Sync',
                src: jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/admin/admin.sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span", $this).text() + '&step=items',
                scrolling: 'yes'
            }).appendTo('#zp-ManageAccounts');
        }
        else
        {
            jQuery("iframe#zp-Sync-" + jQuery("span", $this).text()).attr("src", jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/admin/admin.sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span", $this).text() + '&step=items');
        }
        
        $this.parent().find('.zp-Sync-Messages').text("Syncing items 1-50 ...");
        
        return false;
    });


});