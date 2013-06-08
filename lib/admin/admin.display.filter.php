
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
            window.location = "admin.php?page=Zotpress&account_id="+id;
        });
        
        
        // FILTER BY TAG
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByTag", "change", function()
        {
            window.location = "admin.php?page=Zotpress&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&tag_id="+jQuery(this).val();
        });
