<style type="text/css">
<!--
    div#zp-Zotpress {
        margin: 1em 0;
    }
    div#zp-Zotpress div.zp-Entry {
        clear: both;
    }
    div#zp-Zotpress div.zp-Entry-Image {
        float: left;
    }
    div#zp-Zotpress div.zp-Entry-Image-Crop {
        overflow: hidden;
        width: 150px;
        height: 150px;
    }
    div#zp-Zotpress div.zp-Entry.zp-Image div.csl-bib-body {
        margin: 0 0 15px 170px;
    }
    div#zp-Zotpress div.csl-bib-body {
        margin: 0 0 15px 0;
    }
    div#zp-Zotpress span.zp-Loading {
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
    div#zp-Zotpress span.zp-Loading span {
        visibility: hidden;
    }
-->
</style>

<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        
        /*
            DISPLAY CITATIONS
        */
        
        function DisplayCitations(account_type, api_user_id, public_key)
        {
            var xmlUriCitations = '<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.rss.php?'
                                        + 'account_type='+account_type+'&api_user_id='+api_user_id+'&public_key='+public_key
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
                                        //alert(xmlUriCitations);
            
            // Grab Zotero request
            jQuery.get(xmlUriCitations, {}, function(xml)
            {
                // AUTHOR
                <?php if ($author !== false) { ?>
                
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
                                    + 'account_type='+account_type+'&api_user_id='+api_user_id+'&public_key='+public_key
                                    + '&limit=<?php echo $limit; ?>'
                                    + '&data_type=<?php echo $data_type; ?>'
                                    + '&collection_id=<?php echo $collection_id; ?>'
                                    + '&item_key='+value
                                    + '&tag_name=<?php echo $tag_name; ?>'
                                    + '&content=<?php echo $content; ?>'
                                    + '&style=<?php echo $style; ?>'
                                    + '&order=<?php echo $order; ?>'
                                    + '&sort=<?php echo $sort; ?>';
                                    //alert(xmlUriAuthorCitations);
                            
                            // Grab Zotero request
                            jQuery.get(xmlUriAuthorCitations, {}, function(xmlAuthorCitations)
                            {
                                citation = "<div class='zp-Entry' rel='"+jQuery(xmlAuthorCitations).find("zapi\\:key").text()+"'>\n"
                                        + jQuery(xmlAuthorCitations).find("content").html()
                                        +"</div>\n\n";
                                jQuery('div#zp-Zotpress').append(citation);
                            });
                        }
                        
                    });
                    
                
                // ALL OTHER CITATIONS
                <? } else { ?>
                
                    <?php if ($data_type == "items") { ?>
                    
                    // Collection Title
                    <?php if ($collection_id !== false && trim($collection_id) != "") { ?>jQuery('div#zp-Zotpress').append("<h3>Citations from the \"<?php echo $collection_id; ?>\" Collection</h3>");<?php } ?>
                    
                    // SINGLE CITATION
                    <?php if ($item_key !== false && trim($item_key) != "") { ?>
                    citation = "<div class='zp-Entry' rel='"+jQuery(xml).find("zapi\\:key").text()+"'>\n"
                            + jQuery(xml).find("content").html()
                            +"</div>\n\n";
                    jQuery('div#zp-Zotpress').append(citation);
                    
                    // MULTIPLE CITATIONS
                    <?php } else { ?>
                    jQuery(xml).find("entry").each(function()
                    {
                        citation = "<div class='zp-Entry' rel='"+jQuery(this).find("zapi\\:key").text()+"'>\n"
                                + jQuery(this).find("content").html()
                                +"</div>\n\n";
                        jQuery('div#zp-Zotpress').append(citation);
                    });
                    <?php } ?>
                    
                    // Citation Images
                    <?php if ($image != "no") { ?>
                    jQuery(xml).find("zpimage").each(function()
                    {
                        jQuery('div.zp-Entry[rel='+jQuery(this).attr('citation_id')+']').addClass("zp-Image").prepend("<div class='zp-Entry-Image' ><div class='zp-Entry-Image-Crop'><img src='"+jQuery(this).attr('image_url')+"' alt='image' /></div></div>\n");
                    });
                    <?php } ?>
                    
                    <?php } else if ($data_type == "tags") { ?>
                    
                    // TAGS
                    jQuery(xml).find("entry").each(function()
                    {
                        tags = "<div class='zp-Entry'>\n"
                                + jQuery(this).find("title").text()
                                +"</div>\n\n";
                        jQuery('div#zp-Zotpress').append(tags);
                    });
                    
                    <?php } else { ?>
                    
                    // COLLECTIONS
                    jQuery(xml).find("entry").each(function()
                    {
                        collections = "<div class='zp-Entry'>\n"
                                + jQuery(this).find("title").text()
                                +"</div>\n\n";
                        jQuery('div#zp-Zotpress').append(collections);
                    });
                    
                    <?php } ?>
                    
                <?php } ?>
                
                jQuery('div#zp-Zotpress span.zp-Loading').remove();
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
                    echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$account->public_key."');";
                }
            }
        }
        
        else {
            foreach ($zp_accounts as $account) {
                echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$account->public_key."');";
            }
        }
        
        ?>
        
    });
</script>