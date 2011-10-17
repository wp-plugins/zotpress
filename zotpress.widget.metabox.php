<style type="text/css">

    div#ZotpressMetaBox h3.hndle span {
        background: transparent url('<?php echo ZOTPRESS_PLUGIN_URL; ?>/images/icon.png') no-repeat left center;
        padding-left: 22px;
    }
    
    div.zp-ZotpressMetaBox-Tabs {
        border-radius: 5px;
        -moz-border-radius: 5px;
        border: 1px solid #ccc;
        padding: 1px;
        margin-bottom: 10px;
    }
    div.zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav {
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        background-color: #f6f6f6;
        padding: 3px;
        padding-bottom: 0;
        overflow: hidden;
        /*height: 20px;*/
    }
    div.zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li {
        margin: 0;
        padding: 5px 5px 0;
        float: left;
        height: 13px;
    }
    div.zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active {
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        background-color: #fff;
        height: 13px;
    }
    div.zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li a {
        font: 9px/10px 'Arial', sans-serif;
        letter-spacing: 1px;
        padding: 0 3px;
        text-decoration: none;
        text-transform: uppercase;
        vertical-align: top;
    }
    div.zp-ZotpressMetaBox-Tabs ul.ui-tabs-nav li.ui-state-active a {
        color: #333;
    }
    div.zp-ZotpressMetaBox-Tabs input[type="radio"] {
        vertical-align: text-top;
    }
    
    div#ZotpressMetaBox h4 {
        font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
        margin: 0 0 5px 0;
        padding-top: 5px;
    }
    
    div#zp-ZotpressMetaBox-Output {
        display: none;
        padding-top: 5px;
    }
    div#ZotpressMetaBox label {
        font-family: "Arial", sans-serif;
        font-size: 12px;
        margin: 0 0 0 5px;
        font-weight: bold;
    }
    div#ZotpressMetaBox div.zp-Tab label {
        font-family: "Arial", sans-serif;
        font-size: 12px;
        display: block;
        margin: 0;
        padding: 0 0 5px 0;
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
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-0 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-1 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-3 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-4 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-5 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-6 select,
    div#ZotpressMetaBox div.zp-Tab#zp-ZotpressMetaBox-ShortcodeCreator-7 select
    {
        height: auto !important;
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
        padding: 0.6em !important;
    }
    p.zp-Note,
    div.zp-ZotpressMetaBox-Tabs p.note {
        font-size: 9px !important;
        font-style: italic !important;
        color: #aaaaaa !important;
        margin-top: 0 !important;
    }
    p.zp-Note {
        font-size: 10px !important;
        color: #888 !important;
    }
    div.zp-ZotpressMetaBox-Tabs div#zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib-Container p.note {
        color: #999 !important;
        margin: 0 0 3px 5px !important;
    }
    div.zp-Tab span.label {
        margin-top: 5px;
        font-weight: bold;
    }
    div.zp-Tab span.label em {
        font-style: normal;
        text-decoration: underline;
    }
    div#zp-ZotpressMetaBox-ShortcodeCreator-Output {
        border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #eee;
        background-color: #fafafa;
        margin: 0 5px 5px;
        padding: 4px;
    }
    div.zp-ZotpressMetaBox-RadioButtons {
        margin-bottom: 10px;
    }
    div.zp-ZotpressMetaBox-RadioButtons label {
        display: inline !important;
    }
    label#zp-ZotpressMetaBox-ShortcodeCreator-1-UserID {
        padding-left: 1em;
    }
    select.zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText {
        display: none;
    }
    #zp-ZotpressMetaBox-ShortcodeCreator-Text {
        margin-top: 3px;
        font-size: 9px;
        width: 100%;
    }
    .postbox div.zp-Tab input[type="text"],
    div.zp-Tab select {
        margin-bottom: 10px;
    }
    
    .inner-sidebar .zp-ZotpressMetaBox-Tabs .wide {
        display: none;
    }
    #post-body .zp-ZotpressMetaBox-Tabs .sm {
        display: none;
    }
    .bibOnly {
        display: none;
    }
    div#zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib-Container {
        padding-top: 5px;
    }
    div#zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib-Container input {
        font-size: 9px;
    }
    
</style>

<script>
    jQuery(function()
    {
        
        /*
         *
         *  BASIC FUNCTIONS
         * 
         *
        */
        
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
        
        
        
        /*
         *
         *  ZOTPRESS SHORTCODE CREATOR
         * 
         *
        */
        
        // ZOTPRESS SHORTCODE CREATOR TABS
        jQuery( "#zp-ZotpressMetaBox-ShortcodeCreator" ).tabs();
        
        // FORMAT
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-0-Type").click(function() {
            var value = jQuery(this).val();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (value == "In-Text" && shortcode.search(/\[zotpressInText/i) == -1)
                shortcode = shortcode.replace(/zotpress/i, "zotpressInText");
            else if (value == "Bibliography" && shortcode.search(/\[zotpressInText/i) != -1)
                shortcode = shortcode.replace(/zotpressInText/i, "zotpress");
            
            if (value == "In-Text") {
                jQuery(".inTextOnly").show();
                jQuery(".bibOnly").hide();
            }
            else if (value == "Bibliography") {
                jQuery(".bibOnly").show();
                jQuery(".inTextOnly").hide();
                if (shortcode.search(/pages=/i) != -1)
                    shortcode = shortcode.replace(/ pages=([^\]\s]+)/i, "");
            }
                
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // USERID/NICK
        jQuery(".zp-ZotpressMetaBox-ShortcodeCreator-1-Type").removeAttr('checked');
        jQuery(".zp-ZotpressMetaBox-ShortcodeCreator-1-Type").click(function() {
            var value = jQuery(this).val();
            jQuery(".zp-ZotpressMetaBox-ShortcodeCreator-1-Type[value!='"+value+"']").removeAttr('checked');
            jQuery(".zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText").hide();
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-1-TypeText-"+value).show();
        });
        jQuery(".zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText").click(function() {
            var value = jQuery(this).val();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (jQuery(this).hasClass("UserID")) {
                shortcode = shortcode.replace(/ nickname=([^\]\s]+)/i, "");
                if (shortcode.search(/userid=/i) == -1)
                    shortcode = shortcode.replace(/\]/i, " userid="+value+"]");
                else
                    shortcode = shortcode.replace(/userid=([^\]\s]+)/i, "userid="+value);
            } else if (jQuery(this).hasClass("Nickname")) {
                shortcode = shortcode.replace(/ userid=([^\]\s]+)/i, "");
                if (shortcode.search(/nickname=/i) == -1)
                    shortcode = shortcode.replace(/\]/i, " nickname="+value+"]");
                else
                    shortcode = shortcode.replace(/nickname=([^\]\s]+)/i, "nickname="+value);
            }
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // AUTHOR/YEAR
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-2-Author-Button").click(function() {
            var value = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-2-Author").val().trim();
            if (value != "")
            {
                var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
                if (shortcode.search(/author=/i) == -1)
                    shortcode = shortcode.replace(/\]/i, " author="+value+"]");
                else
                    shortcode = shortcode.replace(/author=([^\]\s]+)/i, "author="+value);
                jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
            }
        });
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-2-Year-Button").click(function() {
            var value = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-2-Year").val().trim();
            if (value != "")
            {
                var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
                if (shortcode.search(/year=/i) == -1)
                    shortcode = shortcode.replace(/\]/i, " year="+value+"]");
                else
                    shortcode = shortcode.replace(/year=([^\]\s]+)/i, "year="+value);
                jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
            }
        });
        
        // DATATYPE
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-3-Datatype").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/datatype=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " datatype="+value+"]");
            else
                shortcode = shortcode.replace(/datatype=([^\]\s]+)/i, "datatype="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // DISPLAY
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-4-Content").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/content=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " content="+value+"]");
            else
                shortcode = shortcode.replace(/content=([^\]\s]+)/i, "content="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-4-Title").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/title=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " title="+value+"]");
            else
                shortcode = shortcode.replace(/title=([^\]\s]+)/i, "title="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-4-Image").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/showimage=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " showimage="+value+"]");
            else
                shortcode = shortcode.replace(/showimage=([^\]\s]+)/i, "showimage="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // STYLE
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Style").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/style=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " style="+value+"]");
            else
                shortcode = shortcode.replace(/style=([^\]\s]+)/i, "style="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // SORT
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-6-SortBy").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/sortby=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " sortby="+value+"]");
            else
                shortcode = shortcode.replace(/sortby=([^\]\s]+)/i, "sortby="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-6-Sort").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/sort=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " sort="+value+"]");
            else
                shortcode = shortcode.replace(/sort=([^\]\s]+)/i, "sort="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // EXTRA
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-7-Download").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/downloadable=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " downloadable="+value+"]");
            else
                shortcode = shortcode.replace(/downloadable=([^\]\s]+)/i, "downloadable="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-7-Notes").click(function() {
            var value = jQuery(this).val().toLowerCase();
            var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
            if (shortcode.search(/notes=/i) == -1)
                shortcode = shortcode.replace(/\]/i, " notes="+value+"]");
            else
                shortcode = shortcode.replace(/notes=([^\]\s]+)/i, "notes="+value);
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
        });
        
        // PAGES
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-8-Pages-Button").click(function() {
            var value = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-8-Pages").val().trim();
            if (value != "")
            {
                var shortcode = jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val();
                if (shortcode.search(/pages=/i) == -1)
                    shortcode = shortcode.replace(/\]/i, " pages="+value+"]");
                else
                    shortcode = shortcode.replace(/pages=([^\]\s]+)/i, "pages="+value);
                jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-Text").val(shortcode);
            }
        });
        
        
        
        /*
         *
         *  ZOTPRESS REFERENCE
         * 
         *
        */
        
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



<!-- START OF ZOTPRESS SHORTCODE CREATOR ------------------------------------------------------------------------->

<h4>Shortcode Creator</h4>

<div id="zp-ZotpressMetaBox-ShortcodeCreator" class="zp-ZotpressMetaBox-Tabs">
    
    <ul>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-0"><span class="wide">Format</span><span class="sm">1</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-1"><span class="wide">Account</span><span class="sm">2</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-2"><span class="wide">Author/Year</span><span class="sm">3</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-3"><span class="wide">Data Type</span><span class="sm">4</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-4"><span class="wide">Display</span><span class="sm">5</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-5"><span class="wide">Style</span><span class="sm">6</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-6"><span class="wide">Sort</span><span class="sm">7</span></a></li>
        <li><a href="#zp-ZotpressMetaBox-ShortcodeCreator-7"><span class="wide">Extra</span><span class="sm">8</span></a></li>
        <li class="inTextOnly"><a href="#zp-ZotpressMetaBox-ShortcodeCreator-8"><span class="wide">Pages</span><span class="sm">9</span></a></li>
    </ul>
    
    <?php global $wpdb; ?>
    
    <!-- START OF TYPE: BIB/INTEXT -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-0" class="zp-Tab">
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-0-Type">Choose Type:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-0-Type">
            <option id="intext" value="In-Text" selected="selected">In-Text</option>
            <option id="bib" value="Bibliography">Bibliography</option>
        </select>
    </div>
    <!-- END OF TYPE: BIB/INTEXT -->
    
    <!-- START OF USERID/NICK -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-1" class="zp-Tab">
        <p class="note">*Only required if you have more than one account.</p>
        <div class="zp-ZotpressMetaBox-RadioButtons">
            <label for="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-UserID">User ID:</label>
            <input id="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-UserID" class="zp-ZotpressMetaBox-ShortcodeCreator-1-Type" type="radio" value="UserID" />
            <label for="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-Nick">Nickname:</label>
            <input id="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-Nick" class="zp-ZotpressMetaBox-ShortcodeCreator-1-Type" type="radio" value="Nickname" />
        </div>
        <?php
        
        $zp_accounts = $wpdb->get_results("SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
        $zp_accounts_total = $wpdb->num_rows;
        
        if ($zp_accounts_total > 0)
        {
            $zp_userids = "";
            $zp_nicks = "";
            foreach ($zp_accounts as $zp_account)
            {
                $zp_userids .= "<option id=\"".$zp_account->api_user_id."\" value=\"".$zp_account->api_user_id."\">".$zp_account->api_user_id."</option>\n";
                $zp_nicks .= "<option id=\"".$zp_account->nickname."\" value=\"".$zp_account->nickname."\">".$zp_account->nickname."</option>\n";
            }
        }
        
        ?>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-1-TypeText-UserID" class="zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText UserID">
            <?php echo $zp_userids; ?>
        </select>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-1-TypeText-Nickname" class="zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText Nickname">
            <?php echo $zp_nicks; ?>
        </select>
    </div>
    <!-- END OF USERID/NICK -->
    
    <!-- START OF AUTHOR/YEAR -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-2" class="zp-Tab">
        <p class="note">Optional. Be sure to replace spaces with a +.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-2-Author">Author:</label>
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Author" type="text" size="20" value="" />
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Author-Button" class="button-secondary" type="button" value="Add" />
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-2-Year">Year:</label>
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Year" type="text" size="20" value="" />
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Year-Button" class="button-secondary" type="button" value="Add" />
    </div>
    <!-- END OF AUTHOR/YEAR -->
    
    <!-- START OF DATATYPE -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-3" class="zp-Tab">
        <p class="note">Optional. Default is "items."</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-3-Datatype">Choose Data Type:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-3-Datatype">
            <option id="Items" value="Items" selected="selected">Items</option>
            <option id="Tags" value="Tags">Tags</option>
            <option id="Collections" value="Collections">Collections</option>
        </select>
    </div>
    <!-- END OF DATATYPE -->
    
    <!-- START OF DISPLAY -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-4" class="zp-Tab">
        <p class="note">Optional. Default is "bib."</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Content">Choose Content:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Content">
            <option id="bib" value="bib" selected="selected">bib</option>
            <option id="html" value="html">html</option>
        </select>
        <p class="note">Optional. Displays title by year.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Title">Show Title?</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Title">
            <option id="no" value="no" selected="selected">no</option>
            <option id="yes" value="yes">yes</option>
        </select>
        <p class="note">Optional. Displays image if exists.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Image">Show Image?</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Image">
            <option id="no" value="no" selected="selected">no</option>
            <option id="yes" value="yes">yes</option>
        </select>
    </div>
    <!-- END OF DISPLAY -->
    
    <!-- START OF STYLE -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-5" class="zp-Tab">
        <p class="note">Optional. Default is "apa."</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">Choose Content:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">
            <?php
            
            $zp_styles = "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, mla, nlm, nature, vancouver";
            $zp_styles = explode(", ", $zp_styles);
            
            foreach($zp_styles as $zp_style)
                if ($zp_style == "apa")
                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" selected='selected'>".$zp_style."</option>\n";
                else
                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
            
            ?>
        </select>
        </select>
    </div>
    <!-- END OF STYLE -->
    
    <!-- START OF SORT -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-6" class="zp-Tab">
        <p class="note">Optional. Default is "latest."</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-6-SortBy">Sort By:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-6-SortBy">
            <option id="latest" value="latest" selected="selected">latest</option>
            <option id="author" value="author">author</option>
            <option id="date" value="date">date</option>
        </select>
        <p class="note">Optional. Default is "desc."</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-6-Sort">Sort By:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-6-Sort">
            <option id="desc" value="desc" selected="selected">desc</option>
            <option id="asc" value="asc">asc</option>
        </select>
    </div>
    <!-- END OF SORT -->
    
    <!-- START OF EXTRA -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-7" class="zp-Tab">
        <p class="note">Optional. Displays download link.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-7-Download">Show Title?</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-7-Download">
            <option id="no" value="no" selected="selected">no</option>
            <option id="yes" value="yes">yes</option>
        </select>
        <p class="note">Optional. Displays note/s if they exist.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-7-Notes">Show Image?</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-7-Notes">
            <option id="no" value="no" selected="selected">no</option>
            <option id="yes" value="yes">yes</option>
        </select>
    </div>
    <!-- END OF DISPLAY -->
    
    <!-- START OF PAGES -->
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-8" class="zp-Tab">
        <p class="note">Optional. Single number or a range, e.g. 3-10.</p>
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-8-Pages">Page/s:</label>
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-8-Pages" type="text" size="20" value="" />
        <input id="zp-ZotpressMetaBox-ShortcodeCreator-8-Pages-Button" class="button-secondary" type="button" value="Add" />
    </div>
    <!-- START OF PAGES -->
    
    <div id="zp-ZotpressMetaBox-ShortcodeCreator-Output">
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-Text"><span class="inTextOnly">In-Text</span><span class="bibOnly">Bibliography</span> Shortcode:</span></label>
        <textarea id="zp-ZotpressMetaBox-ShortcodeCreator-Text">[zotpressInText]</textarea>
        <div id="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib-Container" class="inTextOnly">
            <label for="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib">In-Text Bibliography Shortcode:</span></label>
            <p class="note">Copy-n-paste at the end of your post.</p>
            <input id="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib" type="text" value="[zotpressInTextBib]" />
        </div>
    </div>
    
</div>

<!-- END OF ZOTPRESS SHORTCODE CREATOR ---------------------------------------------------------------------------->



<!-- START OF ZOTPRESS REFERENCE -------------------------------------------------------------------------------------->

<h4>Key Lookup</h4>
<p class="zp-Note">Search for item and collection keys and tag names below. Shortcode parameters: item, collection, tag.</p>

<div id="zp-ZotpressMetaBox-Tabs" class="zp-ZotpressMetaBox-Tabs">
    
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

<!-- END OF ZOTPRESS REFERENCE ------------------------------------------------------------------------------------------>