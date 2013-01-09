<?php

    global $wpdb;
    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    $zp_accounts_total = $wpdb->num_rows;
    
    
    // FILTER PARAMETERS
    
    // Account ID
    
    global $account_id;
    
    if (isset($_GET['account_id']) && preg_match("/^[0-9]+$/", $_GET['account_id'])) {
        $account_id = trim($_GET['account_id']);
    }
    else {
        if (get_option("Zotpress_DefaultAccount")) {
            $temp = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".get_option("Zotpress_DefaultAccount")."'", OBJECT);
            $account_id = $temp->id;
        }
        else
        {
            $temp = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."zotpress LIMIT 1");
            
            if (count($temp) > 0)
                $account_id = $temp[0]->id;
            else
                $account_id = false;
        }
    }
    
    
    
    // Collection ID
    
    global $collection_id;
    
    if (isset($_GET['collection_id']) && preg_match("/^[0-9a-zA-Z]+$/", $_GET['collection_id']))
        $collection_id = trim($_GET['collection_id']);
    else
        $collection_id = false;
    
    
    // Tag Name and ID
    
    global $tag_id;
    global $tag_name;
    
    if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']))
        $tag_id = trim($_GET['tag_id']);
    else
        $tag_id = false;
    
    if (isset($_GET['tag_name']) && preg_match("/^[0-9a-zA-Z -_+]+$/", $_GET['tag_name']))
        $tag_name = trim($_GET['tag_name']);
    else
        $tag_name = false;
    
    
    if ($zp_accounts_total > 0) { ?>
    
    <div id="zp-Zotpress" class="wrap">
        
        <?php include('admin.display.tabs.php'); ?>
        
        <span id="ZOTPRESS_PLUGIN_URL"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
        
        <div id="zp-Filter">
            <div id="zp-FilterInner">
            
                <div class="section first">
                    <label for="zp-FilterByAccount">Account:</label>
                    
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
                                    echo "<option selected='selected' value='".$zp_account->id."'>".$zp_account->nickname." [".$zp_account->api_user_id."]</option>\n";
                                else
                                    echo "<option value='".$zp_account->id."'>".$zp_account->nickname." [".$zp_account->api_user_id."]</option>\n";
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
                    <span class="divider"></span>
                </div>
                
                <div id="zp-FilterByTag-Section" class="section">
                    <label for="zp-FilterByTag">Filter by Tag:</label>
                    
                    <select id="zp-FilterByTag">
                        <option selected='selected' value=''>Select a tag</option>
                        <option value=''>-------------------</option>
                        <?php
                        
                        // DISPLAY TAG LIST
                        
                        $zp_tags = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."' ORDER BY title ASC");
                        
                        foreach ($zp_tags as $zp_tag)
                        {
                            if ($tag_id && $tag_id == $zp_tag->id) {
                                echo "<option selected='selected' value='".urlencode($zp_tag->id)."'>".$zp_tag->title." (".$zp_tag->numItems." items)</option>\n";
                                $tag_name = $zp_tag->title;
                            }
                            else {
                                echo "<option value='".urlencode($zp_tag->id)."'>".$zp_tag->title." (".$zp_tag->numItems." items)</option>\n";
                            }
                        }
                        
                        unset($zp_tags);
                        unset($zp_tag);
                        
                        ?>
                    </select>
                </div>
                
            </div>
            <div class="clear"></div>
        </div>
        
        <div id="zp-List"><?php
            
            
            // GET COLLECTION LIST: Top level or sub-collections
            if ($collection_id)
            {
                $zp_collections = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."' AND parent='".$collection_id."' ORDER BY title ASC");
                
                
                // Display title
                
                $zp_collection = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."' AND item_key='".$collection_id."'");
                echo "<h3>Collection: ".$zp_collection[0]->title." (".$collection_id.")";
                
                if (trim($zp_collection[0]->parent) != "")
                    echo " &emsp; / <a title='Back' href='" . $_SERVER['PHP_SELF']."?".str_replace($collection_id, $zp_collection[0]->parent, $_SERVER['QUERY_STRING']) ."'>Back</a>";
                else
                    echo " &emsp; / <a title='Back' href='" . $_SERVER['PHP_SELF']."?".str_replace("&collection_id=", "", str_replace($collection_id, "", $_SERVER['QUERY_STRING'])) ."'>Back</a>";
                
                echo "</h3>\n\n";
                
                
                // Display links to subcollections, if applicable
                
                if ($zp_collection[0]->numCollections > 0)
                {
                    echo "<div id='zp-List-Subcollection'>\n";
                    
                    foreach ($zp_collections as $zp_collection)
                        echo "<a class='zp-List-Subcollection' title='".$zp_collection->title."' href='" . $_SERVER['PHP_SELF']."?".str_replace($collection_id, $zp_collection->item_key, $_SERVER['QUERY_STRING']) ."'>".$zp_collection->title." (".$zp_collection->item_key.") <span class='meta'>(".$zp_collection->numCollections." subcollections, ".$zp_collection->numItems." items)</span></a>\n";
                    
                    echo "</div>\n\n";
                }
            }
            
            // Top level collections
            else if (!$collection_id && !$tag_id)
            {
                $zp_collections = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."' AND parent='' ORDER BY title ASC");
                
                if (count($zp_collections) > 0)
                {
                    echo "<div id='zp-List-Subcollection'>\n";
                    
                    foreach ($zp_collections as $zp_collection)
                        echo "<a class='zp-List-Subcollection' title='".$zp_collection->title."' href='" . $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'] ."&amp;collection_id=".$zp_collection->item_key."'>".$zp_collection->title." (".$zp_collection->item_key.") <span class='meta'>(".$zp_collection->numCollections." subcollections, ".$zp_collection->numItems." items)</span></a>\n";
                    
                    echo "</div>\n\n";
                }
            }
            
            unset($zp_collections);
           
           
            // DISPLAY TAG TITLE
            
            if ($tag_name) {
                echo "<h3>Viewing Items Tagged \"".$tag_name."\"";
                echo " &emsp; / <a title='Back' href='" . $_SERVER['PHP_SELF']."?".str_replace("&tag_id=".$tag_id, "", $_SERVER['QUERY_STRING'])."'>Back</a></h3>\n\n";
            }
            
            
            
            // GET CITATIONS
            
            // By Collection ID
            if (isset($_GET['collection_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id']) == 1) {
                $zp_citations = $wpdb->get_results("SELECT ".$wpdb->prefix."zotpress_zoteroItems.* FROM ".$wpdb->prefix."zotpress_zoteroCollections, ".$wpdb->prefix."zotpress_zoteroItems WHERE ".$wpdb->prefix."zotpress_zoteroCollections.item_key='".$_GET['collection_id']."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroItems.item_key, ".$wpdb->prefix."zotpress_zoteroCollections.listitems) AND itemType != 'note' AND itemType != 'attachment' ORDER BY author ASC");
            
            // By Tag ID
            } else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1) {
                $zp_citations = $wpdb->get_results("SELECT ".$wpdb->prefix."zotpress_zoteroItems.* FROM ".$wpdb->prefix."zotpress_zoteroTags, ".$wpdb->prefix."zotpress_zoteroItems WHERE ".$wpdb->prefix."zotpress_zoteroTags.id='".$_GET['tag_id']."' AND FIND_IN_SET(".$wpdb->prefix."zotpress_zoteroItems.item_key, ".$wpdb->prefix."zotpress_zoteroTags.listitems) AND itemType != 'note' AND itemType != 'attachment' ORDER BY author ASC");
            
            // Top-level
            } else {
                $zp_all_citations = $wpdb->get_col("SELECT item_key FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."' AND itemType != 'note' AND itemType != 'attachment'");
                $zp_all_collection_citations = $wpdb->get_results("SELECT listItems FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."'");
                
                if (count($zp_all_collection_citations) > 0)
                {
                    $zp_all_collection_citations_arr = array();
                    foreach ($zp_all_collection_citations as $list)
                        foreach(explode(",", $list->listItems) as $list_item )
                            array_push($zp_all_collection_citations_arr, $list_item);
                    
                    $zp_toplevel_citations = array_diff( $zp_all_citations, $zp_all_collection_citations_arr );
                    $zp_citations = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE item_key IN ('" . implode("','", $zp_toplevel_citations) . "') AND api_user_id='".$api_user_id."' ORDER BY author ASC");
                }
                else // no collections
                {
                    $zp_citations = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."' AND itemType != 'note' AND itemType != 'attachment' LIMIT 99");
                }
            }
            
            
            
            // DISPLAY EACH ENTRY
            
            $entry_zebra = true;
            
            if (count($zp_citations) == 0)
            {
                echo "<p>There are no citations to display. If you think you're receiving this message in error, you may need to <a title=\"Import your Zotero items\" href=\"admin.php?page=Zotpress&setup=true&setupstep=three&api_user_id=".$api_user_id."\" style=\"color: #f00000; text-shadow: none;\">import your Zotero library</a>.</p>";
            }
            else // display
            {
                foreach ($zp_citations as $entry)
                {
                    $item_type = $entry->itemType;
                    $citation_id = $entry->item_key;
                    $citation_content = $entry->citation;
                    
                    
                    // DISPLAY IMAGE
                    if ($entry_zebra === true)
                        echo "<div class='zp-Entry'>\n";
                    else
                        echo "<div class='zp-Entry odd'>\n";
                    
                    echo "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image' rel='".$citation_id."'>\n";
                    
                    // GET CITATION IMAGE
                    $citation_image = "<a class='upload' href='admin.php?page=Zotpress&amp;image=true&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."'>Upload Image</a>\n";
                    
                    if (is_null($entry->image) === false && $entry->image != "")
                    {
                        $citation_image = "<a class='change' href='admin.php?page=Zotpress&amp;image=true&update=true&amp;api_user_id=".$api_user_id."&amp;citation_id=".$citation_id."'>Change Image</a>\n";
                        $citation_image .= "<a class='delete' rel='lib/actions/actions.php?remove=".$citation_id."&amp;api_user_id=".$api_user_id."' href='javascript:void(0);'>&times;</a>\n";
                        $citation_image .= "<img class='thumb' src='".$entry->image."' alt='image' />\n";
                    }
                    
                    echo $citation_image;
                    
                    // DISPLAY CONT.
                    echo "<div class='bg'></div>";
                    echo "</div>\n";
                    
                    echo html_entity_decode($citation_content, ENT_QUOTES)."\n";
                    
                    echo "<div class='zp-Entry-ID'><span class='title'>Item Key (Citation ID):</span> <div class='zp-Entry-ID-Text'><span>".$citation_id."</span><input value='".$citation_id."' /></div></div>\n";
                    echo "</div>\n\n";
                    
                    // Zebra striping
                    if ($entry_zebra === true)
                        $entry_zebra = false;
                    else
                        $entry_zebra = true;
                }
            }
            
            ?>
        
        </div>
        
        <div id="zp-Pagination">
            <div id="zp-PaginationInner">
                <span class="zp-Pagination-Total">
                    Showing 
                <?php
                
                echo count($zp_citations) . " out of ";
                unset($zp_citations);
                
                $zp_citations_count = $wpdb->get_results("SELECT COUNT(id) AS count FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE api_user_id='".$api_user_id."' AND itemType != 'note' AND itemType != 'attachment'", OBJECT);
                
                echo $zp_citations_count[0]->count;
                
                ?> entries</span><?php
                
                unset($zp_citations_count);
                
                ?>
            </div>
        </div>
        
    </div>
    
    
<?php } else { ?>
    
    <div id="zp-Zotpress" class="wrap">
        
        <h3>Loading setup ...</h3>
        
        <div class="zp-Loading-Initial"></div>
        
        <script type="text/javascript">
        
            jQuery(document).ready(function()
            {
                // LOAD SETUP
                
                jQuery(window).load(function () {
                    window.parent.location = "admin.php?page=Zotpress&setup=true";
                });
                
            });
            
        </script>
        
    </div>
    
<?php } ?>