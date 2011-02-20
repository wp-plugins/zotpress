<style type="text/css">
<!--
    div.zp-Zotpress {
        margin: 1em 0;
    }
    div.zp-ZotpressInner {
        display: none;
    }
    div.zp-Zotpress div.zp-Entry {
        clear: both;
    }
    div.zp-Zotpress div.zp-Entry-Image {
        float: left;
    }
    div.zp-Zotpress div.zp-Entry-Image-Crop {
        overflow: hidden;
        width: 150px;
        height: 150px;
    }
    div.zp-Zotpress div.zp-Entry.zp-Image div.csl-bib-body {
        margin: 0 0 15px 170px;
    }
    div.zp-Zotpress div.csl-bib-body {
        margin: 0 0 15px 0;
    }
    div.zp-Zotpress span.zp-Loading {
        border: 1px solid #ddd;
        border-radius: 5px;
        -moz-border-radius: 5px;
        background: #f3f3f3 url('<?php echo ZOTPRESS_PLUGIN_URL; ?>loading_list.gif') no-repeat top left;
        display: block;
        margin: auto;
        overflow: hidden;
        width: 33px;
        height: 32px;
    }
    div.zp-Zotpress span.zp-Loading span {
        visibility: hidden;
    }
    div.zp-Zotpress p.zp-NoCitations {
        margin: 0;
    }
-->
</style>

<script type="text/javascript">
    
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
            DISPLAY CITATIONS
        */
        
        function DisplayCitations(account_type, api_user_id)
        {
            
            // DOWNLOAD URL
            <?php if ($download == "yes") { ?>
            
            var citation_downloads = new Array();
            
            var xmlUriCitationDownloads = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                        + '&data_type=<?php echo $data_type; ?>'
                        + '&collection_id=<?php echo $collection_id; ?>'
                        + '&item_key=<?php echo $item_key; ?>'
                        + '&tag_name=<?php echo $tag_name; ?>'
                        + '&content=html'
                        + '&style=<?php echo $style; ?>'
                        + '&order=<?php echo $order; ?>'
                        + '&sort=<?php echo $sort; ?>'
                        + '&year=<?php echo $year; ?>'
                        + '&download=<?php echo $download; ?>'
                        + '&author=<?php echo $author; ?>';
            
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
                        // Find type "attachment"
                        if (jQuery(this).find("tr.url td").text().length > 0 && jQuery(this).find("zapi\\:itemType").text() == "attachment")
                        {
                            var temp_url = jQuery(this).find("tr.url td").text();
                            var temp_attachment_id = jQuery(this).find("zapi\\:key").text();
                            var temp_citation_id = jQuery(this).find("link[rel='up']").attr("href").split("/");
                            temp_citation_id = temp_citation_id[temp_citation_id.length-1];
                            
                            citation_downloads[temp_citation_id] = { 'attachment_id': temp_attachment_id, 'attachment_url': temp_url };
                        }
                    });
                    
                },
                    
                complete: function() {
            
            
            // MAIN ZOTERO REQUEST
            <?php } ?>
            
            var xmlUriCitations = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                        + 'account_type='+account_type+'&api_user_id='+api_user_id
                                        + '&limit=<?php echo $limit; ?>'
                                        + '&data_type=<?php echo $data_type; ?>'
                                        + '&collection_id=<?php echo $collection_id; ?>'
                                        + '&item_key=<?php echo $item_key; ?>'
                                        + '&tag_name=<?php echo $tag_name; ?>'
                                        + '&content=<?php echo $content; ?>'
                                        + '&style=<?php echo $style; ?>'
                                        + '&order=<?php echo $order; ?>'
                                        + '&sort=<?php echo $sort; ?>'
                                        + '&year=<?php echo $year; ?>'
                                        + '&download=<?php echo $download; ?>'
                                        + '&author=<?php echo $author; ?>';
            
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
                    <?php if ($author != false || $year !=false) { ?>
                    
                        var citations_array = new Array();
                        
                        jQuery(xml).find("entry").each(function ()
                        {
                            var citation_match = false;
                            var this_citation_id = jQuery(this).find("zapi\\:key").text();
                            
                            // AUTHOR
                            <?php if ($author != false) { ?>
                            if (jQuery(this).find("tr.creator td").text().indexOf("<?php echo str_replace("+"," ",$author); ?>") != -1) {
                                citation_match = true;
                            }
                            <?php } ?>
                            
                            // YEAR
                            <?php if ($year != false && $author != false) { ?>
                            if (citation_match) {
                                if (jQuery(this).find("tr.date td").text().indexOf("<?php echo $year; ?>") == -1) {
                                    citation_match = false;
                                }
                            }
                            <?php } else if ($year != false && $author == false) { ?>
                            if (jQuery(this).find("tr.date td").text().indexOf("<?php echo $year; ?>") != -1)
                                citation_match = true;
                            <?php } ?>
                            
                            // Add to array if match
                            if (citations_array.length < <?php echo $limit; ?>) {
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
                                    var xmlUriAuthorCitations = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                            + 'account_type='+account_type+'&api_user_id='+api_user_id
                                            + '&limit=<?php echo $limit; ?>'
                                            + '&data_type=<?php echo $data_type; ?>'
                                            + '&collection_id=<?php echo $collection_id; ?>'
                                            + '&item_key='+value
                                            + '&tag_name=<?php echo $tag_name; ?>'
                                            + '&content=<?php echo $content; ?>'
                                            + '&style=<?php echo $style; ?>'
                                            + '&order=<?php echo $order; ?>'
                                            + '&sort=<?php echo $sort; ?>';
                                            
                                    
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
                                        
                                        <?php if ($download == "yes") { ?>
                                        if (citation_downloads[this_citation_id] !== undefined) {
                                            citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                        }
                                        <?php } ?>
                                        
                                        citation_html += "</div>\n\n";
                                        
                                        jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation_html);
                                        
                                    }, "XML");
                                }
                            });
                        }
                        
                        else // No citations
                        {
                            jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append("<p class='zp-NoCitations'>No citations found.</p>\n");
                        }
                    
                    
                    
                    // ALL OTHER CITATIONS
                    <?php } else { ?>
                    
                        <?php if ($data_type == "items") { ?>
                            
                            // Collection Title
                            <?php if ($collection_id !== false && trim($collection_id) != "") { ?>jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append("<h3>Citations from the \"<?php echo $collection_id; ?>\" Collection</h3>");<?php } ?>
                            
                            
                            
                                // SINGLE CITATION
                                <?php if ($item_key !== false && trim($item_key) != "") { ?>
                                var zpcontent = (browser_is_IE) ? jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).substr(0, jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(xml).find("content").html();
                                var this_citation_id = jQuery(xml).find("zapi\\:key").text();
                                
                                if (this_citation_id != "")
                                {
                                    var citation_html = "<div class='zp-Entry' rel='"+this_citation_id+"'>\n" + zpcontent;
                                    
                                    <?php if ($download == "yes") { ?>
                                    if (citation_downloads[this_citation_id] !== undefined) { // 5KWRUASC
                                        citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                    }
                                    <?php } ?>
                                        
                                    citation_html += "</div>\n\n";
                                    
                                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation_html);
                                }
                                
                                
                                
                                // MULTIPLE CITATIONS
                                <?php } else { ?>
                                jQuery(xml).find("entry").each(function()
                                {
                                    var this_citation_id = jQuery(this).find("zapi\\:key").text();
                                    
                                    var zpcontent = (browser_is_IE) ? jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).substr(0, jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(this).find("content").html();
                                    
                                    var citation_html = "<div class='zp-Entry' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n" + zpcontent;
                                    
                                    <?php if ($download == "yes") { ?>
                                    if (citation_downloads[this_citation_id] !== undefined) { // 5KWRUASC
                                        citation_html += "<a href='"+citation_downloads[this_citation_id].attachment_url+"'>Download URL</a>\n";
                                    }
                                    <?php } ?>
                                    
                                    citation_html += "</div>\n\n";
                                    
                                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation_html);
                                });
                            <?php } ?>
                            
                            
                            // IMAGES
                            <?php if ($image == "yes") { ?>
                            var xmlUriCitationImages = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
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
                            
                            <?php } ?>
                            
                        <?php } else if ($data_type == "tags") { ?>
                        
                        
                        // TAGS
                        
                        var tags = ""
                        
                        jQuery(xml).find("entry").each(function()
                        {
                            tags += "<li class='zp-Entry'>\n"
                                    + jQuery(this).find("title").text()
                                    +"</li>\n\n";
                        });
                        
                        jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+tags+"</ul>\n\n");
                        
                        <?php } else { ?>
                        
                        
                        // COLLECTIONS
                        
                        var collections = "";
                        jQuery(xml).find("entry").each(function()
                        {
                            collections += "<li class='zp-Entry'>\n"
                                    + jQuery(this).find("title").text()
                                    +"</li>\n\n";
                        });
                        
                        jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append("<ul class='zp-Entries'>\n"+collections+"</ul>\n\n");
                        
                        <?php } ?>
                        
                    <?php } ?>
                    
                },
                
                statusCode: {304: function() {
                    alert('The page has been updated');
                }},
                
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error: "+errorThrown);
                },
                
                complete: function() {
                    jQuery('div#<?php echo $zp_instance_id; ?> span.zp-Loading').fadeOut("fast");
                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').slideDown("slow");
                }
                
            });
            
            <?php if ($download == "yes") { ?>
                }
            });
            <?php } ?>
        }
        
        <?php
        
        $zp_multiple = false;
        
        if ($api_user_id !== false || $nickname !== false)
            $zp_multiple = true;
            
        if ($zp_multiple === false) {
            foreach ($zp_accounts as $account) {
                if (($api_user_id !== false && $api_user_id == $account->api_user_id)
                        || ($nickname !== false && $nickname == $account->nickname)) {
                    echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."');";
                }
            }
        }
        
        else {
            foreach ($zp_accounts as $account) {
                echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."');";
            }
        }
        
        ?>
        
    });
</script>