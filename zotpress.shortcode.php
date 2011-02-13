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
                                        + '&author=<?php echo $author; ?>';
            
            // Grab Zotero request
            //jQuery.get(xmlUriCitations, {}, function(xml)
            jQuery.ajax({
                url: xmlUriCitations,
                dataType: "XML",
                cache: true,
                ifModified: true,
                success: function(xml, textStatus, jqXHR) {
                        
                    if (browser_is_IE)
                    {
                        xml = createXmlDOMObject (xml);
                    }
                    
                    // AUTHOR
                    <?php if ($author != false) { ?>
                    
                        var authors = "";
                        
                        jQuery(xml).find("entry").each(function ()
                        {
                                if (jQuery(this).find("tr.creator td").text().indexOf("<?php echo str_replace("+"," ",$author); ?>") != -1)
                                        authors += jQuery(this).find("zapi\\:key").text()+",";
                        });
                        
                        authors = authors.split(",");
                        
                        jQuery.each(authors, function(index, value)
                        {
                            if (value.length >0)
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
                                    
                                    var zpcontent = (browser_is_IE) ? jQuery(xmlAuthorCitations).context.xml.substr(jQuery(xmlAuthorCitations).context.xml.indexOf("div")-1).substr(0, jQuery(xmlAuthorCitations).context.xml.substr(jQuery(xmlAuthorCitations).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(xmlAuthorCitations).find("content").html();
                                    //var zpcontent = (browser_is_IE) ? jQuery(jQuery(xmlAuthorCitations).context.xml).find("content").html() : jQuery(xmlAuthorCitations).find("content").html();
                                    //alert(zpcontent);
                                    
                                    citation = "<div class='zp-Entry' rel='"+jQuery(xmlAuthorCitations).find("zapi\\:key").text()+"'>\n"
                                            + zpcontent
                                            +"</div>\n\n";
                                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation);
                                    
                                }, "XML");
                            }
                            
                        });
                        
                    
                    // ALL OTHER CITATIONS
                    <?php } else { ?>
                    
                        <?php if ($data_type == "items") { ?>
                            
                            // Collection Title
                            <?php if ($collection_id !== false && trim($collection_id) != "") { ?>jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append("<h3>Citations from the \"<?php echo $collection_id; ?>\" Collection</h3>");<?php } ?>
                            
                                // SINGLE CITATION
                                <?php if ($item_key !== false && trim($item_key) != "") { ?>
                                var zpcontent = (browser_is_IE) ? jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).substr(0, jQuery(xml).context.xml.substr(jQuery(xml).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(xml).find("content").html();
                                //var zpcontent = (browser_is_IE) ? jQuery(jQuery(xml).context.xml).find("content").html() : jQuery(xml).find("content").html();
                                
                                citation = "<div class='zp-Entry' rel='"+jQuery(xml).find("zapi\\:key").text()+"'>\n"
                                        + zpcontent
                                        +"</div>\n\n";
                                jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation);
                                
                                // MULTIPLE CITATIONS
                                <?php } else { ?>
                                jQuery(xml).find("entry").each(function()
                                {
                                    var zpcontent = (browser_is_IE) ? jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).substr(0, jQuery(this).context.xml.substr(jQuery(this).context.xml.indexOf("div")-1).indexOf("/content")-1) : jQuery(this).find("content").html();
                                    //var zpcontent = (browser_is_IE) ? jQuery(jQuery(this).context.xml).find("content").html() : jQuery(this).find("content").html();
                                    
                                    citation = "<div class='zp-Entry' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n"
                                            + zpcontent
                                            +"</div>\n\n";
                                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').append(citation);
                                });
                            <?php } ?>
                            
                            // Citation Images
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
                    jQuery('div#<?php echo $zp_instance_id; ?> span.zp-Loading').remove();
                    jQuery('div#<?php echo $zp_instance_id; ?> div.zp-ZotpressInner').slideDown("slow");
                }
                
            });
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