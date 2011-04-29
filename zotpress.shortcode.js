
    jQuery(document).ready(function()
    {
        
        // SET UP CITATIONS AND IMAGES ARRAY
        
        window.zp_citations = new Array();
        window.zp_citation_images = new Array();
        window.zp_citation_output = new Array();
        //window.zp_citation_collection_name = false; // To global or not to global?
        //window.zp_citation_collection_name_counter = 0; // To global or not to global?
        
        
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
        
        
        // MANAGE SINGLE AND MULTIPLE CITATIONS
        
        function zpManageCitations( singleOrMultiple, citationXml, citationLibXml, account_type, api_user_id, data_type, collection_id, style )
        {
            // Get citation id
            var zp_citation_id = citationXml.find("zapi\\:key").text();
            window.zp_citation_id = zp_citation_id; // Necessary?
            
            // Get author(s)
            var zp_author = citationXml.find("zapi\\:creatorSummary").text();
            
            // Get citation content (formatted citation)
            var zp_content = (browser_is_IE) ? citationXml.context.xml.substr(citationXml.context.xml.indexOf("div")-1).substr(0, citationXml.context.xml.substr(citationXml.context.xml.indexOf("div")-1).indexOf("/content")-1) : citationXml.find("content").html();
            
            
            // ADD TO CITATIONS ARRAY
            window.zp_citations[zp_citation_id] =  { "citation_id": zp_citation_id, "author": zp_author, "content": zp_content, "year": false, "image": false, "url": false, "download": false, "output": false };
            
            
            // GET YEAR
            if (singleOrMultiple == "single")
            {
                window.zp_citations[zp_citation_id]["year"] = jQuery(citationLibXml).find("tr.date td").text().replace(/[^0-9]/g, "");
            }
            else // mulitple
            {
                jQuery(citationLibXml).find("zapi\\:key").each(function()
                {
                    if (jQuery(this).text() == zp_citation_id)
                        window.zp_citations[zp_citation_id]["year"] = jQuery(this).parent().find("tr.date td").text().replace(/[^0-9]/g, "");
                });
            }
            
            
            // CITATION URL
            if (singleOrMultiple == "single")
            {
                window.zp_citations[zp_citation_id]["url"] = htmlentities( jQuery(citationLibXml).find("tr.url td").text() );
            }
            else // mulitple
            {
                jQuery(citationLibXml).find("zapi\\:key").each(function()
                {
                    if (jQuery(this).text() == zp_citation_id)
                        window.zp_citations[zp_citation_id]["url"] = htmlentities( jQuery(this).parent().find("tr.url td").text() );
                });
            }
            
            
            // FILE DOWNLOAD URL
            if (parseInt( citationXml.find("zapi\\:numChildren").text() ) > 0)
            {
                var xmlUriChildren = window.ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'+ 'account_type='+account_type+'&api_user_id='+api_user_id+'&children='+zp_citation_id;
                
                jQuery.ajax({
                    url: xmlUriChildren,
                    dataType: "XML",
                    cache: false,
                    async: false,
                    ifModified: false, // Change to true when implemented on Zotero end
                    success: function(xmlChildren, textStatus, jqXHR)
                    {
                        if (browser_is_IE)
                            xmlChildren = createXmlDOMObject (xmlChildren);
                        
                        jQuery(xmlChildren).find("entry").each(function() {
                            if (jQuery(this).find("link[rel='enclosure']").attr("type") == "application/pdf") {
                                window.zp_citations[zp_citation_id]["download"] = htmlentities( jQuery(this).find("link[rel='enclosure']").attr("href") );
                            }
                        });
                    }
                });
            }
            
            
            // Update HTML output if URL was found
            if (window.zp_citations[zp_citation_id]["url"].length > 0)
                zp_content = zp_content.replace(window.zp_citations[zp_citation_id]["url"], "<a href='"+window.zp_citations[zp_citation_id]["url"]+"'>"+window.zp_citations[zp_citation_id]["url"]+"</a>");
            
            // Set up HTML output
            //if (window.zp_citation_collection_name !== false) {
            //    var citation_html = window.zp_citation_collection_name + "<div class='zp-Entry' rel='"+zp_citation_id+"'>\n" + zp_content;
            //    if (window.zp_citation_collection_name_counter < 2) {
            //        window.zp_citation_collection_name_counter++;
            //    }
            //    else {
            //        window.zp_citation_collection_name = false;
            //    }
            //alert(window.zp_citation_collection_name_counter+": "+window.zp_citation_collection_name);
            //alert(citation_html);
            //}
            //else {
                var citation_html = "<div class='zp-Entry' rel='"+zp_citation_id+"'>\n" + zp_content;
            //}
            
            // Update HTML output if DOWNLOAD URL was found
            if (window.zp_citations[zp_citation_id]["download"].length > 0)
                citation_html = citation_html.replace("</div>", "\n<a class='zp-Entry-Download-Link' href='"+window.ZOTPRESS_PLUGIN_URL+"zotpress.rss.file.php?download_url="+escape(window.zp_citations[zp_citation_id]["download"])+"&amp;account_type="+account_type+"&amp;api_user_id="+api_user_id+"'>(Download)</a>\n</div>");
            
            citation_html += "</div>\n\n";
            
            window.zp_citations[zp_citation_id]["output"] = citation_html;
            
        }
        
        
        
        /*
         
            DISPLAY CITATIONS
            
        */
        
        window.zpDisplayCitations = function(
                account_type,
                api_user_id,
                data_type,
                collection_id,
                item_key,
                tag_name,
                content,
                style,
                order,
                sort,
                year,
                download,
                author,
                limit,
                image,
                instance_id)
        {
            
            // DEFAULT PARAMETER VALUES
            
            data_type = typeof(data_type) != 'undefined' ? data_type : "items";
            collection_id = typeof(collection_id) != 'undefined' ? collection_id : false;
            item_key = typeof(item_key) != 'undefined' ? item_key : false;
            tag_name = typeof(tag_name) != 'undefined' ? tag_name : false;
            content = typeof(content) != 'undefined' ? content : "bib";
            style = typeof(style) != 'undefined' ? style : false;
            order = typeof(order) != 'undefined' ? order : false;
            sort = typeof(sort) != 'undefined' ? sort : false;
            year = typeof(year) != 'undefined' ? year : false;
            download = typeof(download) != 'undefined' ? download : false;
            author = typeof(author) != 'undefined' ? author : false;
            limit = typeof(limit) != 'undefined' ? limit : false;
            image = typeof(image) != 'undefined' ? image : false;
            window.zp_instance_id = typeof(instance_id) != 'undefined' ? instance_id : 'zotpress-'+(Math.floor(Math.random()*801)+100); // shouldn't have to generate this, but just in case ...
            
            
            // MAIN ZOTERO REQUEST
            
            xmlUriCitations = window.ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'+ 'account_type='+account_type+'&api_user_id='+api_user_id;
            if (data_type)
                xmlUriCitations += '&data_type='+data_type
            if (collection_id)
                xmlUriCitations += '&collection_id='+collection_id
            if (item_key)
                xmlUriCitations += '&item_key='+item_key
            if (tag_name)
                xmlUriCitations += '&tag_name='+tag_name
            if (content)
                xmlUriCitations += '&content='+content
            if (style)
                xmlUriCitations += '&style='+style
            if (order)
                xmlUriCitations += '&order='+order
            if (sort)
                xmlUriCitations += '&sort='+sort
            if (year)
                xmlUriCitations += '&year='+year
            if (author)
                xmlUriCitations += '&author='+author
            if (download)
                xmlUriCitations += '&download='+download
            if (image)
                xmlUriCitations += '&displayImages='+image
            if (limit)
                xmlUriCitations += '&limit='+limit;
            xmlUriCitations += '&step=one';
            xmlUriCitations +='&instance_id='+window.zp_instance_id;
            
            // Add ajax call to array
            window.zp_ajax_calls[window.zp_ajax_calls.length] = xmlUriCitations;
            //alert(xmlUriCitations);
            
            
            // Grab Zotero request
            jQuery.ajax({
                url: xmlUriCitations,
                dataType: "XML",
                cache: false,
                async: false,
                ifModified: false, // Change to true when implemented on Zotero end
                success: function(xml, textStatus, jqXHR)
                {
                    if (browser_is_IE)
                        xml = createXmlDOMObject (xml);
                    
                    
                    // MANAGE CITATIONS
                    
                    if (data_type == "items")
                    {
                        // Collection Title
                        //if (collection_id !== false && jQuery.trim(collection_id) != "")
                        //{
                        //    collection_name = jQuery(xml).find("title").first().text().replace(jQuery(xml).find("name").first().text(), "").replace("Zotero / ", "").replace("/ Items in Collection ", "");
                        //    if (collection_name !== false && collection_name != false && jQuery.trim(collection_name) != "") {
                        //        window.zp_citation_collection_name = "<h3 id='zp-Collection-Header-"+collection_id+"' class='zp-Collection-Header'>Citations from the "+collection_name+" Collection</h3>";
                        //        //jQuery('div#'+window.zp_instance_id+' div.zp-ZotpressInner').append(collection_name);
                        //    }
                        //}
                        
                        
                        // GRAB LIB VERSION OF QUERY
                        xmlUriLibCitations = xmlUriCitations.replace("&content=bib", "&content=html");
                        
                        jQuery.ajax({
                            url: xmlUriLibCitations,
                            dataType: "XML",
                            cache: false,
                            async: false,
                            ifModified: false, // Change to true when implemented on Zotero end
                            success: function(xmlLib, textStatus, jqXHR)
                            {
                                if (browser_is_IE)
                                    xmlLib = createXmlDOMObject (xmlLib);
                                
                                
                                // GRAB SINGLE AND MULTIPLE CITATIONS
                                
                                if (item_key !== false && jQuery.trim(item_key) != "")
                                {
                                    zpManageCitations( "single", jQuery(xml), jQuery(xmlLib), account_type, api_user_id, data_type, collection_id, style );
                                }
                                else
                                {
                                    jQuery(xml).find("entry").each(function()
                                    {
                                        zpManageCitations( "multple", jQuery(this), jQuery(xmlLib), account_type, api_user_id, data_type, collection_id, style );
                                    });
                                }
                            }
                        });
                        
                        
                        // GRAB IMAGES
                        
                        if (image == "yes")
                        {
                            var xmlUriCitationImages = window.ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?account_type='+account_type+'&api_user_id='+api_user_id
                                    +'&displayImages=true';
                            
                            // Grab Images
                            jQuery.ajax({
                                url: xmlUriCitationImages,
                                dataType: "XML",
                                cache: false,
                                async: false,
                                ifModified: false, // Change to true when implemented on Zotero end
                                success: function(xmlImages, textStatus, jqXHR)
                                {
                                    if (browser_is_IE)
                                        xmlImages = createXmlDOMObject (xmlImages);
                                    
                                    jQuery(xmlImages).find("zpimage").each(function()
                                    {
                                        window.zp_citation_images[jQuery(this).attr('citation_id')] = jQuery(this).attr('image_url');
                                    });
                                }
                            });
                        }
                    }
                    
                    // TAGS
                    else if (data_type == "tags")
                    {
                        var tags = ""
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            tags += "<li class='zp-Entry'>\n"
                                    + jQuery(this).find("title").text()
                                    +"</li>\n\n";
                        });
                        
                        jQuery('div#'+window.zp_instance_id+' div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+tags+"</ul>\n\n");
                    }
                    
                    // COLLECTIONS
                    else
                    {
                        var collections = "";
                        jQuery(xml).find("entry").each(function()
                        {
                            collections += "<li class='zp-Entry'>\n"
                                    + jQuery(this).find("title").text()
                                    +"</li>\n\n";
                        });
                        
                        jQuery('div#'+window.zp_instance_id+' div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+collections+"</ul>\n\n");
                    }
                },
                
                statusCode: {
                    304: function() {
                    //alert('The page has been updated');
                }},
                
                error: function(jqXHR, textStatus, errorThrown) {
                    //alert("Error: "+errorThrown);
                },
                
                complete: function() {
                    
                    // Append citations
                    for (key in window.zp_citations)
                    {
                        // Filter by author and year
                        if (window.zp_citations[key]["author"].length > 0 || window.zp_citations[key]["year"].length > 0)
                        {
                            // Both
                            if (window.zp_citations[key]["author"].indexOf(author) == -1 && window.zp_citations[key]["year"].indexOf(year) == -1) {
                                continue;
                            }
                            // Just author
                            else if (window.zp_citations[key]["author"].indexOf(author) != -1 && window.zp_citations[key]["year"].indexOf(year) == -1) {
                                continue;
                            }
                            // Just year
                            else if (window.zp_citations[key]["author"].indexOf(author) == -1 && window.zp_citations[key]["year"].indexOf(year) != -1) {
                                continue;
                            }
                        }
                        
                        // Add images to citations
                        for (img_key in window.zp_citation_images) {
                            if (key == img_key) {
                                window.zp_citations[key]["output"] = jQuery.trim( window.zp_citations[key]["output"] );
                                window.zp_citations[key]["output"] = window.zp_citations[key]["output"].replace("zp-Entry", "zp-Entry zp-Image");
                                window.zp_citations[key]["output"] = window.zp_citations[key]["output"].substring(0, window.zp_citations[key]["output"].length-6);
                                window.zp_citations[key]["output"] += "<div class='zp-Entry-Image' ><div class='zp-Entry-Image-Crop'><img src='"+window.zp_citation_images[img_key]+"' alt='image' /></div></div>\n</div>\n";
                            }
                        }
                        
                        // Display citation output
                        jQuery('div#'+window.window.zp_instance_id+' div.zp-ZotpressInner').append(window.zp_citations[key]["output"]);
                        
                        // Add citation output to database
                        jQuery.ajax({
                            url: window.ZOTPRESS_PLUGIN_URL+'zotpress.rss.update.php?instance_id='+window.zp_instance_id+'&output='+escape( window.zp_citations[key]["output"] ),
                            dataType: "XML",
                            cache: false,
                            async: true,
                            ifModified: false
                        });
                    }
                    
                    // Clear citations array -- should I clear the images array, too?
                    window.zp_citations = new Array();
                    window.zp_citation_images = new Array();
                    
                    // Display citations
                    jQuery('div#'+window.zp_instance_id+' span.zp-Loading').fadeOut("fast");
                    jQuery('div#'+window.zp_instance_id+' div.zp-ZotpressInner').slideDown("slow");
                    
                } // complete
                
            }); // ajax
            
        } // window.zpDisplayCitations
        
        
    });