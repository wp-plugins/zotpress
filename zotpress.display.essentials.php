        
        // DETERMINE BROWSER
        
        var browser_is_IE = false;
        
        jQuery.each(jQuery.browser, function() {
            if (jQuery.browser.msie)
                browser_is_IE = true;
        });
        
        // GET ACCOUNTS
        var accounts = new Array();
        
        <?php
        
        foreach ($zp_accounts as $zp_account)
            echo "accounts[accounts.length] = {'id': '".$zp_account->id."', 'account_type': '".$zp_account->account_type."', 'api_user_id': '".$zp_account->api_user_id."', 'public_key': '".$zp_account->public_key."', 'nickname': '".addslashes($zp_account->nickname)."'};\n";
            
        ?>
        
        
        // DISPLAY ACCOUNTS
        
        jQuery.each(accounts, function(id, account) {
            if (account.nickname != "")
                jQuery('select#zp-FilterByAccount').prepend("<option value='"+id+"'>"+account.nickname+"</option>\n");
            else
                jQuery('select#zp-FilterByAccount').prepend("<option value='"+id+"'>"+account.api_user_id+"</option>\n");
        });
        
        jQuery('span#zp-FilterByAccount-Loading').remove();
        
        
        // MAKE XML WORK IN IE
        // Thanks to Bigabdoul at Stackoverflow.com
        
        function createXmlDOMObject(xmlString)
        {
            var xmlDoc = null;
            
            if( ! window.DOMParser )
            {
                // the xml string cannot be directly manipulated by browsers 
                // such as Internet Explorer because they rely on an external 
                // DOM parsing framework...
                // create and load an XML document object through the DOM 
                // ActiveXObject that it can deal with
                xmlDoc = new ActiveXObject( "Microsoft.XMLDOM" );
                xmlDoc.async = false;
                xmlDoc.loadXML( xmlString );
            }
            else
            {
                // the current browser is capable of creating its own DOM parser
                parser = new DOMParser();
                xmlDoc = parser.parseFromString( xmlString, "text/xml" ) ;
            }
            
            return xmlDoc;
        }
        