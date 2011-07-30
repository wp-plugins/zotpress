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
        padding: 5px 5px 0;
        float: left;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active {
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        background-color: #fff;
        height: 20px;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li a {
        font: 9px/10px 'Arial', sans-serif;
        letter-spacing: 1px;
        padding: 0 3px;
        text-decoration: none;
        text-transform: uppercase;
        vertical-align: top;
    }
    div#zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active a {
        color: #333;
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
        background-color: #fff;
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
        
        // DETERMINE BROWSER
        
        var browser_is_IE = false;
        var browser_is_Safari_Chrome = false;
        
        jQuery.each(jQuery.browser, function() {
            if (jQuery.browser.msie)
                browser_is_IE = true;
            else if (jQuery.browser.safari || jQuery.browser.webkit)
                browser_is_Safari_Chrome = true;
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
        
        
        // SORT ASSOCIATIVE ARRAY (JS OBJECT)
        // Thanks tohttp://www.latentmotion.com/how-to-sort-an-associative-array-object-in-javascript/
        function sortObj(arr)
        {
            // Setup Arrays
            var sortedKeys = new Array();
            var sortedObj = {};
            
            // Separate keys and sort them
            for (var i in arr){
                sortedKeys.push(i);
            }
            sortedKeys.sort();
            
            // Reconstruct sorted obj based on keys
            for (var i in sortedKeys){
                sortedObj[sortedKeys[i]] = arr[sortedKeys[i]];
            }
            return sortedObj;
        }
        
        
        
        // ZOTPRESS REFERENCE TABS
        
        jQuery( "#zp-ZotpressMetaBox-Tabs" ).tabs(
        {
            select: function(event, ui)
            {
                // Hide output
                jQuery("#zp-ZotpressMetaBox-Output").hide();
                
                // Clear output
                jQuery("#zp-ZotpressMetaBox-Output").val("");
            }
        });
        
        
        // COLLECTIONS: ACCOUNTS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Accounts option").attr("selected", false);
        
        jQuery("#zp-ZotpressMetaBox-Collection-Accounts").click( function()
        {
            if (jQuery(this).val() != "")
            {
                // Remove existing
                if (jQuery("#zp-ZotpressMetaBox-Collection-Collections").length > 0)
                    jQuery("#zp-ZotpressMetaBox-Collection-Collections").parent().remove();
                
                // Add loading
                jQuery("#zp-ZotpressMetaBox-Tabs").append("<div class='zp-Loading'>loading...</div>\n");
                
                // Set up xml url
                var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("option:selected", this).attr("class")+'&api_user_id='+jQuery("option:selected", this).attr("id")+'&data_type=collections&limit=150';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriCollections,
                    dataType: "XML",
                    cache: false,
                    async: true,
                    ifModified: false, // Change to true when implemented on Zotero end
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var collectionsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Collection-Collections'>Collections:</label>\n";
                        collectionsSelect += "<select id='zp-ZotpressMetaBox-Collection-Collections' multiple='yes'>\n";
                        
                        var collectionsArray = new Array();
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            if (browser_is_Safari_Chrome)
                                collectionsArray[jQuery(this).find("title").text().replace(" ","+")] = "<option value='"+jQuery(this.getElementsByTagName("key")[0]).text()+"'>"+jQuery(this).find("title").text()+" ("+jQuery(this.getElementsByTagName("numItems")[0]).text()+")</option>\n";
                            else
                                collectionsArray[jQuery(this).find("title").text().replace(" ","+")] = "<option value='"+jQuery(this).find("zapi\\:key").text()+"'>"+jQuery(this).find("title").text()+" ("+jQuery(this).find("zapi\\:numItems").text()+")</option>\n";
                        });
                        
                        // Add to select
                        collectionsArray = sortObj( collectionsArray );
                        
                        for (var i in collectionsArray)
                            collectionsSelect += collectionsArray[i];
                        
                        collectionsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Collection-Accounts").after(collectionsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Tabs").find("div.zp-Loading").remove();
                        
                        // Open up output
                        jQuery("#zp-ZotpressMetaBox-Output").show();
                    }
                });
            }
        });
        
        
        // COLLECTIONS: COLLECTIONS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Collections").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
                
                // Remove existing
                if (jQuery("#zp-ZotpressMetaBox-Collection-Items").length > 0)
                    jQuery("#zp-ZotpressMetaBox-Collection-Items").parent().remove();
                
                // Add loading
                jQuery("#zp-ZotpressMetaBox-Tabs").append("<div class='zp-Loading'>loading...</div>\n");
                
                // Build citation url
                var xmlUriCollections = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("#zp-ZotpressMetaBox-Collection-Accounts option").attr("class")+'&api_user_id='+jQuery("#zp-ZotpressMetaBox-Collection-Accounts option:selected").attr("id")+'&collection_id='+jQuery(this).val()+'&limit=150';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriCollections,
                    dataType: "XML",
                    cache: false,
                    async: true,
                    ifModified: false, // Change to true when implemented on Zotero end
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                            
                        // Build select
                        var collectionsItemsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Collection-Items'>Items:</label>\n";
                        collectionsItemsSelect += "<select id='zp-ZotpressMetaBox-Collection-Items' multiple='yes'>\n";
                        
                        var collectionItemsArray = new Array();
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            if (browser_is_Safari_Chrome)
                                collectionItemsArray[jQuery(this.getElementsByTagName("creatorSummary")[0]).text()+"-"+jQuery(this.getElementsByTagName("key")[0]).text()] = "<option title='"+jQuery(this).find("title").text()+"' value='"+jQuery(this.getElementsByTagName("key")[0]).text()+"'>("+jQuery(this.getElementsByTagName("creatorSummary")[0]).text()+") "+jQuery(this).find("title").text()+"</option>\n";
                            else
                                collectionItemsArray[jQuery(this).find("zapi\\:creatorSummary").text()+"-"+jQuery(this).find("zapi\\:key").text()] += "<option title='"+jQuery(this).find("title").text()+"' value='"+jQuery(this).find("zapi\\:key").text()+"'>("+jQuery(this).find("zapi\\:creatorSummary").text()+") "+jQuery(this).find("title").text()+"</option>\n";
                        });
                        
                        // Add to select
                        collectionItemsArray = sortObj( collectionItemsArray );
                        
                        for (var i in collectionItemsArray)
                            collectionsItemsSelect += collectionItemsArray[i];
                        
                        collectionsItemsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Collection-Collections").after(collectionsItemsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Tabs").find("div.zp-Loading").remove();
                    }
                });
            }
        });
        
        
        // COLLECTIONS: ITEMS
        
        jQuery("#zp-ZotpressMetaBox-Collection-Items").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery("option:selected", this).val());
            }
        });
        
        
        
        // TAGS: ACCOUNTS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Accounts option").attr("selected", false);
        
        jQuery("#zp-ZotpressMetaBox-Tags-Accounts").click( function()
        {
            if (jQuery(this).val() != "")
            {
                // Remove existing
                if (jQuery("#zp-ZotpressMetaBox-Tags-Collections").length > 0)
                    jQuery("#zp-ZotpressMetaBox-Tags-Collections").parent().remove();
                
                // Add loading
                jQuery("#zp-ZotpressMetaBox-Tabs").append("<div class='zp-Loading'>loading...</div>\n");
                
                // Create xml uri
                var xmlUriTags = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("option:selected", this).attr("class")+'&api_user_id='+jQuery("option:selected", this).attr("id")+'&data_type=tags&limit=150';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriTags,
                    dataType: "XML",
                    cache: false,
                    async: true,
                    ifModified: false, // Change to true when implemented on Zotero end
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var tagsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Tags-Tags'>Tags:</label>\n";
                        tagsSelect += "<select id='zp-ZotpressMetaBox-Tags-Tags' multiple='yes'>\n";
                        
                        var tagsArray = new Array();
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            if (browser_is_Safari_Chrome)
                                tagsArray[jQuery(this).find("title").text().replace(" ","+")] = "<option value='"+jQuery(this).find("title").text().replace(" ", "+")+"'>"+jQuery(this).find("title").text()+" ("+jQuery(this.getElementsByTagName("numItems")[0]).text()+")</option>\n";
                            else
                                tagsArray[jQuery(this).find("title").text().replace(" ","+")] = "<option value='"+jQuery(this).find("title").text().replace(" ", "+")+"'>"+jQuery(this).find("title").text()+" ("+jQuery(this).find("zapi\\:numItems").text()+")</option>\n";
                        });
                        
                        // Add to select
                        tagsArray = sortObj( tagsArray );
                        
                        for (var i in tagsArray)
                            tagsSelect += tagsArray[i];
                        
                        tagsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Tags-Accounts").after(tagsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        jQuery("#zp-ZotpressMetaBox-Tabs").find("div.zp-Loading").remove();
                        
                        // Open up output
                        jQuery("#zp-ZotpressMetaBox-Output").show();
                    }
                });
            }
        });
        
        
        // TAGS: COLLECTIONS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Tags").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery(this).val());
                
                // Remove existing
                if (jQuery("#zp-ZotpressMetaBox-Tags-Items").length > 0)
                    jQuery("#zp-ZotpressMetaBox-Tags-Items").parent().remove();
                
                // Add loading
                //jQuery("#zp-ZotpressMetaBox-Tags-Tags").parent().append("<div class='zp-Loading'>loading...</div>\n");
                jQuery("#zp-ZotpressMetaBox-Tabs").append("<div class='zp-Loading'>loading...</div>\n");
                
                // Build citation url
                var xmlUriTags = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'+ 'account_type='+jQuery("#zp-ZotpressMetaBox-Tags-Accounts option:selected").attr("class")+'&api_user_id='+jQuery("#zp-ZotpressMetaBox-Tags-Accounts option:selected").attr("id")+'&tag_name='+escape( jQuery(this).val() )+'&limit=150';
                
                // Grab Zotero request
                jQuery.ajax({
                    url: xmlUriTags,
                    dataType: "XML",
                    cache: false,
                    async: true,
                    ifModified: false, // Change to true when implemented on Zotero end
                    success: function(xml, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xml = createXmlDOMObject (xml);
                        
                        // Build select
                        var tagsItemsSelect = "<div>\n<label for='zp-ZotpressMetaBox-Tags-Items'>Items:</label>\n";
                        tagsItemsSelect += "<select id='zp-ZotpressMetaBox-Tags-Items' multiple='yes'>\n";
                        
                        var tagItemsArray = new Array();
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            if (browser_is_Safari_Chrome)
                                tagItemsArray[jQuery(this.getElementsByTagName("creatorSummary")[0]).text()+"-"+jQuery(this.getElementsByTagName("key")[0]).text()] = "<option title='"+jQuery(this).find("title").text()+"' value='"+jQuery(this.getElementsByTagName("key")[0]).text()+"'>("+jQuery(this.getElementsByTagName("creatorSummary")[0]).text()+") "+jQuery(this).find("title").text()+"</option>\n";
                            else
                                tagItemsArray[jQuery(this).find("zapi\\:creatorSummary").text()+"-"+jQuery(this).find("zapi\\:key").text()] = "<option title='"+jQuery(this).find("title").text()+"' value='"+jQuery(this).find("zapi\\:key").text()+"'>("+jQuery(this).find("zapi\\:creatorSummary").text()+") "+jQuery(this).find("title").text()+"</option>\n";
                        });
                        
                        // Add to select
                        tagItemsArray = sortObj( tagItemsArray );
                        
                        for (var i in tagItemsArray)
                            tagsItemsSelect += tagItemsArray[i];
                        
                        tagsItemsSelect += "</select>\n</div>\n\n";
                        
                        jQuery("#zp-ZotpressMetaBox-Tags-Tags").after(tagsItemsSelect);
                    },
                    complete: function()
                    {
                        // Remove loading
                        //jQuery("#zp-ZotpressMetaBox-Tags-Tags").parent().find("div.zp-Loading").remove();
                        jQuery("#zp-ZotpressMetaBox-Tabs").find("div.zp-Loading").remove();
                    }
                });
            }
        });
        
        
        // TAGS: ITEMS
        
        jQuery("#zp-ZotpressMetaBox-Tags-Items").livequery("click", function()
        {
            if (jQuery(this).val() != "")
            {
                // Update output
                jQuery("#zp-ZotpressMetaBox-Output-Text").val(jQuery("option:selected", this).val());
            }
        });
        
    });
</script>

<p>Search for item keys, collection ids and tag names using the form below:</p>

<div id="zp-ZotpressMetaBox-Tabs">
    
    <ul>
        <li><a href="#zp-ZotpressMetaBox-Tabs-2">By Collection</a></li>
        <li><a href="#zp-ZotpressMetaBox-Tabs-3">By Tag</a></li>
    </ul>
    
    
    <!-- START OF By Collection -->
    <div id="zp-ZotpressMetaBox-Tabs-2" class="zp-Tab">
        
        <label for="zp-ZotpressMetaBox-Collection-Accounts">Choose Account:</label>
        <select id="zp-ZotpressMetaBox-Collection-Accounts" multiple="yes">
        <?php
        
            global $wpdb;
            $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
            
            foreach ($accounts as $account)
                if (isset( $account->nickname ))
                    echo "<option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                else
                    echo "<option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
        
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
                if (isset( $account->nickname ))
                    echo "<option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                else
                    echo "<option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
        ?>
        </select>
        
    </div>
    <!-- END OF By Tag -->
    
</div>

<div id="zp-ZotpressMetaBox-Output">
    <label for="zp-ZotpressMetaBox-Output-Text">Copy this:</label>
    <input id="zp-ZotpressMetaBox-Output-Text" type="text" size="28" />
</div>