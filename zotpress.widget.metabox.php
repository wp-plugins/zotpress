<style type="text/css">

    div#ZotpressMetaBox h3.hndle span {
        background: transparent url('<?php echo ZOTPRESS_PLUGIN_URL; ?>/images/icon.png') no-repeat left center;
        padding-left: 22px;
    }
    
    div#zp-ZotpressMetaBox-Tabs {
        border-radius: 5px;
        -moz-border-radius: 5px;
        border: 1px solid #ccc;
        padding: 1px;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav {
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        background-color: #f6f6f6;
        padding: 3px;
        padding-bottom: 0;
        overflow: hidden;
        height: 20px;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li {
        margin: 0;
        padding: 0 5px;
        float: left;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active {
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        background-color: #fff;
        height: 20px;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active a {
        color: #333;
        vertical-align: bottom;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li a {
        font: 9px/10px 'Arial', sans-serif;
        letter-spacing: 1px;
        padding: 0 3px;
        text-decoration: none;
        text-transform: uppercase;
    }
    
    div#zp-ZotpressMetaBox-Output {
        display: none;
        padding-top: 5px;
    }
    div#ZotpressMetaBox label {
        margin-left: 5px;
        font-weight: bold;
    }
    div#ZotpressMetaBox div.zp-Tab label {
        display: block;
        margin-top: 5px;
        padding: 5px 0;
    }
    div#ZotpressMetaBox div.zp-Tab select {
        width: 100%;
        height: 5em !important;
    }
    div#ZotpressMetaBox div.zp-Tab select#zp-ZotpressMetaBox-Collection-Collections,
    div#ZotpressMetaBox div.zp-Tab select#zp-ZotpressMetaBox-Collection-Items,
    div#ZotpressMetaBox div.zp-Tab select#zp-ZotpressMetaBox-Tags-Tags,
    div#ZotpressMetaBox div.zp-Tab select#zp-ZotpressMetaBox-Tags-Items {
        height: 10em !important;
    }

    div#zp-ZotpressMetaBox-Tabs div.zp-Loading {
        background: transparent url('<?php echo ZOTPRESS_PLUGIN_URL; ?>/images/loading_list.gif') no-repeat center;
        letter-spacing: -1000px;
        overflow: hidden;
        text-indent: -5000px;
        width: 100%;
        height: 50px;
    }
    
    div.zp-Tab {
        padding: 6px;
        padding-top: 2px;
    }
    div.zp-Tab span.label {
        margin-top: 5px;
        font-weight: bold;
    }
    div.zp-Tab span.label em {
        font-style: normal;
        text-decoration: underline;
    }
    
</style>

<script>
    jQuery(function()
    {
        
        // SET UP CITATIONS AND IMAGES ARRAY
        
        window.citations = new Array();
        window.citation_images = new Array();
        
        
        // DETERMINE BROWSER
        
        var browser_is_IE = false;
        
        jQuery.each(jQuery.browser, function() {
            if (jQuery.browser.msie)
                browser_is_IE = true;
        });
        
        
        // HTML ENCODE ENTITIES
        // Thanks to Markus Ernst at Bytes.com
        function htmlentities( text )
        {
            text = text.replace(/&/g,"&amp;");
            text = text.replace(/</g,"&lt;");
            text = text.replace(/>/g,"&gt;");
            
            return text;
        }
        
        
        // MAKE XML WORK IN IE
        // Thanks to Bigabdoul at Stackoverflow.com
        
        function createXmlDOMObject(xmlString)
        {
            var xmlDoc = null;
            
            if( ! window.DOMParser )
            {
                xmlDoc = new ActiveXObject( "Microsoft.XMLDOM" );
                xmlDoc.async = false;
                xmlDoc.loadXML( xmlString );
            }
            else // All browsers other than IE
            {
                parser = new DOMParser();
                xmlDoc = parser.parseFromString( xmlString, "text/xml" ) ;
            }
            
            return xmlDoc;
        }
        
        
        // ZOTPRESS REFERENCE TABS
        
        jQuery( "#zp-ZotpressMetaBox-Tabs" ).tabs();
        
        
        // COLLECTIONS: ACCOUNTS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Accounts option").attr("selected", false);
        
        jQuery("#zp-ZotpressMetaBox-Collection-Accounts option").click( function()
        {
            if (jQuery(this).val() != "")
            {
                var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery(this).val()+'&api_user_id='+jQuery(this).text()+'&data_type=collections';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriCollections,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    ifModified: false, // Change to true when implemented on Zotero end
                    beforeSend: function()
                    {
                        // Remove existing
                        if (jQuery("#zp-ZotpressMetaBox-Collection-Collections").length > 0)
                            jQuery("#zp-ZotpressMetaBox-Collection-Collections").parent().remove();
                        
                        // Add loading
                        jQuery("#zp-ZotpressMetaBox-Collection-Accounts").parent().append("<div class='zp-Loading'>loading...</div>\n");
                    },
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var collectionsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Collection-Collections'>Collections:</label>\n";
                        collectionsSelect += "<select id='zp-ZotpressMetaBox-Collection-Collections' multiple='yes'>\n";
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            collectionsSelect += "<option value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+"</option>\n";
                        });
                        collectionsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Collection-Accounts").after(collectionsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Collection-Accounts").parent().find("div.zp-Loading").remove();
                        
                        // Open up output
                        jQuery("#zp-ZotpressMetaBox-Output").show();
                    }
                });
            }
        });
        
        
        // COLLECTIONS: COLLECTIONS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Collections option").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
                
                // Build citation url
                var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("#zp-ZotpressMetaBox-Collection-Accounts option:selected").val()+'&api_user_id='+jQuery("#zp-ZotpressMetaBox-Collection-Accounts option:selected").text()+'&collection_id='+jQuery(this).val();
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriCollections,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    ifModified: false, // Change to true when implemented on Zotero end
                    beforeSend: function()
                    {
                        // Remove existing
                        if (jQuery("#zp-ZotpressMetaBox-Collection-Items").length > 0)
                            jQuery("#zp-ZotpressMetaBox-Collection-Items").parent().remove();
                        
                        // Add loading
                        jQuery("#zp-ZotpressMetaBox-Collection-Collections").parent().append("<div class='zp-Loading'>loading...</div>\n");
                    },
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var collectionsItemsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Collection-Items'>Items:</label>\n";
                        collectionsItemsSelect += "<select id='zp-ZotpressMetaBox-Collection-Items' multiple='yes'>\n";
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            collectionsItemsSelect += "<option value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+"</option>\n";
                        });
                        collectionsItemsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Collection-Collections").after(collectionsItemsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Collection-Collections").parent().find("div.zp-Loading").remove();
                    }
                });
            }
        });
        
        
        // COLLECTIONS: ITEMS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Items option").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
            }
        });
        
        
        
        // TAGS: ACCOUNTS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Accounts option").attr("selected", false);
        
        jQuery("#zp-ZotpressMetaBox-Tags-Accounts option").click( function()
        {
            if (jQuery(this).val() != "")
            {
                var xmlUriTags = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery(this).val()+'&api_user_id='+jQuery(this).text()+'&data_type=tags';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriTags,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    ifModified: false, // Change to true when implemented on Zotero end
                    beforeSend: function()
                    {
                        // Remove existing
                        if (jQuery("#zp-ZotpressMetaBox-Tags-Collections").length > 0)
                            jQuery("#zp-ZotpressMetaBox-Tags-Collections").parent().remove();
                        
                        // Add loading
                        jQuery("#zp-ZotpressMetaBox-Tags-Accounts").parent().append("<div class='zp-Loading'>loading...</div>\n");
                    },
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var tagsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Tags-Tags'>Tags:</label>\n";
                        tagsSelect += "<select id='zp-ZotpressMetaBox-Tags-Tags' multiple='yes'>\n";
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            tagsSelect += "<option value='"+jQuery(this).find("title").text().replace(" ", "+")+"'>"+jQuery(this).find("title").text()+"</option>\n";
                        });
                        tagsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Tags-Accounts").after(tagsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Tags-Accounts").parent().find("div.zp-Loading").remove();
                        
                        // Open up output
                        jQuery("#zp-ZotpressMetaBox-Output").show();
                    }
                });
            }
        });
        
        
        // TAGS: COLLECTIONS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Tags option").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
                
                // Build citation url
                var xmlUriTags = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("#zp-ZotpressMetaBox-Tags-Accounts option:selected").val()+'&api_user_id='+jQuery("#zp-ZotpressMetaBox-Tags-Accounts option:selected").text()+'&tag_name='+escape( jQuery(this).val() );
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriTags,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    ifModified: false, // Change to true when implemented on Zotero end
                    beforeSend: function()
                    {
                        // Remove existing
                        if (jQuery("#zp-ZotpressMetaBox-Tags-Items").length > 0)
                            jQuery("#zp-ZotpressMetaBox-Tags-Items").parent().remove();
                        
                        // Add loading
                        jQuery("#zp-ZotpressMetaBox-Tags-Tags").parent().append("<div class='zp-Loading'>loading...</div>\n");
                    },
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var tagsItemsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Tags-Items'>Items:</label>\n";
                        tagsItemsSelect += "<select id='zp-ZotpressMetaBox-Tags-Items' multiple='yes'>\n";
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            tagsItemsSelect += "<option value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+"</option>\n";
                        });
                        tagsItemsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Tags-Tags").after(tagsItemsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Tags-Tags").parent().find("div.zp-Loading").remove();
                    }
                });
            }
        });
        
        
        // TAGS: ITEMS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Items option").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
            }
        });
        
    });
</script>

<p>Search for item keys, collection ids and tag names using the form below:</p>

<div id="zp-ZotpressMetaBox-Tabs">
    
    <ul>
        <li><a href="#zp-ZotpressMetaBox-Tabs-1">Account List</a></li>
        <li><a href="#zp-ZotpressMetaBox-Tabs-2">By Collection</a></li>
        <li><a href="#zp-ZotpressMetaBox-Tabs-3">By Tag</a></li>
    </ul>
    
    
    <!-- START OF Account List -->
    <div id="zp-ZotpressMetaBox-Tabs-1" class="zp-Tab">
<?php
    global $wpdb;
    $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    
    foreach ($accounts as $account)
    {
        echo "<p><span class='label'>";
        if (isset( $account->nickname ))
            echo "<em>".$account->nickname."</em>'s ";
        echo substr( $account->account_type, 0, strlen( $account->account_type )-1 )." ID</span>: ".$account->api_user_id."</p>\n";
    }
?>
    </div>
    <!-- END OF Account List -->
    
    
    <!-- START OF By Collection -->
    <div id="zp-ZotpressMetaBox-Tabs-2" class="zp-Tab">
        
        <label for="zp-ZotpressMetaBox-Collection-Accounts">Choose Account:</label>
        <select id="zp-ZotpressMetaBox-Collection-Accounts" multiple="yes">
        <?php
            foreach ($accounts as $account)
                echo "<option id='".$account->api_user_id."' value='".$account->account_type."'>".$account->api_user_id."</option>\n";
        ?>
        </select>
        
    </div>
    <!-- END OF By Collection -->
    
    
    <!-- START OF By Tags -->
    <div id="zp-ZotpressMetaBox-Tabs-3" class="zp-Tab">
        
        <label for="zp-ZotpressMetaBox-Tags-Accounts">Choose Account:</label>
        <select id="zp-ZotpressMetaBox-Tags-Accounts" multiple="yes">
        <?php
            foreach ($accounts as $account)
                echo "<option id='".$account->api_user_id."' value='".$account->account_type."'>".$account->api_user_id."</option>\n";
        ?>
        </select>
        
    </div>
    <!-- END OF By Tag -->
    
</div>

<div id="zp-ZotpressMetaBox-Output">
    <label for="zp-ZotpressMetaBox-Output-Text">Copy this:</label>
    <input id="zp-ZotpressMetaBox-Output-Text" type="text" size="28" />
</div>