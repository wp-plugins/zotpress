        
        /*
            FILTER CITATIONS
        */
        
        
        // FILTER BY ACCOUNT
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByAccount", "change", function()
        {
            var id = jQuery(this).val();
            window.location = "admin.php?page=Zotpress&display=true&account_id="+id+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY COLLECTION
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByCollection", "change", function()
        {
            window.location = "admin.php?page=Zotpress&display=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery(this).val()+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY TAG
        
        jQuery('div#zp-Filter').delegate("select#zp-FilterByTag", "change", function()
        {
            window.location = "admin.php?page=Zotpress&display=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&tag_name="+jQuery(this).val()+"&limit="+jQuery('input#zp-FilterByLimit').val();
        });
        
        
        // FILTER BY LIMIT
        
        jQuery('input#zp-FilterByLimit').bind({
            blur: function()
            {
                window.location = "admin.php?page=Zotpress&display=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery('select#zp-FilterByCollection option:selected').val()+"&tag_name="+jQuery('select#zp-FilterByTag option:selected').val()+"&limit="+jQuery(this).val();
            },
            keypress: function(e)
            {
                if (e.keyCode == 13) {
                    window.location = "admin.php?page=Zotpress&display=true&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&collection_id="+jQuery('select#zp-FilterByCollection option:selected').val()+"&tag_name="+jQuery('select#zp-FilterByTag option:selected').val()+"&limit="+jQuery(this).val();
                }
            }
        });
        