jQuery(document).ready( function()
{
    
    
    /*
     
        SETUP BUTTONS
        
    */

    jQuery("input#zp-Zotpress-Setup-Options-Next").click(function()
    {
        window.parent.location = "admin.php?page=Zotpress&setup=true&setupstep=three";
        return false;
    });

    jQuery("input#zp-Zotpress-Setup-Options-Complete").click(function()
    {
        if ( jQuery(this).hasClass("import") )
            window.parent.location = "admin.php?page=Zotpress";
        else
            window.parent.location = "admin.php?page=Zotpress&accounts=true";
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
                jQuery('div.zp-Success').html("<p><strong>Success!</strong> Your Zotero account has been validated.</p>\n");
                
                jQuery('div.zp-Success').show();
                
                // SETUP
                if (jQuery("div#zp-Setup").length > 0)
                {
                    jQuery.doTimeout(1000,function() {
                        window.parent.location = "admin.php?page=Zotpress&setup=true&setupstep=two";
                    });
                }
                
                // REGULAR
                else 
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
        
        REMOVE ACCOUNT
        
    */

    jQuery('#zp-Accounts').delegate(".actions a.delete", "click", function () {
        
        $this = jQuery(this);
        $thisProject = $this.parent().parent();
        
        var confirmDelete = confirm("Are you sure you want to remove this account?");
        
        if (confirmDelete==true)
        {
            var xmlUri = jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/actions/actions.php?delete=' + $this.attr("href").replace("#", "");
            
            jQuery.get(xmlUri, {}, function(xml)
            {
                if ( jQuery('result', xml).attr('success') == "true" )
                {
                    if ( jQuery('result', xml).attr('total_accounts') == 0 )
                        window.location = 'admin.php?page=Zotpress';
                    else
                        window.location = 'admin.php?page=Zotpress&accounts=true';
                }
                else
                {
                    alert( "Sorry - couldn't delete that account." );
                }
            });
        }
        
    });
    
    
    
    /*
     
        SET UP IMPORT BUTTON
        
    */
    
    jQuery("iframe#zp-Setup-Import").ready(function()
    {
        jQuery("input#zp-Zotpress-Setup-Import").removeAttr('disabled');
        jQuery("input.zp-Import-Button").removeAttr('disabled');
        
        // IMPORT ITEMS
        jQuery("input#zp-Zotpress-Setup-Import-Items").click(function()
        {
            jQuery(".import .zp-Loading-Initial").show();
            jQuery(".import .zp-Import-Messages").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=items&singlestep=true");
            
            return false;
        });
        
        // IMPORT COLLECTIONS
        jQuery("input#zp-Zotpress-Setup-Import-Collections").click(function()
        {
            jQuery(".import .zp-Loading-Initial").show();
            jQuery(".import .zp-Import-Messages").text("Importing collections 1-50 ...").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=collections&singlestep=true");
            
            return false;
        });
        
        // IMPORT TAGS
        jQuery("input#zp-Zotpress-Setup-Import-Tags").click(function()
        {
            jQuery(".import .zp-Loading-Initial").show();
            jQuery(".import .zp-Import-Messages").text("Importing tags 1-50 ...").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=tags&singlestep=true");
            
            return false;
        });
        
        // IMPORT EVERYTHING
        jQuery("input#zp-Zotpress-Setup-Import").click(function()
        {
            jQuery(".import .zp-Loading-Initial").show();
            jQuery(".import .zp-Import-Messages").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=items");
            
            return false;
        });
    });
    
    
    
    /*
        
        SET UP SYNC BUTTON
        
    */

    //jQuery('div#zp-AccountsList div.zp-Account .actions a.sync').click(function(e)
    //{
    //    var $this = jQuery(this);
    //    
    //    // Disable sync link until done
    //    e.preventDefault();
    //    
    //    // Prep and show loading sign
    //    $this.removeClass("success");
    //    $this.removeClass("error");
    //    $this.addClass("syncing");
    //    
    //    // Add sync iframe to DOM
    //    if (jQuery("iframe#zp-Sync-" + jQuery("span", $this).text()).length == 0)
    //    {
    //        jQuery('<iframe/>', {
    //            id: 'zp-Sync-' + jQuery('span.api_user_id', $this.parent().parent()).text(),
    //            'class': 'zp-Setup-Sync', // IE ISSUE - needs quotations around class
    //            //src: jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/admin/admin.sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span#ZOTPRESS_PASSCODE").text() + '&step=items',
    //            src: jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/admin/admin.sync.php?api_user_id=' + $this.attr("rel") + '&step=items',
    //            scrolling: 'yes'
    //        }).appendTo('#zp-ManageAccounts');
    //    }
    //    else
    //    {
    //        jQuery("iframe#zp-Sync-" + jQuery("span", $this).text()).attr("src", jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/admin/admin.sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span", $this).text() + '&step=items');
    //    }
    //    
    //    $this.parent().find('.zp-Sync-Messages').text("Syncing items 1-50 ...");
    //    
    //    return false;
    //});
    
    
    
    
    /*
        
        SELECTIVE IMPORT BY COLLECTION
        
    */
    
    jQuery("iframe#zp-Step-Import-Collection-Frame").on("load", function()
    {
        jQuery("#zp-Step-Import-Collection").removeClass("loading");
        jQuery("#zp-Step-Import-Collection, iframe#zp-Step-Import-Collection-Frame").animate({ height: jQuery("iframe#zp-Step-Import-Collection-Frame").contents().find(".zp-Collection-List").outerHeight() + "px"}, 0);
        jQuery("input#zp-Zotpress-Setup-Import-Selective").removeAttr('disabled');
    });
    
    jQuery("#zp-Zotpress-Setup-Import-Selective").click(function ()
    {
        if ( jQuery("#zp-Step-Import-Collection-Frame").contents().find(".zp-Collection.selected").length > 0 )
        {
            var zpSelectedCollections = "";
            
            jQuery("#zp-Step-Import-Collection-Frame").contents().find(".zp-Collection.selected").each( function()
            {
                zpSelectedCollections += jQuery(this).attr("rel") + ",";
            });
            
            zpSelectedCollections = zpSelectedCollections.slice(0, - 1);
            
            jQuery(".selective.zp-Loading-Initial").show();
            jQuery(".selective.zp-Import-Messages").text("Importing items 1-50 ....").show();
            jQuery(this).attr('disabled', 'true');
            
            jQuery("iframe#zp-Setup-Import").attr('src', jQuery("iframe#zp-Setup-Import").attr('src') + "&go=true&step=selective&collections=" + zpSelectedCollections);
            
            return false;
        }
        else
        {
            alert ("Please select at least one collection to import."); return false;
        }
    });
    
    


});