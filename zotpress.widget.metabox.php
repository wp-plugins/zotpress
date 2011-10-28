
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
        // Thanks to http://www.latentmotion.com/how-to-sort-an-associative-array-object-in-javascript/
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
        jQuery("#zp-ZotpressMetaBox-ShortcodeCreator").tabs();
        
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
        
        // DEFAULT STYLE BUTTON - IN PROGRESS
        //jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Default").click(function() {
        //    
        //    jQuery("#editorcontainer iframe").contents().find("p").each(function() {
        //        if (jQuery(this).text().search("zotpressInTextBib style") == -1) {
        //            var content = jQuery(this).text().replace("zotpressInTextBib", "zotpressInTextBib style="+jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Style").val());
        //            jQuery(this).text(content);
        //        }
        //        if (jQuery(this).text().search("zotpress") > -1 && jQuery(this).text().search("style") == -1) {
        //            var content = jQuery(this).text().replace("zotpress", "zotpress style="+jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Style").val());
        //            jQuery(this).text(content);
        //        }
        //    });
        //});
        
        
        
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
        <?php
        
        // Default style, per post or overall
        $zp_default_style = "apa";
        if (get_option("Zotpress_DefaultStyle_". get_the_ID()))
            $zp_default_style = get_option("Zotpress_DefaultStyle_". get_the_ID());
        else
            if (get_option("Zotpress_DefaultStyle"))
                $zp_default_style = get_option("Zotpress_DefaultStyle");
                
        ?>
        <p class="note">Optional. Default is "<?php echo $zp_default_style; ?>."</p>
        
        <label for="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">Choose Style:</label>
        <select id="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">
            <?php
            
            $zp_styles = "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, mla, nlm, nature, vancouver";
            $zp_styles = explode(", ", $zp_styles);
            
            foreach($zp_styles as $zp_style)
                if ($zp_style == $zp_default_style)
                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" selected='selected'>".$zp_style."</option>\n";
                else
                    echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
            
            ?>
        </select>
        
        <script type="text/javascript" >
        jQuery(document).ready(function() {
        
            jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button").click(function()
            {
                // Plunk it together
                var data = 'submit=true&style=' + jQuery('#zp-ZotpressMetaBox-ShortcodeCreator-5-Style').val() + '&forpost=true&post=<?php the_ID(); ?>';
                
                // Prep for validation
                jQuery('input#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button').attr('disabled','true');
                jQuery('.zp-Loading').show();
                
                // Set up uri
                var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>/zotpress.widget.metabox.actions.php?'+data;
                
                // AJAX
                jQuery.get(xmlUri, {}, function(xml)
                {
                    var $result = jQuery('result', xml).attr('success');
                    
                    jQuery('.zp-Loading').hide();
                    jQuery('input#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button').removeAttr('disabled');
                    
                    if ($result == "true")
                    {
                        jQuery('div.zp-Errors').hide();
                        jQuery('div.zp-Success').show();
                        
                        jQuery.doTimeout(1000,function() {
                            jQuery('div.zp-Success').hide();
                        });
                    }
                    else // Show errors
                    {
                        jQuery('div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                        jQuery('div.zp-Errors').show();
                    }
                });
                
                // Cancel default behaviours
                return false;
                
            });
            
        });
        </script>
        
        <!--<form id="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Form" action="<?php //echo $PHP_SELF;?>" method="post">-->
            <label for="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button">Set Style as Post Default:</label>
            <input type="button" id="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button" class="button-secondary" value="Set Default Style" />
            <div class="zp-Loading">loading</div>
            <div class="zp-Success">Success!</div>
            <div class="zp-Errors">Errors!</div>
        <!--</form>-->
        
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