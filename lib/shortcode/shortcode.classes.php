<?php
 
class zotpressBrowse
{
	/**
	 * Creates a HTML-formatted library for the selected account.
	 *
	 * TO-DO:
	 * - Remove admin-only features, e.g. Set Image, item key input box, etc.
	 * - Style front-end display
	 * - Add shortcode information to Help page
	 * 
	 * @return     string         	the HTML-formatted subcollections
	 */
	
	private $api_user_id = "";
	
	public function __construct()
	{
		// Called automatically when an instance is instantiated
	}
	
	public function setAccount($api_user_id)
	{
		$this->api_user_id = $api_user_id;
	}
	
	public function getAccount()
	{
		return $this->api_user_id;
	}
	
	public function getLib()
	{
		global $wpdb;
		
		
		// Account ID
		
		global $api_user_id;
		
		if ( isset($_GET['account_id']) && preg_match("/^[0-9]+$/", $_GET['account_id']) )
			$api_user_id = $wpdb->get_var("SELECT nickname FROM ".$wpdb->prefix."zotpress WHERE id='".$_GET['account_id']."'", OBJECT);
		
		
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
		
		
		?>
            <div id="zp-Browse">
                
                <div id="zp-Browse-Bar">
                    
                    <div id="zp-Browse-Collections">
						<?php
						
						// Collection Title
						
						if ( is_admin() ) // Admin Browse Only
						{
							echo '<a class="zp-List-Subcollection toplevel ';
							if (!$collection_id && !$tag_id) echo 'selected';
							echo '" title="Top Level" href="?page=Zotpress';
							if ( $api_user_id ) echo "&amp;api_user_id=".$api_user_id;
							echo '"><span>Collections</span></a>';
						}
						
						// Display Collection List
                        
                        if ( $collection_id ) // parent
                        {
                            //$zp_collection = get_term( $collection_id, 'zp_collections', 'OBJECT' );
                            if ( is_admin( ) ) $zp_top_collection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' AND id='".$collection_id."'", OBJECT);
                            if ( ! is_admin( ) ) $zp_top_collection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' AND item_key='".$collection_id."'", OBJECT);
                        }
                        
                        $zp_collections_query = "SELECT * FROM ".$wpdb->prefix."zotpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' ";
                        if ( $collection_id ) $zp_collections_query .= "AND parent='".$zp_top_collection->item_key."' "; else $zp_collections_query .= "AND parent='' ";
                        $zp_collections_query .= "ORDER BY title ASC";
                        //$zp_collections = get_terms( 'zp_collections', array( 'parent' => $collection_id, 'hide_empty' => false ) );
                        $zp_collections = $wpdb->get_results($zp_collections_query, OBJECT);
						
						if ( ! is_admin() )
						{
							echo "<div class='zp-Browse-Select'><select id='zp-Browse-Collections-Select'>\n";
							
							if ( $tag_id ) echo "<option value='blank'>--Nothing Selected--</option>";
							if ( ! $collection_id ) echo "<option value='toplevel'>Top level</option>";
							if ( $collection_id ) echo "<option selected='selected' value='".$zp_top_collection->item_key."'>".$zp_top_collection->title."</option>";
						}
                        
                        foreach ( $zp_collections as $i => $zp_collection )
                        {
                            //if ( get_option( 'zp_collection-'.$zp_collection->term_id.'-api_user_id' ) != $account_id ) continue;
                            
							if ( ! is_admin() )
							{
								echo '<option value="'.$zp_collection->item_key.'">';
								if ( $collection_id ) echo " - ";
								echo $zp_collection->title.' ('.$zp_collection->numCollections.' subcollections, '.$zp_collection->numItems.' items)</option>'; echo "\n";
							}
							else // admin browse
							{
								echo "<a class='zp-List-Subcollection";
								if ( $collection_id && $collection_id == $zp_collection->item_key ) echo " selected";
								if ( $collection_id ) echo " child";
								if ( !$collection_id && $i == (count($zp_collections)-1) ) echo " last";
								echo "' title='".$zp_collection->title."' href='?page=Zotpress&amp;collection_id=".$zp_collection->id;
								if ( $collection_id ) echo "&amp;up=".$collection_id;
								if ( $api_user_id ) { echo "&amp;api_user_id=".$api_user_id; }
								echo "'>";
								echo "<span class='name'>".$zp_collection->title."</span>";
								echo "<span class='item_key'>Collection Key: ".$zp_collection->item_key."</span>";
								echo "<span class='meta'>".$zp_collection->numCollections." subcollections, ".$zp_collection->numItems." items</span>";
								echo "</a>\n";
							}
                        }
                        
						// Collection List back button
                        if ( is_admin() && $collection_id )
						{
							echo '<a class="zp-List-Subcollection back last" title="Back to previous collection(s)" href="?page=Zotpress';
							if (isset($_GET['up']) && preg_match("/^[0-9]+$/", $_GET['up'])) echo "&amp;collection_id=".$_GET['up'];
							if ( $api_user_id ) echo "&amp;api_user_id=".$api_user_id;
							echo '"><span>Back</span></a>';
						}
						
						if ( ! is_admin() )
						{
							if ( $collection_id ) echo "<option value='toplevel'>Back to Top level</option>";
							echo "</select></div>\n";
						}
						?>
                    </div><!-- #zp-Browse-Collections -->
                    
                    
                    <div id="zp-Browse-Tags">
                        <?php
						
						if ( is_admin() ) echo '<label for="zp-List-Tags"><span>Tags</span></label>';
						
						if ( ! is_admin() ) echo "<div class='zp-Browse-Select'>";
						echo '<select id="zp-List-Tags" name="zp-List-Tags"';
						if ( $tag_id ) echo ' class="active"';
						echo '>';
                        
						if ( !$tag_id ) echo '<option id="zp-List-Tags-Select" name="zp-List-Tags-Select">No tag selected</option>';
                        
                        //$zp_tags = get_terms( 'zp_tags', array( 'hide_empty' => false ) );
                        $zp_tags = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$this->api_user_id."' ORDER BY title ASC", OBJECT);
                        
                        foreach ( $zp_tags as $zp_tag )
                        {
                            //if ( get_option( 'zp_tag-'.$zp_tag->term_id.'-api_user_id' ) != $account_id ) continue;
                            
                            echo "<option class='zp-List-Tag' rel='".$zp_tag->id."'";
                            if ( $tag_id == $zp_tag->id ) echo " selected='selected'";
                            echo ">".$zp_tag->title." (".$zp_tag->numItems.")";
                            echo "</option>\n";
                        }
						
						echo "</select>\n";
                        
						if ( ! is_admin() ) echo "</div>\n";
                        ?>
                    </div><!-- #zp-Browse-Tags -->
                    
                </div><!-- #zp-Browse-Bar -->
                
				
                <div id="zp-List">
                
                <?php
                
                // Display title if on collection page
                
                if ( $collection_id )
                {
                    echo "<div class='zp-Collection-Title'>";
                        echo "<span class='name'>".$zp_top_collection->title."</span>";
						if ( is_admin() )
						{
							echo "<div class='item_key'>";
								echo "<span class='item_key_title'>Collection key:</span>";
								echo "<div class='item_key_inner'>";
									echo "<span id='zp-Collection-Title-Key'>".$zp_top_collection->item_key."</span>";
									echo "<input id='zp-Collection-Title-Key-Input' type='text' value='".$zp_top_collection->item_key."' />";
								echo "</div>\n";
							echo "</div>\n";
						}
                    echo "</div>\n";
                }
                else if ( $tag_id ) // Top Level
                {
                    $tag_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."zotpress_zoteroTags WHERE api_user_id='".$this->api_user_id."' AND id='".$tag_id."'", OBJECT);
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
                        $zp_citations_query = 
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
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$zp_citations = $wpdb->get_results( $zp_citations_query );
                    }
                    // By Tag ID
                    else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1)
                    {
                        $zp_citations_query =
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
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$zp_citations = $wpdb->get_results( $zp_citations_query );
                    }
                    // Top-level
                    else
                    {
                        $zp_citations_query =
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
                            AND ".$wpdb->prefix."zotpress_zoteroItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$zp_citations = $wpdb->get_results( $zp_citations_query );
                    }
                    
                    
                    // DISPLAY EACH ENTRY
                    
                    $entry_zebra = true;
                    
                    if (count($zp_citations) == 0)
                    {
                        echo "<p>There are no citations to display.";
						if ( is_admin() ) echo " If you think you're receiving this message in error, you may need to <a title=\"Import your Zotero items\" href=\"admin.php?page=Zotpress&setup=true&setupstep=three&api_user_id=".$this->api_user_id."\" style=\"color: #f00000; text-shadow: none;\">import your Zotero library</a>.";
						echo "</p>";
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
                            
							
                            // START OF DISPLAY IMAGE
							
                            if ( is_admin() || $zp_thumbnail !== false ) echo "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image";
                            if ( $zp_thumbnail !== false ) echo " hasimage";
                            if ( is_admin() || $zp_thumbnail !== false ) echo "' rel='".$citation_id."'>\n";
                            
                            $citation_image = "";
							
                            if ( is_admin() ) $citation_image .= "<a title='Set Image' class='upload' rel='".$entry->item_key."' href='media-upload.php?post_id=".$entry->id."&type=image&TB_iframe=1'>Set Image</a>\n";
                            
                            if ( $zp_thumbnail !== false )
                            {
                                if ( is_admin() ) $citation_image .= "<a title='Remove Image' class='delete' rel='".$entry->id."' href='".ZOTPRESS_PLUGIN_URL."lib/actions/actions.php?remove=image&amp;entry_id=".$entry->id."'>&times;</a>\n";
                                $citation_image .= "<img class='thumb' src='".$zp_thumbnail[0]."' alt='image' />\n";
                            }
                            
                            echo $citation_image;
                            if ( is_admin() || $zp_thumbnail !== false ) echo "</div><!-- .zp-Entry-Image -->\n";
							
							// END OF DISPLAY IMAGE
							
                            
                            // DISPLAY CONTENT
                            echo html_entity_decode($citation_content, ENT_QUOTES)."\n";
                            
                            if ( is_admin() ) echo "<div class='zp-Entry-ID'><span class='title'>Item Key:</span> <div class='zp-Entry-ID-Text'><span>".$citation_id."</span><input value='".$citation_id."' /></div></div>\n";
                            echo "</div>\n\n";
                            
                            // Zebra striping
                            if ($entry_zebra === true) $entry_zebra = false; else $entry_zebra = true;
                        }
                    }
                    
                    ?>
                
                </div><!-- #zp-List -->
                
                <div id="zp-Pagination">
                    <div id="zp-PaginationInner">
                        <span class="zp-Pagination-Total">
                            Showing <?php echo count($zp_citations); if ( count($zp_citations) == 1 ) echo " entry"; else echo " entries"; unset($zp_citations); ?>
                        </span>
                    </div><!-- #zp-PaginationInner -->
                </div><!-- #zp-Pagination -->
                
            </div><!-- #zp-Browse -->
		<?php
	}
}
 
?>