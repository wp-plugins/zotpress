<?php

    global $wpdb;
    
    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    $zp_accounts_total = $wpdb->num_rows;
    
    
    // FILTER PARAMETERS
    
    // Account ID
    
    global $account_id;
    $account_name = false;
    
    if ( isset($_GET['account_id']) && preg_match("/^[0-9]+$/", $_GET['account_id']) )
    {
        $account_id = $_GET['account_id'];
        $temp = $wpdb->get_row("SELECT nickname FROM ".$wpdb->prefix."zotpress WHERE id='".$account_id."'", OBJECT);
        $account_name = $temp->nickname;
    }
    else if ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) )
    {
        $temp = $wpdb->get_row("SELECT id, nickname FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);
        $account_id = $temp->id;
        $account_name = $temp->nickname;
    }
    else
    {
        if ( get_option("Zotpress_DefaultAccount") )
        {
            $temp = $wpdb->get_row("SELECT id, api_user_id, nickname FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".get_option("Zotpress_DefaultAccount")."'", OBJECT);
            $account_id = $temp->id;
            $account_name = $temp->nickname;
        }
        else
        {
            $temp = $wpdb->get_results("SELECT id, api_user_id, nickname FROM ".$wpdb->prefix."zotpress LIMIT 1");
            
            if (count($temp) > 0)
            {
                $account_id = $temp[0]->id;
                $account_name = $temp[0]->nickname;
            }
            else
            {
                $account_id = false;
            }
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
    
    if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']))
        $tag_id = trim($_GET['tag_id']);
    else
        $tag_id = false;
    
    if ($zp_accounts_total > 0)
    { ?>
    
    <div id="zp-Zotpress" class="wrap">
        
        <?php include('admin.display.tabs.php'); ?>
        
        <div id="zp-Browse-Wrapper">
            
            <h3><?php if ( $account_name !== false && strlen($account_name) > 0 ) { echo $account_name . "'s "; } else { echo "Your "; } ?>Library</h3>
            
            <div id="zp-Browse-Accounts">
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
                        if ($account_id && $account_id == $zp_account->id)
                        {
                            $account_type = $zp_account->account_type;
                            $api_user_id = $zp_account->api_user_id;
                            $public_key = $zp_account->public_key;
                            $nickname = $zp_account->nickname;
                        }
                        
                        // DISPLAY ACCOUNTS IN DROPDOWN
                        
                        if ($zp_account->nickname)
                        {
                            if ($account_id && $account_id == $zp_account->id)
                                echo "<option selected='selected' rel='".$zp_account->api_user_id."' value='".$zp_account->id."'>".$zp_account->nickname." [".$zp_account->api_user_id."]</option>\n";
                            else
                                echo "<option value='".$zp_account->id."' rel='".$zp_account->api_user_id."'>".$zp_account->nickname." [".$zp_account->api_user_id."]</option>\n";
                        }
                        else
                        {
                            if ($account_id && $account_id == $zp_account->id)
                                echo "<option selected='selected' value='".$zp_account->id."' rel='".$zp_account->api_user_id."'>".$zp_account->api_user_id."</option>\n";
                            else
                                echo "<option value='".$zp_account->id."' rel='".$zp_account->api_user_id."'>".$zp_account->api_user_id."</option>\n";
                        }
                    }
                    
                    ?>
                </select>
            </div>
            
            <span id="ZOTPRESS_PLUGIN_URL"><?php echo ZOTPRESS_PLUGIN_URL; ?></span>
            
            <div id="zp-Browse">
                
                <div id="zp-Browse-Bar">
                    
                    <div id="zp-Browse-Collections">
                        <a class="zp-List-Subcollection toplevel <?php if (!$collection_id && !$tag_id) { ?> selected<?php } ?>" title="Top Level" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=Zotpress<?php if ( $account_id ) { echo "&amp;account_id=".$account_id; } ?>"><span>Collections</span></a>
                        <?php
                        
                        if ( $collection_id ) // parent
                        {
                            //$zp_collection = get_term( $collection_id, 'zp_collections', 'OBJECT' );
                            $zp_top_collection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."' AND id='".$collection_id."'", OBJECT);
                            
                            //echo "<a class='zp-List-Subcollection selected' title='".$zp_collection->title."' href='" . $_SERVER['PHP_SELF']."?page=Zotpress&amp;collection_id=".$zp_collection->id;
                            //if ( $account_id ) { echo "&amp;account_id=".$account_id; }
                            //echo "'>";
                            //echo "<span class='name'>".$zp_collection->title."</span>";
                            //echo "<span class='item_key'>".$zp_collection->item_key."</span>";
                            //echo "<span class='meta'>".$zp_collection->numCollections." subcollections, ".$zp_collection->numItems." items</span>";
                            //echo "</a>\n";
                        }
                        
                        $zp_collections_query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$api_user_id."' ";
                        if ( $collection_id ) $zp_collections_query .= "AND parent='".$zp_top_collection->item_key."' "; else $zp_collections_query .= "AND parent='' ";
                        $zp_collections_query .= "ORDER BY title ASC";
                        //$zp_collections = get_terms( 'zp_collections', array( 'parent' => $collection_id, 'hide_empty' => false ) );
                        $zp_collections = $wpdb->get_results($zp_collections_query, OBJECT);
                        
                        foreach ( $zp_collections as $i => $zp_collection )
                        {
                            //if ( get_option( 'zp_collection-'.$zp_collection->term_id.'-api_user_id' ) != $account_id ) continue;
                            
                            echo "<a class='zp-List-Subcollection";
                            if ( $collection_id && $collection_id == $zp_collection->item_key ) echo " selected";
                            if ( $collection_id ) echo " child";
                            if ( !$collection_id && $i == (count($zp_collections)-1) ) echo " last";
                            echo "' title='".$zp_collection->title."' href='" . $_SERVER['PHP_SELF']."?page=Zotpress&amp;collection_id=".$zp_collection->id;
                            if ( $collection_id ) echo "&amp;up=".$collection_id;
                            if ( $account_id ) { echo "&amp;account_id=".$account_id; }
                            echo "'>";
                            echo "<span class='name'>".$zp_collection->title."</span>";
                            echo "<span class='item_key'>Collection Key: ".$zp_collection->item_key."</span>";
                            echo "<span class='meta'>".$zp_collection->numCollections." subcollections, ".$zp_collection->numItems." items</span>";
                            echo "</a>\n";
                        }
                        
                        ?>
                        <?php if ($collection_id) { ?><a class="zp-List-Subcollection back last" title="Back to previous collection(s)" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=Zotpress<?php if (isset($_GET['up']) && preg_match("/^[0-9]+$/", $_GET['up'])) { echo "&amp;collection_id=".$_GET['up']; } ?><?php if ( $account_id ) { echo "&amp;account_id=".$account_id; } ?>"><span>Back</span></a><?php } ?>
                    </div>
                    
                    
                    <div id="zp-Browse-Tags">
                        <label for="zp-List-Tags"><span>Tags</span></label>
                        <select id="zp-List-Tags" name="zp-List-Tags"<?php if ( $tag_id ) { ?> class="active"<?php } ?>>
                            <?php if ( !$tag_id ) { ?><option id="zp-List-Tags-Select" name="zp-List-Tags-Select">No tag selected</option><?php } ?>
                        <?php
                        
                        //$zp_tags = get_terms( 'zp_tags', array( 'hide_empty' => false ) );
                        $zp_tags = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."' ORDER BY title ASC", OBJECT);
                        
                        foreach ( $zp_tags as $zp_tag )
                        {
                            //if ( get_option( 'zp_tag-'.$zp_tag->term_id.'-api_user_id' ) != $account_id ) continue;
                            
                            echo "<option class='zp-List-Tag' rel='".$zp_tag->id."'";
                            if ( $tag_id == $zp_tag->id ) echo " selected='selected'";
                            echo ">".$zp_tag->title." (".$zp_tag->numItems.")";
                            echo "</option>\n";
                        }
                        
                        ?>
                        </select>
                    </div>
                    
                </div><!-- #zp-Browse-Bar -->
                
                <div id="zp-List">
                
                <?php
                
                // Display title if on collection page
                
                if ( $collection_id )
                {
                    echo "<div class='zp-Collection-Title'>";
                        echo "<span class='name'>".$zp_top_collection->title."</span>";
                        echo "<div class='item_key'>";
                            echo "<span class='item_key_title'>Collection key:</span>";
                            echo "<div class='item_key_inner'>";
                                echo "<span id='zp-Collection-Title-Key'>".$zp_top_collection->item_key."</span>";
                                echo "<input id='zp-Collection-Title-Key-Input' type='text' value='".$zp_top_collection->item_key."' />";
                            echo "</div>\n";
                        echo "</div>\n";
                    echo "</div>\n";
                }
                else if ( $tag_id ) // Top Level
                {
                    $tag_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$api_user_id."' AND id='".$tag_id."'", OBJECT);
                    echo "<div class='zp-Collection-Title'>Viewing items with the \"<strong>".$tag_title->title."</strong>\" tag</div>\n";
                }
                else
                {
                    echo "<div class='zp-Collection-Title'>Top Level Items</div>\n";
                }
                
                ?>
                
                <?php
                    
                    /*$zp_citation_attr =
                        array(
                            'posts_per_page' => -1,
                            'post_type' => 'zp_entry',
                            'orderby' => 'post_date',
                            'order' => 'DESC',
                            'meta_query' => array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'api_user_id',
                                    'value' => $account_id,
                                    'compare' => 'LIKE'
                                ),
                                array(
                                    'key' => 'item_type',
                                    'value' => array( 'attachment', 'note' ),
                                    'compare' => 'NOT IN'
                                )
                            )
                        );
                    
                    // By Collection ID
                    if (isset($_GET['collection_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id']) == 1)
                    {
                        $zp_citation_attr = array_merge( $zp_citation_attr,
                            array(
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'zp_collections',
                                        'field' => 'id',
                                        'terms' => $_GET['collection_id'],
                                        'include_children' => false
                                    )
                                )
                            )
                        );
                    
                    // By Tag ID
                    } else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1)
                    {
                        $zp_citation_attr = array_merge( $zp_citation_attr,
                            array(
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'zp_tags',
                                        'field' => 'id',
                                        'terms' => $_GET['tag_id']
                                    )
                                )
                            )
                        );
                    }
                    
                    $zp_citations = get_posts( $zp_citation_attr );*/
                    
                    // By Collection ID
                    if (isset($_GET['collection_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id']) == 1)
                    {
                        $zp_citations = $wpdb->get_results(
                            "
                            SELECT ".$wpdb->prefix."zotpress_zoteroItems.*,
							".$wpdb->prefix."zotpress_zoteroItemImages.image AS itemImage
							FROM ".$wpdb->prefix."zotpress_zoteroItems 
                            LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key 
							LEFT JOIN ".$wpdb->prefix."zotpress_zoteroItemImages
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id=".$wpdb->prefix."zotpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key = '".$zp_top_collection->item_key."' 
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$api_user_id."'
                            ORDER BY author ASC
                            "
                        );
                    }
                    // By Tag ID
                    else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1)
                    {
                        $zp_citations = $wpdb->get_results(
                            "
                            SELECT ".$wpdb->prefix."zotpress_zoteroItems.*,
							".$wpdb->prefix."zotpress_zoteroItemImages.image AS itemImage
							FROM ".$wpdb->prefix."zotpress_zoteroItems 
                            LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemTags
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemTags.item_key 
							LEFT JOIN ".$wpdb->prefix."zotpress_zoteroItemImages
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id=".$wpdb->prefix."zotpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."zotpress_zoteroRelItemTags.tag_title = '".$tag_title->title."' 
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$api_user_id."'
                            ORDER BY author ASC
                            "
                        );
                    }
                    // Top-level
                    else
                    {
                        $zp_citations = $wpdb->get_results(
                            "
                            SELECT ".$wpdb->prefix."zotpress_zoteroItems.*,
								".$wpdb->prefix."zotpress_zoteroItemImages.image AS itemImage,
								".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key
							FROM ".$wpdb->prefix."zotpress_zoteroItems 
                            LEFT JOIN ".$wpdb->prefix."zotpress_zoteroRelItemColl
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroRelItemColl.item_key
							LEFT JOIN ".$wpdb->prefix."zotpress_zoteroItemImages
								ON ".$wpdb->prefix."zotpress_zoteroItems.item_key=".$wpdb->prefix."zotpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id=".$wpdb->prefix."zotpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."zotpress_zoteroRelItemColl.collection_key IS NULL
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$api_user_id."'
                            ORDER BY author ASC
                            "
                        );
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
                            $citation_id = $entry->item_key;
                            $citation_content = htmlentities( $entry->citation, ENT_QUOTES, "UTF-8", true );
                            
                            $zp_thumbnail = false;
                            //if ( has_post_thumbnail( $entry->ID ) ) $zp_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $entry->ID ) );
                            if ( !is_null($entry->itemImage) ) $zp_thumbnail = wp_get_attachment_image_src($entry->itemImage);
                            
                            if ($entry_zebra === true) echo "<div class='zp-Entry'>\n"; else echo "<div class='zp-Entry odd'>\n";
                            
                            // DISPLAY IMAGE
                            echo "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image";
                            if ( $zp_thumbnail !== false ) echo " hasimage";
                            echo "' rel='".$citation_id."'>\n";
                            
                            // FEATURED IMAGE
                            $citation_image = "<a title='Set Image' class='upload' rel='".$entry->item_key."' href='media-upload.php?post_id=".$entry->id."&type=image&TB_iframe=1'>Set Image</a>\n";
                            
                            if ( $zp_thumbnail !== false )
                            {
                                $citation_image .= "<a title='Remove Image' class='delete' rel='".$entry->id."' href='".ZOTPRESS_PLUGIN_URL."lib/actions/actions.php?remove=image&amp;entry_id=".$entry->id."'>&times;</a>\n";
                                $citation_image .= "<img class='thumb' src='".$zp_thumbnail[0]."' alt='image' />\n";
                            }
                            
                            echo $citation_image;
                            echo "</div>\n";
                            
                            // DISPLAY CONTENT
                            echo html_entity_decode($citation_content, ENT_QUOTES)."\n";
                            
                            echo "<div class='zp-Entry-ID'><span class='title'>Item Key:</span> <div class='zp-Entry-ID-Text'><span>".$citation_id."</span><input value='".$citation_id."' /></div></div>\n";
                            echo "</div>\n\n";
                            
                            // Zebra striping
                            if ($entry_zebra === true) $entry_zebra = false; else $entry_zebra = true;
                        }
                    }
                    
                    ?>
                
                </div>
                
                <div id="zp-Pagination">
                    <div id="zp-PaginationInner">
                        <span class="zp-Pagination-Total">
                            Showing <?php echo count($zp_citations); if ( count($zp_citations) == 1 ) echo " entry"; else echo " entries"; unset($zp_citations); ?>
                        </span>
                    </div><!-- #zp-PaginationInner -->
                </div><!-- #zp-Pagination -->
                
            </div><!-- #zp-Browse -->
        </div><!-- #zp-Browse-Wrapper -->
            
    </div>
    
    
<?php } else { ?>
    
    <div id="zp-Zotpress">
        
        <div id="zp-Setup">
            
            <div id="zp-Zotpress-Navigation">
            
                <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>
                
                <div class="nav">
                    <div id="step-1" class="nav-item nav-tab-active">System Check</div>
                </div>
            
            </div><!-- #zp-Zotpress-Navigation -->
            
            <div id="zp-Setup-Step">
                
                <h3>Welcome to Zotpress</h3>
                
                <div id="zp-Setup-Check">
                    
                    <p>
                        Before we get started, let's make sure your system can support Zotpress:
                    </p>
                    
                    <?php
                    
                    $zp_check_curl = intval( function_exists('curl_version') );
                    $zp_check_streams = intval( function_exists('stream_get_contents') );
                    $zp_check_fsock = intval( function_exists('fsockopen') );
                    
                    if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) <= 1 ) { ?>
                    
                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> Zotpress requires at least one of the following: <strong>cURL, fopen with Streams (PHP 5), or fsockopen</strong>. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
                    </div>
                    
                    <?php } else { ?>
                    
                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em>Hurrah!</em></strong> Your system meets the requirements necessary for Zotpress to communicate with Zotero from WordPress.</p>
                    </div>
                    
                    <p>Sometimes systems aren't configured to allow communication with external websites. Let's check by accessing WordPress.org:
                    
                    <?php
                    
                    $response = wp_remote_get( "https://wordpress.org", array( 'headers' => array("Zotero-API-Version: 2") ) );
                    
                    if ( $response["response"]["code"] == 200 ) { ?>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#zp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Zotpress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <div id="zp-Setup-Check-Message" class="updated">
                        <p><strong><em>Great!</em></strong> We successfully connected to WordPress.org.</p>
                    </div>
                    
                    <p>Everything appears to check out. Let's continue setting up Zotpress by adding your Zotero account. Click "Next."
                    
                    <?php } else { ?>
                    
                    <div id="zp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> Zotpress was not able to connect to WordPress.org.</p>
                    </div>
                    
                    <p>Unfortunately, Zotpress ran into an error. Here's what WordPress has to say: <?php if ( is_wp_error($response) ) { echo $response->get_error_message(); } else { echo "Sorry, but there's no details on the error." ; } ?></p>
                    
                    <p>First, try reloading. If the error recurs, your system may not be set up to run Zotpress. Please contact your system administrator or website host and ask about allowing PHP scripts to access content like RSS feeds from external websites through cURL, fopen with Streams (PHP 5), or fsockopen.</p>
                    
                    <p>You can still try to use Zotpress, but it may not work and/or you may encounter further errors.</p>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#zp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=Zotpress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <?php }
                    } ?>
                    
                </div>
                
                <div class="proceed">
                    <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="Next" tabindex="5" disabled="disabled" />
                </div>
                
            </div>
            
        </div>
        
    </div>
    
<?php } ?>