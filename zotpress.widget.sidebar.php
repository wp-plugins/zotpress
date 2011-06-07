<?php
    
    class ZotpressSidebarWidget extends WP_Widget {
        
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated above}
        *   $GLOBALS['zp_shortcode_attrs']
        *   $GLOBALS['zp_account']
        *   $GLOBALS['zp_instance_id']
        *
        */
        
        function ZotpressSidebarWidget()
        {
            $widget_ops = array('description' => __('Display your citations on your sidebar', 'zp-ZotpressSidebarWidget'));
	    parent::WP_Widget(false, __('Zotpress Widget'), $widget_ops);
        }
	
        function widget( $args, $instance )
        {
            extract( $args );
            
            // ARGUMENTS
            $widget_title = apply_filters('widget_title', $instance['widget_title'] );
            
            $api_user_id = $instance['api_user_id'];
            $nickname = isset( $instance['nickname'] ) ? $instance['nickname'] : false;
            $author = isset( $instance['author'] ) ? $instance['author'] : false;
            $year = isset( $instance['year'] ) ? $instance['year'] : false;
            
            $data_type = isset( $instance['data_type'] ) ? $instance['data_type'] : "items";
            $collection_id = isset( $instance['collection_id'] ) ? $instance['collection_id'] : false;
            $item_key = isset( $instance['item_key'] ) ? $instance['item_key'] : false;
            $tag_name = isset( $instance['tag_name'] ) ? $instance['tag_name'] : false;
            
            $content = isset( $instance['content'] ) ? $instance['content'] : "bib";
            $style = isset( $instance['style'] ) ? $instance['style'] : "apa";
            $order = isset( $instance['order'] ) ? $instance['order'] : false;
            $sort = isset( $instance['sort'] ) ? $instance['sort'] : false;
            $limit = isset( $instance['limit'] ) ? $instance['limit'] : "5";
	    
            $sortby = isset( $instance['sortby'] ) ? $instance['sortby'] : false;
	    if ($sortby == "latest updated")
		$sortby = false;
            
            $image = isset( $instance['image'] ) ? $instance['image'] : "no";
            $download = isset( $instance['download'] ) ? $instance['download'] : "no";
            $title = isset( $instance['zptitle'] ) ? $instance['zptitle'] : "no";
            
            
            
            
            // Required for theme
            echo $before_widget;
            
            if ($widget_title)
                echo $before_title . $widget_title . $after_title;
            
            
            
            // DISPLAY
            
            global $wpdb;
            
            if ($api_user_id != false)
                $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'");
            else if ($nickname != false)
                $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'");
	    else if ($api_user_id == false && $nickname == false)
		$GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");
            
	    // Get total accounts
            $zp_accounts_total = $wpdb->num_rows;
	    
	    // Set api_user_id and account type
	    $api_user_id = $GLOBALS['zp_account'][0]->api_user_id;
	    $account_type = $GLOBALS['zp_account'][0]->account_type;
	    
            // Generate instance id for shortcode
            $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sort.$order.$limit.$image.$download);
	    
	    
	    if ($zp_accounts_total > 0)
	    {
		// INCLUDE REQUEST FUNCTION
		
		$include = true;
		require_once("zotpress.rss.php");
		$recache = false;
		$zp_output = "\n\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress zp-ZotpressSidebarWidget'>\n	<div class='zp-ZotpressInner'>\n";
		
		
		// READ FORMATTED CITATION XML
		
		$zp_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $GLOBALS['zp_instance_id'], false, false, $style);
		
		$doc_citations = new DOMDocument();
		$doc_citations->loadXML($zp_xml);
		
		$zp_entries = $doc_citations->getElementsByTagName("entry");
		
		unset($zp_xml);
		
		
		// READ CITATION META XML
		
		$zp_meta_xml = MakeZotpressRequest($account_type, $api_user_id, $data_type, $collection_id, $item_key, $tag_name, $limit, false, true, $recache, $GLOBALS['zp_instance_id'], true, false, $style);
		
		$doc_meta = new DOMDocument();
		$doc_meta->loadXML($zp_meta_xml);
		
		$zp_meta_entries = $doc_meta->getElementsByTagName("entry");
		
		unset($zp_meta_xml);
		
		
		// Prep citation array
		$zp_citations = array();
		
		
		// DISPLAY EACH ENTRY
		
		foreach ($zp_entries as $entry)
		{
		    // Get item type
		    $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
		    
		    // IGNORE ATTACHMENTS
		    if ($item_type == "attachment")
			continue;
		    
		    // Get citation ID
		    $citation_id = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
		    
		    // GET META
		    foreach ($zp_meta_entries as $zp_meta_entry)
			if ($zp_meta_entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue == $citation_id)
			    $zp_this_meta = json_decode( $zp_meta_entry->getElementsByTagName("content")->item(0)->nodeValue );
		    
		    // FILTER BY AUTHOR
		    if ($author !== false && $author !== "")
		    {
			$temp_continue = false;
			
			foreach ($zp_this_meta->creators as $creator) {
			    if (str_replace(" ", "+", $creator->firstName."+".$creator->lastName) == $author)
				$temp_continue = true;
			}
			
			if ($temp_continue === false)
			    continue;
		    }
		    
		    // Format date
		    $zp_this_meta->date = preg_replace( '/-\d{1,2}/', '', $zp_this_meta->date );
		    if (strlen($zp_this_meta->date) == 4)
			$zp_this_meta->date = "January 1, ".$zp_this_meta->date;
		    if (is_numeric(substr($zp_this_meta->date, 0, 3))) {
			$temp = substr($zp_this_meta->date, 0, 4);
			$zp_this_meta->date = trim(substr($zp_this_meta->date, 4, strlen($zp_this_meta->date))).", ".$temp;
		    }
		    
		    // FILTER BY YEAR
		    if ($year !== false && $year !== "" && date("Y", strtotime($zp_this_meta->date)) != $year)
			continue;
		    
		    
		    // GET CITATION CONTENT
		    $citation_html = new DOMDocument();
		    foreach($entry->getElementsByTagName("content")->item(0)->childNodes as $child) {
			$citation_html->appendChild($citation_html->importNode($child, true));
			$citation_content = $citation_html->saveHTML();
			$citation_content = preg_replace( '/^\s+|\n|\r|\s+$/m', '', trim( $citation_content ) );
		    }
		    
		    // Hyperlink URL
		    if (isset($zp_this_meta->url) && strlen($zp_this_meta->url) > 0)
			$citation_content = str_replace($zp_this_meta->url, "<a title='".$zp_this_meta->title."' rel='external' href='".$zp_this_meta->url."'>".$zp_this_meta->url."</a>", $citation_content);
		    
		    // GET DOWNLOAD URL
		    $zp_download_url = false;
		    if (isset($download) && $download == "yes")
		    {
			if ($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue > 0)
			{
			    $zp_item_xml = MakeZotpressRequest($account_type, $api_user_id, "items", false, $citation_id, false, false, false, true, false, $GLOBALS['zp_instance_id'], false, true, $style);
			    
			    $item_meta = new DOMDocument();
			    $item_meta->loadXML($zp_item_xml);
			    
			    $zp_download_url = "<a class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."zotpress.rss.file.php?account_type=".$account_type."&api_user_id=".$api_user_id."&download_url=".$item_meta->getElementsByTagName("entry")->item(0)->getElementsByTagName("link")->item(3)->getAttribute('href')."'>(Download)</a>";
			    
			    $citation_content = str_replace("</div></div>", " ".$zp_download_url."</div></div>", $citation_content);
			    
			    unset($zp_item_xml);
			    unset($item_meta);
			}
		    }
		    
		    // GET CITATION IMAGE
		    $has_citation_image = false;
		    $citation_image = false;
		    if (isset($image) && $image == "yes")
		    {
			$zp_entry_image = $wpdb->get_results("SELECT image FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".$citation_id."'");
			
			if ($wpdb->num_rows > 0)
			{
			    $citation_image .= "<div id='zp-Citation-".$citation_id."' class='zp-Entry-Image' rel='".$citation_id."'>";
			    $citation_image .= "<img src='".$zp_entry_image[0]->image."' alt='image' />";
			    $citation_image .= "</div>\n";
			    
			    $has_citation_image = " zp-HasImage";
			}
			else {
			    $citation_image = false;
			}
		    }
                    
                    $zp_author = false;
                    if (isset($zp_this_meta->creators[0]->lastName))
                        $zp_author = $zp_this_meta->creators[0]->lastName;
                    
		    
		    $zp_citations[count($zp_citations)] = array(
                                                                'author' => $zp_author,
                                                                'date' => date( "Y-m-d", strtotime( $zp_this_meta->date ) ),
                                                                'hasImage' => $has_citation_image,
                                                                'image' => $citation_image,
                                                                'content' => $citation_content
                                                                );
		}
		
		// SORT CITATIONS
		if ($sortby)
		{
		    $zp_citations = subval_sort( $zp_citations, $sortby, "asc" );
		}
		
		// OUTPUT CITATIONS
		foreach ($zp_citations as $zp_citation) {
		    if (isset($current_title) && $current_title == "") {
			$current_title = date("Y", strtotime($zp_citation['date']));
			$zp_output .= "<h3>".$current_title."</h3>\n";
		    }
		    else if (isset($current_title) && strlen($current_title) > 0 && $current_title != date("Y", strtotime($zp_citation['date']))) {
			$current_title = date("Y", strtotime($zp_citation['date']));
			$zp_output .= "<h3>".$current_title."</h3>\n";
		    }
		    $zp_output .= "<div class='zp-Entry".$zp_citation['hasImage']."'>\n" . $zp_citation['image'] . $zp_citation['content'] . "\n</div><!--Entry-->\n\n";
		}
		
		$zp_output .= "\n</div>\n</div>\n\n";
		
		echo $zp_output;
		
		unset($zp_images);
		unset($zp_entries);
		unset($doc_citations);
		unset($doc_images);
	    }
	    else
	    {
		echo "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress zp-ZotpressSidebarWidget'>Sorry, no citations found.</div>\n";
	    }
            
            // Required for theme
            echo $after_widget;
        }
        
        function update( $new_instance, $old_instance )
        {
            $instance = $old_instance;
            
            $instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
            
            $instance['api_user_id'] = strip_tags( $new_instance['api_user_id'] );
            $instance['nickname'] = strip_tags($new_instance['nickname']);
            $instance['author'] = str_replace(" ", "+", strip_tags($new_instance['author']));
            $instance['year'] = str_replace(" ", "+", strip_tags($new_instance['year']));
            
            $instance['data_type'] = strip_tags( $new_instance['data_type'] );
            $instance['collection_id'] = strip_tags($new_instance['collection_id']);
            $instance['item_key'] = strip_tags($new_instance['item_key']);
            $instance['tag_name'] = str_replace(" ", "+", strip_tags($new_instance['tag_name']));
            
            $instance['content'] = strip_tags( $new_instance['content'] );
            $instance['style'] = strip_tags($new_instance['style']);
            //$instance['order'] = strip_tags($new_instance['order']);
            $instance['sort'] = strip_tags($new_instance['sort']);
            $instance['sortby'] = strip_tags($new_instance['sortby']);
	    
            $instance['limit'] = strip_tags($new_instance['limit']);
            if (intval($instance['limit']) > 99)
                $instance['limit'] = "99";
            if (trim($instance['limit']) == "")
                $instance['limit'] = "5";
            
            $instance['image'] = strip_tags($new_instance['image']);
            $instance['download'] = strip_tags($new_instance['download']);
            $instance['zptitle'] = strip_tags($new_instance['zptitle']);
            
            return $instance;
        }
        
        function form( $instance )
        {
            $widget_title = esc_attr( $instance['widget_title'] );
            ?>
            
                <style type="text/css">
                <!--
                    span.req {
                        color: #CC0066;
                        font-weight: bold;
                        font-size: 1.4em;
                        vertical-align: -20%;
                    }
                    
                    div.zp-ZotpressSidebarWidget-Required {
                        border-radius: 10px;
                        -moz-border-radius: 10px;
                        background-color: #fafafa;
                        margin: 0 0 10px 0;
                        padding: 10px 10px 1px 10px;
                    }
                    
                    div.zp-ZotpressSidebarWidget-Required .widefat {
                        width: 98%;
                    }
                -->
                </style>
            
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>">Widget Title:</label>
			<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php echo $instance['widget_title']; ?>" class="widefat" />
		</p>
                
                <div class="zp-ZotpressSidebarWidget-Required">
                    
                    <p>
                        Fill in <strong>one</strong> of the below. Req'd.
                    </p>
                    
                    <p>
                            <label for="<?php echo $this->get_field_id( 'api_user_id' ); ?>">API User/Group ID: <span class="req">*</span></label>
                            <input id="<?php echo $this->get_field_id( 'api_user_id' ); ?>" name="<?php echo $this->get_field_name( 'api_user_id' ); ?>" value="<?php echo $instance['api_user_id']; ?>" class="widefat" />
                    </p>
                    
                    <p>
                            <label for="<?php echo $this->get_field_id( 'nickname' ); ?>">Nickname: <span class="req">*</span></label>
                            <input id="<?php echo $this->get_field_id( 'nickname' ); ?>" name="<?php echo $this->get_field_name( 'nickname' ); ?>" value="<?php echo $instance['nickname']; ?>" class="widefat" />
                    </p>
                    
                </div>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'data_type' ); ?>">Data Type:</label>
			<select id="<?php echo $this->get_field_id( 'data_type' ); ?>" name="<?php echo $this->get_field_name( 'data_type' ); ?>" class="widefat">
				<option <?php if ( 'items' == $instance['data_type'] ) echo 'selected="selected"'; ?>>items</option>
				<option <?php if ( 'tags' == $instance['data_type'] ) echo 'selected="selected"'; ?>>tags</option>
				<option <?php if ( 'collections' == $instance['data_type'] ) echo 'selected="selected"'; ?>>collections</option>
			</select>
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'author' ); ?>">Enter Author to List by Author:</label>
			<input id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" value="<?php echo $instance['author']; ?>" class="widefat" />
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'year' ); ?>">Enter Year to List by Year:</label>
			<input id="<?php echo $this->get_field_id( 'year' ); ?>" name="<?php echo $this->get_field_name( 'year' ); ?>" value="<?php echo $instance['year']; ?>" class="widefat" />
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'collection_id' ); ?>">Enter Collection ID to List by Collection:</label>
			<input id="<?php echo $this->get_field_id( 'collection_id' ); ?>" name="<?php echo $this->get_field_name( 'collection_id' ); ?>" value="<?php echo $instance['collection_id']; ?>" class="widefat" />
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'item_key' ); ?>">Enter Item Key to List by Citation:</label>
			<input id="<?php echo $this->get_field_id( 'item_key' ); ?>" name="<?php echo $this->get_field_name( 'item_key' ); ?>" value="<?php echo $instance['item_key']; ?>" class="widefat" />
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'tag_name' ); ?>">Enter Tag Name to List by Tag:</label>
			<input id="<?php echo $this->get_field_id( 'tag_name' ); ?>" name="<?php echo $this->get_field_name( 'tag_name' ); ?>" value="<?php echo $instance['tag_name']; ?>" class="widefat" />
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>">Content:</label>
			<select id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" class="widefat">
				<option <?php if ( 'bib' == $instance['content'] ) echo 'selected="selected"'; ?>>bib</option>
				<option <?php if ( 'html' == $instance['content'] ) echo 'selected="selected"'; ?>>html</option>
			</select>
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>">Style:</label>
			<input id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" value="<?php echo $instance['style']; ?>" class="widefat" />
		</p>
                
                <?php if (1 == 2) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>">Order By:</label>
			<input id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" value="<?php echo $instance['order']; ?>" class="widefat" />
		</p>
                <?php } // hehe ?>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'sortby' ); ?>">Sort By:</label>
			<select id="<?php echo $this->get_field_id( 'sortby' ); ?>" name="<?php echo $this->get_field_name( 'sortby' ); ?>" class="widefat">
				<option>latest updated</option>
				<option <?php if ( 'author' == $instance['sortby'] ) echo 'selected="selected"'; ?>>author</option>
				<option <?php if ( 'date' == $instance['sortby'] ) echo 'selected="selected"'; ?>>date</option>
			</select>
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'sort' ); ?>">Sort Order:</label>
			<select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>" class="widefat">
				<option <?php if ( 'desc' == $instance['sort'] ) echo 'selected="selected"'; ?>>desc</option>
				<option <?php if ( 'asc' == $instance['sort'] ) echo 'selected="selected"'; ?>>asc</option>
			</select>
		</p>
                
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">Limit:</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" class="widefat" />
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>">Show Image?:</label>
			<select id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" class="widefat">
				<option <?php if ( 'no' == $instance['image'] ) echo 'selected="selected"'; ?>>no</option>
				<option <?php if ( 'yes' == $instance['image'] ) echo 'selected="selected"'; ?>>yes</option>
			</select>
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'download' ); ?>">Show Download URL?:</label>
			<select id="<?php echo $this->get_field_id( 'download' ); ?>" name="<?php echo $this->get_field_name( 'download' ); ?>" class="widefat">
				<option <?php if ( 'no' == $instance['download'] ) echo 'selected="selected"'; ?>>no</option>
				<option <?php if ( 'yes' == $instance['download'] ) echo 'selected="selected"'; ?>>yes</option>
			</select>
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'zptitle' ); ?>">Show Title(s)?:</label>
			<select id="<?php echo $this->get_field_id( 'zptitle' ); ?>" name="<?php echo $this->get_field_name( 'zptitle' ); ?>" class="widefat">
				<option <?php if ( 'no' == $instance['zptitle'] ) echo 'selected="selected"'; ?>>no</option>
				<option <?php if ( 'yes' == $instance['zptitle'] ) echo 'selected="selected"'; ?>>yes</option>
			</select>
		</p>
                
            <?php
        }
    }
    
    function ZotpressSidebarWidgetInit() {
        register_widget( 'ZotpressSidebarWidget' );
    }
    
?>