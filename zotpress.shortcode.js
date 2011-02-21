
    jQuery(document).ready(function()
    {
        
        // DETERMINE BROWSER
        
        var browser_is_IE = false;
        
        jQuery.each(jQuery.browser, function() {
            if (jQuery.browser.msie)
                browser_is_IE = true;
        });
        
        
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
        
        
        /*
            DISPLAY CITATIONS WITH DOWNLOADS
        */
        
        window.DisplayCitationsWithDownloads = function(account_type, api_user_id, data_type, collection_id, item_key, tag_name, content, style, order, sort, year, download, author, limit, image, instance_id, ZOTPRESS_PLUGIN_URL)
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
            download = true;
            author = typeof(author) != 'undefined' ? author : false;
            limit = typeof(limit) != 'undefined' ? limit : false;
            image = typeof(image) != 'undefined' ? image : false;
            instance_id = typeof(instance_id) != 'undefined' ? instance_id : 'zotpress-'+(Math.floor(Math.random()*801)+100);
            
            
            // DOWNLOAD URL
            
            var citation_downloads = new Array();
            
            var xmlUriCitationDownloads = ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'
                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                        + '&data_type='+data_type
                        + '&collection_id='+collection_id
                        + '&item_key='
                        + '&tag_name='+tag_name
                        + '&content=html'
                        + '&style='+style
                        + '&order='+order
                        + '&sort='+sort
                        + '&year='+year
                        + '&download='+download
                        + '&author='+author;
            
            // Grab attachments
            jQuery.ajax({
                url: xmlUriCitationDownloads,
                dataType: "XML",
                cache: true,
                ifModified: true,
                success: function(xmlDownloads, textStatus, jqXHR)
                {
                    if (browser_is_IE)
                    {
                        xmlDownloads = createXmlDOMObject (xmlDownloads);
                    }
                    
                    jQuery(xmlDownloads).find("entry").each(function ()
                    {
                        // Find type "attachment" and  type "application/pdf"
                        if (jQuery(this).find("tr.url td").text().length > 0
                                && jQuery(this).find("zapi\\:itemType").text() == "attachment"
                                && jQuery(this).find("link[type='application/pdf']").attr("href") != undefined)
                        {
                            var temp_url = jQuery(this).find("id").text().replace("http","https")+"/file";
                            var temp_attachment_id = jQuery(this).find("zapi\\:key").text();
                            var temp_citation_id = jQuery(this).find("link[rel='up']").attr("href").split("/");
                            temp_citation_id = temp_citation_id[temp_citation_id.length-1];
                            
                            citation_downloads[temp_citation_id] = { 'attachment_id': temp_attachment_id, 'attachment_url': temp_url };
                        }
                    });
                    
                },
                    
                complete: function() {
                    DisplayCitations(account_type, api_user_id, data_type, collection_id, item_key, tag_name, content, style, order, sort, year, download, author, limit, image, instance_id, citation_downloads, ZOTPRESS_PLUGIN_URL);
                }
            });
        }
            
            
        /*
            DISPLAY CITATIONS
        */
        
        window.DisplayCitations = function(account_type, api_user_id, data_type, collection_id, item_key, tag_name, content, style, order, sort, year, download, author, limit, image, instance_id, citation_downloads, ZOTPRESS_PLUGIN_URL)
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
            instance_id = typeof(instance_id) != 'undefined' ? instance_id : 'zotpress-'+(Math.floor(Math.random()*801)+100);
            
            
            // MAIN ZOTERO REQUEST
            
            var xmlUriCitations = ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'
                                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                                        + '&limit='+limit
                                        + '&data_type='+data_type
                                        + '&collection_id='+collection_id
                                        + '&item_key='+item_key
                                        + '&tag_name='+tag_name
                                        + '&content='+content
                                        + '&style='+style
                                        + '&order='+order
                                        + '&sort='+sort
                                        + '&year='+year
                                        + '&download='+download
                                        + '&author='+author;
            
            // Grab Zotero request
            jQuery.ajax({
                url: xmlUriCitations,
                dataType: "XML",
                cache: true,
                ifModified: true,
                success: function(xml, textStatus, jqXHR)
                {
                    if (browser_is_IE)
                    {
                        xml = createXmlDOMObject (xml);
                    }
                    
                    
                    
                    // AUTHOR & YEAR
                    
                    if (author != false || year != false)
                    {
                        var citations_array = new Array();
                        
                        jQuery(xml).find("entry").each(function ()
                        {
                            var citation_match = false;
                            var this_citation_id = jQuery(this).find("zapi\\:key").text();
                            
                            // AUTHOR
                            if (author != false) {
                                if (jQuery(this).find("tr.creator td").text().indexOf(author.replace(" ", "+")) != -1) {
                                    citation_match = true;
                                }
                            }
                            
                            // YEAR
                            if (year != false && author != false) {
                                if (citation_match) {
                                    if (jQuery(this).find("tr.date td").text().indexOf(year) == -1) {
                                        citation_match = false;
                                    }
                                }
                            }
                            else if (year != false && author == false) {
                                if (jQuery(this).find("tr.date td").text().indexOf(year) != -1)
                                    citation_match = true;
                            }
                            
                            // Add to array if match
                            if (citations_array.length < limit) {
                                if (citation_match) {
                                    citations_array[citations_array.length] = this_citation_id;
                                }
                            }
                            
                        });
                        
                        if (citations_array.length > 0)
                        {
                            jQuery.each(citations_array, function(index, value)
                            {
                                if (value.length > 0)
                                {
                                    var xmlUriAuthorCitations = ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'
                                            + 'account_type='+account_type+'&api_user_id='+api_user_id
                                            + '&limit='+limit
                                            + '&data_type='+data_type
                                            + '&collection_id='+collection_id
                                            + '&item_key='+value
                                            + '&tag_name='+tag_name
                                            + '&content='+content
                                            + '&style='+style
                                            + '&order='+order
                                            + '&sort='+sort;
                                    
                                    // Grab Zotero request
                                    jQuery.get(xmlUriAuthorCitations, {}, function(xmlAuthorCitations)
                                    {
                                        if (browser_is_IE)
                                        {
                                            xmlAuthorCitations = createXmlDOMObject (xmlAuthorCitations);
                                        }
                                        
                                        // CONTENT
                                        var zpcontent = (browser_is_IE) ? jQuery(xmlAuthorCitations).context.xml.substr(jQuery(xmlAuthorCitations).context.xml.indexOf("div")-1).substr(0, jQuery(xmlAuthorCitations).context.xml.substr(jQuery(xmlAuthorCitations).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(xmlAuthorCitations).find("content").html();
                                        
                                        var this_citation_id = (jQuery(xmlAuthorCitations).find("zapi\\:key").text());
                                        
                                        var citation_html = "<div class='zp-Entry' rel='"+this_citation_id+"'>\n" + zpcontent;
                                        
                                        if (download === true) {
                                            if (citation_downloads[this_citation_id] !== undefined) {
                                                citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                            }
                                        }
                                        
                                        citation_html += "</div>\n\n";
                                        
                                        jQuery('div#'+instance_id+' div.zp-ZotpressInner').append(citation_html);
                                        
                                    }, "XML");
                                }
                            });
                            
                            
                            // IMAGES
                            
                            if (image == "yes")
                            {
                                var xmlUriCitationImages = ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'
                                                                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                                                                        +'&displayImages=true';
                                
                                // Grab Images
                                jQuery.get(xmlUriCitationImages, {}, function(xmlImages)
                                {
                                    if (browser_is_IE)
                                    {
                                        xmlImages = createXmlDOMObject (xmlImages);
                                    }
                                    
                                    jQuery(xmlImages).find("zpimage").each(function()
                                    {
                                        var zpimage = jQuery(this);
                                        
                                        if (jQuery('div.zp-Entry[rel='+jQuery(this).attr('citation_id')+']').hasClass("zp-Image") === false)
                                            jQuery('div.zp-Entry[rel='+jQuery(this).attr('citation_id')+']').addClass("zp-Image").prepend("<div class='zp-Entry-Image' ><div class='zp-Entry-Image-Crop'><img src='"+jQuery(this).attr('image_url')+"' alt='image' /></div></div>\n");
                                        
                                    });
                                    
                                }, "XML");
                            }
                        }
                        
                        else // No citations
                        {
                            jQuery('div#'+instance_id+' div.zp-ZotpressInner').append("<p class='zp-NoCitations'>No citations found.</p>\n");
                        }
                    }
                    
                    
                    // ALL OTHER CITATIONS
                    
                    else
                    {
                        if (data_type == "items")
                        {
                            // Collection Title
                            if (collection_id !== false && jQuery.trim(collection_id) != "") {
                                jQuery('div#'+instance_id+' div.zp-ZotpressInner').append("<h3>Citations from the \""+collection_id+"\" Collection</h3>");
                            }
                            
                            
                            // SINGLE CITATION
                            
                            if (item_key !== false && jQuery.trim(item_key) != "")
                            {
                                var zpcontent = (browser_is_IE) ? jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).substr(0, jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(xml).find("content").html();
                                var this_citation_id = jQuery(xml).find("zapi\\:key").text();
                                
                                if (this_citation_id != "")
                                {
                                    var citation_html = "<div class='zp-Entry' rel='"+this_citation_id+"'>\n" + zpcontent;
                                    
                                    if (download === true) {
                                        if (citation_downloads[this_citation_id] !== undefined) {
                                            citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                        }
                                    }
                                        
                                    citation_html += "</div>\n\n";
                                    
                                    jQuery('div#'+instance_id+' div.zp-ZotpressInner').append(citation_html);
                                }
                            }
                            
                            
                            
                            // MULTIPLE CITATIONS
                            
                            else
                            {
                                jQuery(xml).find("entry").each(function()
                                {
                                    var this_citation_id = jQuery(this).find("zapi\\:key").text();
                                    
                                    var zpcontent = (browser_is_IE) ? jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).substr(0, jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(this).find("content").html();
                                    
                                    var citation_html = "<div class='zp-Entry' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n" + zpcontent;
                                    
                                    if (download === true) {
                                        if (citation_downloads[this_citation_id] !== undefined) {
                                            citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                        }
                                    }
                                    
                                    citation_html += "</div>\n\n";
                                    
                                    jQuery('div#'+instance_id+' div.zp-ZotpressInner').append(citation_html);
                                });
                            }
                            
                            
                            // IMAGES
                            
                            if (image == "yes")
                            {
                                var xmlUriCitationImages = ZOTPRESS_PLUGIN_URL+'zotpress.rss.php?'
                                                                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                                                                        +'&displayImages=true';
                                
                                // Grab Images
                                jQuery.get(xmlUriCitationImages, {}, function(xmlImages)
                                {
                                    if (browser_is_IE)
                                    {
                                        xmlImages = createXmlDOMObject (xmlImages);
                                    }
                                    
                                    jQuery(xmlImages).find("zpimage").each(function()
                                    {
                                        var zpimage = jQuery(this);
                                        
                                        jQuery('div.zp-Entry[rel='+jQuery(this).attr('citation_id')+']').addClass("zp-Image").prepend("<div class='zp-Entry-Image' ><div class='zp-Entry-Image-Crop'><img src='"+jQuery(this).attr('image_url')+"' alt='image' /></div></div>\n");
                                        
                                    });
                                    
                                }, "XML");
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
                            
                            jQuery('div#'+instance_id+' div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+tags+"</ul>\n\n");
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
                            
                            jQuery('div#'+instance_id+' div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+collections+"</ul>\n\n");
                            
                        }
                    }
                    
                },
                
                statusCode: {304: function() {
                    //alert('The page has been updated');
                }},
                
                error: function(jqXHR, textStatus, errorThrown) {
                    //alert("Error: "+errorThrown);
                },
                
                complete: function() {
                    jQuery('div#'+instance_id+' span.zp-Loading').fadeOut("fast");
                    jQuery('div#'+instance_id+' div.zp-ZotpressInner').slideDown("slow");
                }
                
            });
        }
        
    });