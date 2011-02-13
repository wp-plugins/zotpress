    <?php if ($zp_accounts_total > 0) { ?>
        
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.tabs.php'); ?>
            
            <div id="zp-Filter">
                <div id="zp-FilterInner">
                
                    <div class="section first">
                        <label for="zp-FilterByAccount">Choose Account:</label>
                        
                        <select id="zp-FilterByAccount">
                            <?php
                            
                            // ACCOUNT DEFAULTS
                            
                            $account_type = $zp_accounts[0]->account_type;
                            $api_user_id = $zp_accounts[0]->api_user_id;
                            $public_key = $zp_accounts[0]->public_key;
                            $nickname = $zp_accounts[0]->nickname;
                            
                            // DISPLAY ACCOUNTS
                            
                            foreach ($zp_accounts as $zp_account)
                            {
                                
                                // DETERMINE CURRENTLY ACTIVE ACCOUNT
                                
                                if ($account_id && $account_id == $zp_account->id) {
                                    $account_type = $zp_account->account_type;
                                    $api_user_id = $zp_account->api_user_id;
                                    $public_key = $zp_account->public_key;
                                    $nickname = $zp_account->nickname;
                                }
                                
                                // DISPLAY ACCOUNTS IN DROPDOWN
                                
                                if ($zp_account->nickname) {
                                    if ($account_id && $account_id == $zp_account->id)
                                        echo "<option selected='selected' value='".$zp_account->id."'>".$zp_account->nickname."</option>\n";
                                    else
                                        echo "<option value='".$zp_account->id."'>".$zp_account->nickname."</option>\n";
                                }
                                else {
                                    if ($account_id && $account_id == $zp_account->id)
                                        echo "<option selected='selected' value='".$zp_account->id."'>".$zp_account->api_user_id."</option>\n";
                                    else
                                        echo "<option value='".$zp_account->id."'>".$zp_account->api_user_id."</option>\n";
                                }
                            }
                            
                            ?>
                        </select>
                    </div>
                    
                    <div id="zp-FilterByCollection-Section" class="section">
                        <label for="zp-FilterByCollection">Sort by Collection:</label>
                        
                        <select id="zp-FilterByCollection">
                            <option selected='selected' value=''>Select a collection</option>
                            <option value=''>-------------------</option>
                            <?php
                            
                            // READ ZOTERO XML FOR COLLECTIONS
                            
                            $doc_collections = new DOMDocument();
                            $doc_collections->load(ZOTPRESS_PLUGIN_URL."zotpress.rss.php?account_type=".$account_type."&api_user_id=".$api_user_id."&public_key=".$public_key."&data_type=collections");
                            
                            $entries = $doc_collections->getElementsByTagName("entry");
                            foreach ($entries as $entry)
                            {
                                $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
                                $key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
                                
                                if ($collection_id && $collection_id == $key) {
                                    echo "<option selected='selected' class='collection' value='".$key."'>".$title." [".$key."]</option>\n";
                                    $collection_name = $title;
                                }
                                else {
                                    echo "<option class='collection' value='".$key."'>".$title." [".$key."]</option>\n";
                                }
                            }
                            
                            unset($entries);
                            unset($doc_collections);
                            
                            ?>
                        </select>
                    </div>
                    
                    <div id="zp-FilterByTag-Section" class="section">
                        <label for="zp-FilterByTag">Sort by Tag:</label>
                        
                        <select id="zp-FilterByTag">
                            <option selected='selected' value=''>Select a tag</option>
                            <option value=''>-------------------</option>
                            <?php
                            
                            // READ ZOTERO XML FOR TAGS
                            
                            $doc_tags = new DOMDocument();
                            $doc_tags->load(ZOTPRESS_PLUGIN_URL."zotpress.rss.php?account_type=".$account_type."&api_user_id=".$api_user_id."&public_key=".$public_key."&data_type=tags");
                            
                            $entries = $doc_tags->getElementsByTagName("entry");
                            foreach ($entries as $entry)
                            {
                                $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
                                
                                if ($tag_name && $tag_name == $title) {
                                    echo "<option selected='selected' value='".urlencode($title)."'>".$title."</option>\n";
                                }
                                else {
                                    echo "<option value='".urlencode($title)."'>".$title."</option>\n";
                                }
                            }
                            
                            unset($entries);
                            unset($doc_tags);
                            
                            ?>
                        </select>
                    </div>
                    
                    <div class="section last">
                        <label for="zp-FilterByLimit">Limit by:</label>
                        <input id="zp-FilterByLimit" type="text" value="<?php echo $limit; ?>" />
                    </div>
                    
                </div>
            </div>
            
            <div id="zp-List">
                
                
                <?php
                
                // DISPLAY FILTER MESSAGE
                
                if ($collection_id)
                    echo "<h3>Viewing Collection \"".$collection_name."\" [".$collection_id."]</h3>\n\n";
                
                if ($tag_name)
                    echo "<h3>Viewing Citations with Tag \"".$tag_name."\"</h3>\n\n";
                
                // READ ZOTERO XML FOR CITATIONS
                
                $doc_citations = new DOMDocument();
                $doc_citations->load(ZOTPRESS_PLUGIN_URL."zotpress.rss.php?account_type=".$account_type."&api_user_id=".$api_user_id."&public_key=".$public_key."&collection_id=".$collection_id."&tag_name=".$tag_name."&limit=".$limit);
                
                $entries = $doc_citations->getElementsByTagName("entry");
                
                // READ IMAGES XML
                
                $doc_images = new DOMDocument();
                $doc_images->load(ZOTPRESS_PLUGIN_URL."zotpress.rss.php?account_type=".$account_type."&api_user_id=".$api_user_id."&public_key=".$public_key."&displayImages=true");
                
                $zpimages = $doc_images->getElementsByTagName('zpimage');
                
                // DISPLAY EACH ENTRY
                
                foreach ($entries as $entry)
                {
                    $citation_id = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
                    
                    // GET CITATION CONTENT
                    
                    $citation_html = new DOMDocument();
                    foreach($entry->getElementsByTagName("content")->item(0)->childNodes as $child) {
                        $citation_html->appendChild($citation_html->importNode($child, true));
                        $citation_content = $citation_html->saveHTML();
                    }
                    
                    // DISPLAY IMAGE
                    echo "<div class='zp-Entry'>\n";
                    echo "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image' rel='".$citation_id."'>\n";
                    
                    // GET CITATION IMAGE
                    
                    $citation_image = "<a href='admin.php?page=Zotpress&amp;image=true&amp;account_type=".$account_type."&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."&amp;citation=".urlencode($citation_content)."'>\n";
                    $citation_image .= "<span>Upload Image</span>";
                    
                    foreach ($zpimages as $zpimage)
                    {
                        if ($zpimage->getAttribute('citation_id') == $citation_id) {
                            $citation_image = "<a href='admin.php?page=Zotpress&amp;image=true&update=true&image_url=".$zpimage->getAttribute('image_url')."&amp;account_type=".$account_type."&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."&amp;citation=".urlencode($citation_content)."'>\n";
                            $citation_image .= "<span>Change Image</span>";
                            $citation_image .= "<img src='".$zpimage->getAttribute('image_url')."' alt='image' />\n";
                        }
                    }
                    
                    echo $citation_image;
                    
                    // DISPLAY CONT.
                    echo "</a>\n";
                    echo "<div class='bg'></div>";
                    echo "</div>\n";
                    
                    echo $citation_content."\n";
                    
                    echo "<span class='zp-Entry-ID'><span>Item Key (Citation ID):</span> ".$citation_id."</span>\n";
                    echo "</div>\n\n";
                }
                
                unset($zpimages);
                unset($entries);
                unset($doc_citations);
                unset($doc_images);
                
                ?>
            </div>
            
            
        </div>
        
    <?php } else { ?>
        
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.tabs.php'); ?>
            
            <p>
                Zotpress couldn't find any Zotero accounts. Would you like to add a Zotero account?
            </p>
            
            <a class="zp-AddAccount" href="admin.php?page=Zotpress&amp;accounts=true">Yes, let's do it!</a>
            
        </div>
        
    <?php } ?>