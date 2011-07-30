<?php

    global $wpdb;
    
    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    
    $zp_accounts_total = $wpdb->num_rows;
    
    
    // FILTER PARAMETERS
    
    // Account ID
    
    global $account_id;
    
    if (isset($_GET['account_id']) && preg_match("/^[0-9]+$/", $_GET['account_id']))
        $account_id = trim($_GET['account_id']);
    else
        $account_id = false;
    
    
    // Collection ID
    
    global $collection_id;
    
    if (isset($_GET['collection_id']) && preg_match("/^[0-9a-zA-Z]+$/", $_GET['collection_id']))
        $collection_id = trim($_GET['collection_id']);
    else
        $collection_id = false;
    
    
    // Tag Name
    
    global $tag_name;
    
    if (isset($_GET['tag_name']) && preg_match("/^[0-9a-zA-Z -_+]+$/", $_GET['tag_name']))
        $tag_name = trim($_GET['tag_name']);
    else
        $tag_name = false;
    
    
    // Limit
    
    global $limit;
    
    if (isset($_GET['limit']) && preg_match("/^[0-9]+$/", $_GET['limit']))
        $limit = trim($_GET['limit']);
    else
        $limit = "5";
    
    
    if ($zp_accounts_total > 0) { ?>
    
    
    <div id="zp-Zotpress" class="wrap">
        
        <?php include('zotpress.admin.display.tabs.php'); ?>
        
        <?php
        
        // Use iframe to load content nicely
        if (!isset( $_GET['loaded'] )) {
        
        ?>
        
        <div class='zp-Loading-Initial'>
            <h2>Loading ...</h2>
            <p>Grabbing your latest citations ... this may take a few moments.</p>
        </div>
        
        <?php
        
        // Determine if we're forcing a reload
        if (isset( $_GET['recache'] ))
            echo '<iframe id="zp-Loading-Initial" src="admin.php?page=Zotpress&amp;loaded=false&amp;recache=true"></iframe>';
        else
            echo '<iframe id="zp-Loading-Initial" src="admin.php?page=Zotpress&amp;loaded=false"></iframe>';
            
        ?>
        
        <?php } else { ?>
        
        <?php
        
        // Determine if we're forcing a reload
        if (isset( $_GET['recache'] ))
            $recache = true;
        else
            $recache = false;
            
        ?>
        
        <span id="ZOTPRESS_PLUGIN_URL"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
        
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
                        
                        // INCLUDE REQUEST FUNCTION
                        $include = true;
                        include("zotpress.rss.php");
                        
                        ?>
                    </select>
                    <span class="divider"></span>
                </div>
                
                <div id="zp-FilterByCollection-Section" class="section">
                    <label for="zp-FilterByCollection">Sort by Collection:</label>
                    
                    <select id="zp-FilterByCollection">
                        <option selected='selected' value=''>Select a collection</option>
                        <option value=''>-------------------</option>
                        <?php
                        
                        // READ ZOTERO XML FOR COLLECTIONS
                        
                        $zp_xml = MakeZotpressRequest($account_type, $api_user_id, "collections", false, false, false, -1, false, true, $recache);
                        
                        $doc_collections = new DOMDocument();
                        $doc_collections->loadXML($zp_xml);
                        
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
                        unset ($zp_xml);
                        
                        ?>
                    </select>
                    <span class="divider"></span>
                </div>
                
                <div id="zp-FilterByTag-Section" class="section">
                    <label for="zp-FilterByTag">Sort by Tag:</label>
                    
                    <select id="zp-FilterByTag">
                        <option selected='selected' value=''>Select a tag</option>
                        <option value=''>-------------------</option>
                        <?php
                        
                        // READ ZOTERO XML FOR TAGS
                        
                        $zp_xml = MakeZotpressRequest($account_type, $api_user_id, "tags", false, false, false, -1, false, true, $recache);
                        
                        $doc_tags = new DOMDocument();
                        $doc_tags->loadXML($zp_xml);
                        
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
                        unset($zp_xml);
                        
                        ?>
                    </select>
                    <span class="divider"></span>
                </div>
                
                <div class="section last">
                    <label for="zp-FilterByLimit">Limit by:</label>
                    <input id="zp-FilterByLimit" type="text" value="<?php echo $limit; ?>" />
                </div>
                
            </div>
            <div class="clear"></div>
        </div>
        
        <div id="zp-Cache"></div>
        
        <div id="zp-List">
            
            
            <?php
            
            // DISPLAY FILTER MESSAGE
            
            if ($collection_id)
                echo "<h3>Viewing Collection \"".$collection_name."\" [".$collection_id."]</h3>\n\n";
            
            if ($tag_name)
                echo "<h3>Viewing Citations Tagged \"".$tag_name."\"</h3>\n\n";
            
            // READ ZOTERO XML FOR CITATIONS
            
            $zp_xml = MakeZotpressRequest($account_type, $api_user_id, false, $collection_id, false, $tag_name, $limit, false, true, $recache);
            
            $doc_citations = new DOMDocument();
            $doc_citations->loadXML($zp_xml);
            
            $entries = $doc_citations->getElementsByTagName("entry");
            
            unset($zp_xml);
            
            // READ IMAGES XML
            
            $zp_xml = MakeZotpressRequest($account_type, $api_user_id, false, false, false, false, -1, true, true, $recache);
            
            $doc_images = new DOMDocument();
            $doc_images->loadXML($zp_xml);
            
            $zpimages = $doc_images->getElementsByTagName('zpimage');
            
            unset($zp_xml);
            
            // DISPLAY EACH ENTRY
            
            foreach ($entries as $entry)
            {
                $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
                
                if ($item_type == "attachment")
                    continue;
                
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
                
                $citation_image = "<a class='upload' href='admin.php?page=Zotpress&amp;image=true&amp;account_type=".$account_type."&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."'>Upload Image</a>\n";
                
                foreach ($zpimages as $zpimage)
                {
                    if ($zpimage->getAttribute('citation_id') == $citation_id)
                    {
                        // Update
                        $citation_image = "<a class='change' href='admin.php?page=Zotpress&amp;image=true&update=true&image_url=".$zpimage->getAttribute('image_url')."&amp;account_type=".$account_type."&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."&amp;citation=".urlencode($citation_content)."'>Change Image</a>\n";
                        
                        // Delete
                        $citation_image .= "<a class='delete' rel='".$citation_id."' href='javascript:void(0);'>&times;</a>\n";
                        
                        $citation_image .= "<img class='thumb' src='".$zpimage->getAttribute('image_url')."' alt='image' />\n";
                    }
                }
                
                echo $citation_image;
                
                // DISPLAY CONT.
                echo "<div class='bg'></div>";
                echo "</div>\n";
                
                echo $citation_content."\n";
                
                echo "<div class='zp-Entry-ID'><span class='title'>Item Key (Citation ID):</span> <div class='zp-Entry-ID-Text'><span>".$citation_id."</span><input value='".$citation_id."' /></div></div>\n";
                echo "</div>\n\n";
            }
            
            unset($zpimages);
            unset($entries);
            unset($doc_citations);
            unset($doc_images);
            
            ?>
        
        </div>
        
        <?php if ($_GET['loaded'] == "false") { ?>
        
        <script type="text/javascript">
        
            jQuery(document).ready(function()
            {
                
                /*
                    
                    LOAD ADMIN PAGE AFTER LOADING ZOTERO DATA
                    
                */
                
                jQuery(window).load(function () {
                    window.parent.location = "admin.php?page=Zotpress&loaded=true";
                });
                
            });
            
        </script>
        
        <?php } // After finishing loading ?>
        
        <?php } // Loading check ?>
        
    </div>
    
    
<?php } else { ?>
    
    <div id="zp-Zotpress" class="wrap">
        
        <?php include('zotpress.admin.display.tabs.php'); ?>
        
        <p>
            Zotpress couldn't find any Zotero accounts. Would you like to add a Zotero account?
        </p>
        
        <a class="zp-AddAccount" href="admin.php?page=Zotpress&amp;accounts=true">Yes, let's do it!</a>
        
    </div>
    
<?php } ?>