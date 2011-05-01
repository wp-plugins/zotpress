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
            $title = apply_filters('widget_title', $instance['title'] );
            
            $api_user_id = $instance['api_user_id'];
            $nickname = isset( $instance['nickname'] ) ? $instance['nickname'] : false;
            $author = isset( $instance['author'] ) ? $instance['author'] : false;
            
            $data_type = isset( $instance['data_type'] ) ? $instance['data_type'] : "items";
            $collection_id = isset( $instance['collection_id'] ) ? $instance['collection_id'] : false;
            $item_key = isset( $instance['item_key'] ) ? $instance['item_key'] : false;
            $tag_name = isset( $instance['tag_name'] ) ? $instance['tag_name'] : false;
            
            $content = isset( $instance['content'] ) ? $instance['content'] : "bib";
            $style = isset( $instance['style'] ) ? $instance['style'] : "apa";
            //$order = isset( $instance['order'] ) ? $instance['order'] : false;
            $sort = isset( $instance['sort'] ) ? $instance['sort'] : false;
            $limit = isset( $instance['limit'] ) ? $instance['limit'] : "5";
            
            $image = isset( $instance['image'] ) ? $instance['image'] : "no";
            $download = isset( $instance['download'] ) ? $instance['download'] : "no";
            
            
            
            
            // Required for theme
            echo $before_widget;
            
            if ($title)
                echo $before_title . $title . $after_title;
            
            
            
            // DISPLAY
            
            global $wpdb;
            
            if ($api_user_id != false)
                $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'");
            else if ($nickname != false)
                $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'");
            
            $zp_accounts_total = $wpdb->num_rows;
	    $api_user_id = $GLOBALS['zp_account'][0]->api_user_id;
	    $account_type = $GLOBALS['zp_account'][0]->account_type;
	    
            //$GLOBALS['zp_instance_id'] = "zotpress-".rand(100,999);
            // Generate instance id for shortcode
            $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sort.$order.$limit.$image.$download);
            
            // Create global array with the above shortcode attributes
            $GLOBALS['zp_shortcode_attrs'] = array(
                    "api_user_id" => $api_user_id,
		    "account_type" => $account_type,
                    "nickname" => $nickname,
                    "author" => $author,
                    "year" => $year,
                    
                    "data_type" => $data_type,
                    
                    "collection_id" => $collection_id,
                    "item_key" => $item_key,
                    "tag_name" => $tag_name,
                    
                    "content" => $content,
                    "style" => $style,
                    //"order" => $order,
                    "sort" => $sort,
                    "limit" => $limit,
                    
                    "image" => $image,
                    "download" => $download,
            );
            
            if ($zp_accounts_total > 0)
            {
                if ($GLOBALS['is_shortcode_displayed'] == false)
                {
                    add_action('wp_print_footer_scripts', 'Zotpress_theme_shortcode_script_footer');
                    add_action('wp_print_footer_scripts', 'Zotpress_theme_shortcode_display_script_footer');
                }
                
                $GLOBALS['is_shortcode_displayed'] = true;
                
                ob_start();
                include( 'zotpress.shortcode.display.php' );
                $GLOBALS['zp_shortcode_instances'][$GLOBALS['zp_instance_id']] = ob_get_contents();
                ob_end_clean();
                
                $zp_content = "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress zp-ZotpressSidebarWidget'><span class='zp-Loading'><span>loading</span></span><div class='zp-ZotpressInner'></div></div>\n";
                echo $zp_content;
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
            
            $instance['title'] = strip_tags( $new_instance['title'] );
            
            $instance['api_user_id'] = strip_tags( $new_instance['api_user_id'] );
            $instance['nickname'] = strip_tags($new_instance['nickname']);
            $instance['author'] = str_replace(" ", "+", strip_tags($new_instance['author']));
            
            $instance['data_type'] = strip_tags( $new_instance['data_type'] );
            $instance['collection_id'] = strip_tags($new_instance['collection_id']);
            $instance['item_key'] = strip_tags($new_instance['item_key']);
            $instance['tag_name'] = str_replace(" ", "+", strip_tags($new_instance['tag_name']));
            
            $instance['content'] = strip_tags( $new_instance['content'] );
            $instance['style'] = strip_tags($new_instance['style']);
            //$instance['order'] = strip_tags($new_instance['order']);
            $instance['sort'] = strip_tags($new_instance['sort']);
            $instance['limit'] = strip_tags($new_instance['limit']);
            if (intval($instance['limit']) > 99)
                $instance['limit'] = "99";
            if (trim($instance['limit']) == "")
                $instance['limit'] = "5";
            
            $instance['image'] = strip_tags($new_instance['image']);
            $instance['download'] = strip_tags($new_instance['download']);
            
            return $instance;
        }
        
        function form( $instance )
        {
            $title = esc_attr( $instance['title'] );
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
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
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
                
            <?php
        }
    }
    
    function ZotpressSidebarWidgetInit() {
        register_widget( 'ZotpressSidebarWidget' );
    }
    
?>