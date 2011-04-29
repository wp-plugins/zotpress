<?php

/*
 
    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Display your Zotero citations on your Wordpress blog.
    Author: Katie Seaborn
    Version: 3.1
    Author URI: http://katieseaborn.com
    
*/

// GLOBAL VARS ----------------------------------------------------------------------------------
    
    define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    
    $GLOBALS['is_shortcode_displayed'] = false;
    $GLOBALS['zp_shortcode_instances'] = array();
    
    global $Zotpress_main_db_version;
    $Zotpress_main_db_version = "1.0";
    if (!get_option("Zotpress_main_db_version"))
        add_option("Zotpress_main_db_version", $Zotpress_main_db_version);
    
    global $Zotpress_images_db_version;
    $Zotpress_images_db_version = "1.0";
    if (!get_option("Zotpress_images_db_version"))
        add_option("Zotpress_images_db_version", $Zotpress_images_db_version);
    
    global $Zotpress_cache_db_version;
    $Zotpress_cache_db_version = "2.6";
    if (!get_option("Zotpress_cache_db_version"))
        add_option("Zotpress_cache_db_version", $Zotpress_cache_db_version);
    
    global $Zotpress_oauth_db_version;
    $Zotpress_oauth_db_version = "1.1";
    if (!get_option("Zotpress_oauth_db_version"))
        add_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);

// GLOBAL VARS ----------------------------------------------------------------------------------
    

// INSTALL -----------------------------------------------------------------------------------------
    
    function Zotpress_install()
    {
        global $wpdb;
        global $Zotpress_main_db_version;
        global $Zotpress_images_db_version;
        global $Zotpress_cache_db_version;
        global $Zotpress_oauth_db_version;
        
        
        // ACCOUNTS TABLE
        
        if (($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress'") != $wpdb->prefix."zotpress")
                || (get_option("Zotpress_main_db_version") != $Zotpress_main_db_version))
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress (
                id INT(9) NOT NULL AUTO_INCREMENT,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                public_key VARCHAR(28) default NULL,
                nickname VARCHAR(200) default NULL,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            if (get_option("Zotpress_main_db_version") != $Zotpress_main_db_version)
                update_option("Zotpress_main_db_version", $Zotpress_main_db_version);
            else
                add_option("Zotpress_main_db_version", $Zotpress_main_db_version);
        }
        
        
        // IMAGE TABLE
        
        if (($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_images'") != $wpdb->prefix."zotpress_images")
                || (get_option("Zotpress_images_db_version") != $Zotpress_images_db_version))
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_images (
                id INT(9) NOT NULL AUTO_INCREMENT,
                citation_id VARCHAR(10) NOT NULL,
                image VARCHAR(300) NOT NULL,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            if (get_option("Zotpress_images_db_version") != $Zotpress_images_db_version)
                update_option("Zotpress_images_db_version", $Zotpress_images_db_version);
            else
                add_option("Zotpress_images_db_version", $Zotpress_images_db_version);
        }
        
        
        // CACHE TABLE
        
        if (($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_cache'") != $wpdb->prefix."zotpress_cache")
                || (get_option("Zotpress_cache_db_version") != $Zotpress_cache_db_version))
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_cache (
                id INT(9) NOT NULL AUTO_INCREMENT,
                instance_id TEXT,
                zpoutput LONGTEXT,
                api_user_id VARCHAR(50),
                nickname VARCHAR(150),
                author VARCHAR(150),
                year VARCHAR(5),
                data_type VARCHAR(150),
                collection_id VARCHAR(150),
                item_key VARCHAR(150),
                tag_name VARCHAR(150),
                content VARCHAR(50),
                style VARCHAR(50),
                zporder VARCHAR(50),
                sort VARCHAR(50),
                zplimit VARCHAR(5),
                image VARCHAR(5),
                download VARCHAR(5),
                cache_key LONGTEXT NOT NULL,
                xml_data LONGTEXT NOT NULL,
                cache_time VARCHAR(100),
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            if (get_option("Zotpress_cache_db_version") != $Zotpress_cache_db_version)
                update_option("Zotpress_cache_db_version", $Zotpress_cache_db_version);
            else
                add_option("Zotpress_cache_db_version", $Zotpress_cache_db_version);
        }
        
        
        // OAUTH CACHE TABLE
        
        if (($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_oauth'") != $wpdb->prefix."zotpress_oauth")
                || (get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version))
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_oauth (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            if (get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version)
                update_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
            else
                add_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
                
            // Initial populate
            $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_oauth (cache) VALUES ('empty')");
        }
    }

    register_activation_hook(__FILE__, 'Zotpress_install');

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -----------------------------------------------------------------------------------------
    
    function Zotpress_admin_footer()
    {
        // Connect to database
        global $wpdb;
        
        $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
            
        $zp_accounts_total = $wpdb->num_rows;
        
        // INCLUDE FILTER SCRIPT
        
        if ($zp_accounts_total > 0)
        {
            if (!isset($_GET['accounts']) || !isset($_GET['help'])) {
?>
<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        <?php include('zotpress.display.filter.php'); ?>
        
        /*
            
            CITATION IMAGE HOVER
            
        */
        
        jQuery('div#zp-List').delegate("div.zp-Entry-Image", "hover", function () {
            jQuery(this).toggleClass("hover");
        });
        
    });
    
</script>
<?php
            }
        }
    }

    function Zotpress_options()
    {
        // Keep out those without access!
        if (!current_user_can('manage_options'))  {
                wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        
        // ADD ZOTERO ACCOUNT
        
        if (isset($_GET['accounts']))
        {
            include('zotpress.accounts.php');
        }
        
        
        
        // ADD ZOTERO ACCOUNT
        
        else if (isset($_GET['image']))
        {
            global $wpdb;
            
            $zp_image = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".trim($_GET['citation_id'])."'");
            
            include('zotpress.image.php');
        }
        
        
        
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include('zotpress.help.php');
        }
        
        
        
        // ADMIN VIEW CITATIONS
        
        else
        {
            global $wpdb;
            
            $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
            
            $zp_accounts_total = $wpdb->num_rows;
            
            
            // FILTER PARAMETERS
            
            // Account ID
            
            global $account_id;
            
            if (isset($_GET['account_id']))
                if (trim($_GET['account_id']) != "")
                    $account_id = trim($_GET['account_id']);
                else
                    $account_id = false;
            else
                $account_id = false;
            
            // Collection ID
            
            global $collection_id;
            
            if (isset($_GET['collection_id']))
                if (trim($_GET['collection_id']) != "")
                    $collection_id = trim($_GET['collection_id']);
                else
                    $collection_id = false;
            else
                $collection_id = false;
            
            // Tag Name
            
            global $tag_name;
            
            if (isset($_GET['tag_name']))
                if (trim($_GET['tag_name']) != "")
                    $tag_name = trim($_GET['tag_name']);
                else
                    $tag_name = false;
            else
                $tag_name = false;
            
            // Limit
            
            global $limit;
            
            if (isset($_GET['limit']))
                if (trim($_GET['limit']) != "")
                    $limit = trim($_GET['limit']);
                else
                    $limit = "5";
            else
                $limit = "5";
            
            
            // DISPLAY ADMIN CITATIONS
            
            include('zotpress.default.php');
        }
    }

// ADMIN -----------------------------------------------------------------------------------------



// SHORTCODE -----------------------------------------------------------------------------------------

    // Thanks to rosty dot kerei at gmail dot com at php.net
    function unicode_urldecode($url)
    {
        preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
       
        foreach ($a[1] as $uniord)
        {
            $dec = hexdec($uniord);
            $utf = '';
           
            if ($dec < 128)
            {
                $utf = chr($dec);
            }
            else if ($dec < 2048)
            {
                $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
            else
            {
                $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
           
            $url = str_replace('%u'.$uniord, $utf, $url);
        }
       
        return urldecode($url);
    }
    
    
    function Zotpress_func($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['zp_shortcode_instances'] {instantiated above}
        *   $GLOBALS['zp_shortcode_attrs']
        *   $GLOBALS['zp_account']
        *   $GLOBALS['zp_instance_id']
        *
        */
        
        extract(shortcode_atts(array(
            
            'user_id' => false,
            'nickname' => false,
            'author' => false,
            'year' => false,
            
            'data_type' => "items",
            
            'collection_id' => false,
            'item_key' => false,
            'tag_name' => false,
            
            'content' => "bib",
            'style' => "apa",
            'order' => false,
            'sort' => false,
            'limit' => "50",
            
            'image' => "no",
            'download' => "no"
            
        ), $atts));
        
        // Format attributes
        $api_user_id = str_replace('"','',html_entity_decode($user_id));
        $nickname = str_replace('"','',html_entity_decode($nickname));
        $author = str_replace('"','',html_entity_decode($author));
        $year = str_replace('"','',html_entity_decode($year));
        
        $data_type = str_replace('"','',html_entity_decode($data_type));
        
        $collection_id = str_replace('"','',html_entity_decode($collection_id));
        $item_key = str_replace('"','',html_entity_decode($item_key));
        $tag_name = str_replace('"','',html_entity_decode($tag_name));
        
        $content = str_replace('"','',html_entity_decode($content));
        $style = str_replace('"','',html_entity_decode($style));
        $order = str_replace('"','',html_entity_decode($order));
        $sort = str_replace('"','',html_entity_decode($sort));
        $limit = str_replace('"','',html_entity_decode($limit));
        
        $image = str_replace('"','',html_entity_decode($image));
        $download = str_replace('"','',html_entity_decode($download));
        if ($download == "true" || $download === true)
            $download = "yes";
        
        // Connect to database
        global $wpdb;
        
        // Get account and private key
        if ($api_user_id != false)
            $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'");
        else if ($nickname != false)
            $GLOBALS['zp_account'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'");
        
        // Get total accounts
        $zp_accounts_total = $wpdb->num_rows;
        
        // Set api_user_id and account type
        $api_user_id = $GLOBALS['zp_account'][0]->api_user_id;
        $account_type = $GLOBALS['zp_account'][0]->account_type;
        
        // Create global array with the above shortcode attributes
        $GLOBALS['zp_shortcode_attrs'] = array(
                "api_user_id" => $api_user_id,
                "nickname" => $nickname,
                "account_type" => $account_type,
                "author" => $author,
                "year" => $year,
                
                "data_type" => $data_type,
                
                "collection_id" => $collection_id,
                "item_key" => $item_key,
                "tag_name" => $tag_name,
                
                "content" => $content,
                "style" => $style,
                "order" => $order,
                "sort" => $sort,
                "limit" => $limit,
                
                "image" => $image,
                "download" => $download,
        );
        
        
        // FIRST, CHECK IF REQUEST EXISTS
        
        $zp_request_query = "SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE 
                                    api_user_id='".$api_user_id."' AND 
                                    data_type='".$data_type."' AND
                                    content='".$content."' AND
                                    ";
        if ($author)
            $zp_request_query .= "author='".$author."' AND ";
        else
            $zp_request_query .= "author IS NULL AND ";
        
        if ($year)
            $zp_request_query .= "year='".$year."' AND ";
        else
            $zp_request_query .= "year IS NULL AND ";
        
        if ($collection_id)
            $zp_request_query .= "collection_id='".$collection_id."' AND ";
        else
            $zp_request_query .= "collection_id IS NULL AND ";
        
        if ($item_key)
            $zp_request_query .= "item_key='".$item_key."' AND ";
        else
            $zp_request_query .= "item_key IS NULL AND ";
        
        if ($tag_name)
            $zp_request_query .= "tag_name='".$tag_name."' AND ";
        else
            $zp_request_query .= "tag_name IS NULL AND ";
        
        if ($order)
            $zp_request_query .= "zporder='".$order."' AND ";
        else
            $zp_request_query .= "zporder IS NULL AND ";
        
        if ($sort)
            $zp_request_query .= "sort='".$sort."' AND ";
        else
            $zp_request_query .= "sort IS NULL AND ";
        
        if ($limit)
            $zp_request_query .= "zplimit='".$limit."' AND ";
        else
            $zp_request_query .= "zplimit IS NULL AND ";
        
        if ($image)
            $zp_request_query .= "image='".$image."' AND ";
        else
            $zp_request_query .= "image IS NULL AND ";
        
        $zp_request_query .= "style='".$style."'";
        $zp_request = $wpdb->get_results($zp_request_query);
        
        // Get total matching requests (should be 0 or 1)
        $zp_request_match = $wpdb->num_rows;
        
        if ($zp_request_match > 0)
        {
            $temp = "";
            
            // Display cached citation output
            foreach ($zp_request as $key => $output)
                $temp .= unicode_urldecode( html_entity_decode( $output->zpoutput ) );
            
?><!-- START OF ZOTPRESS CODE -->

<style type="text/css">
<!--
    div.zp-Zotpress {
        margin: 1em 0;
    }
    div.zp-ZotpressInner {
        display: none;
    }
    div.zp-Zotpress div.zp-Entry {
        position: relative;
        clear: both;
    }
    div.zp-Zotpress div.zp-Entry.zp-Image {
        min-height: 170px;
    }
    div.zp-Zotpress div.zp-Entry-Image {
        position: absolute;
        top: 0;
        left: 0;
    }
    div.zp-Zotpress div.zp-Entry-Image-Crop {
        overflow: hidden;
        width: 150px;
        height: 150px;
    }
    div.zp-Zotpress div.csl-bib-body {
        margin: 0 0 15px 0;
    }
    div.zp-Zotpress div.zp-Entry.zp-Image div.csl-bib-body {
        margin: 0 0 15px 170px;
    }
    div.zp-Zotpress span.zp-Loading {
        border: 1px solid #ddd;
        border-radius: 5px;
        -moz-border-radius: 5px;
        background: #f3f3f3 url('<?php echo ZOTPRESS_PLUGIN_URL; ?>images/loading_list.gif') no-repeat top left;
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

<!-- END OF ZOTPRESS CODE -->
<?php
            echo "<div class='zp-Zotpress'>".$temp."</div>\n";
        }
        
        
        // IF THE REQUEST IS NEW, PROCEED
        
        else
        {
            // Generate instance id for shortcode
            $GLOBALS['zp_instance_id'] = "zotpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sort.$order.$limit.$image.$download);
            
            // Display shortcode
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
                
                // This shortcode instance's container
                $zp_content = "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'><span class='zp-Loading'><span>loading</span></span><div class='zp-ZotpressInner'></div></div>\n";
                
                return $zp_content;
            }
            
            // Display notification if no citations found
            else {
                echo "\n<div id='".$GLOBALS['zp_instance_id']."' class='zp-Zotpress'>Sorry, no citations found.</div>\n";
            }
        } // $zp_request_match
    }
    
    function Zotpress_theme_shortcode_display_script_footer() {
        foreach ($GLOBALS['zp_shortcode_instances'] as $id => $zp_shortcode_instance)
            echo $zp_shortcode_instance;
        
        // Load again, this time checking for updates
        echo "
        jQuery('div#".$GLOBALS['zp_instance_id']."').one('ajaxStop', function()
        {
            for (key in window.zp_ajax_calls) {
                //alert(window.zp_ajax_calls[key]+'('+key+'/'+window.zp_ajax_calls.length+')');
                jQuery.ajax({
                    url: window.zp_ajax_calls[key].replace('&step=one', ''),
                    dataType: 'XML',
                    cache: false,
                    async: false,
                    ifModified: false // Change to true when implemented on Zotero end
                });
            }
        });
    });
    
    </script>\n\n<!-- END OF ZOTPRESS CODE -->\n\n\n";
    }
    
    function Zotpress_theme_shortcode_script_footer() {
        include('zotpress.shortcode.php');
    }
    
// SHORTCODE -----------------------------------------------------------------------------------------



// SIDEBAR WIDGET -----------------------------------------------------------------------------------
    
    include('zotpress.widget.sidebar.php');

// SIDEBAR WIDGET -----------------------------------------------------------------------------------



// META BOX WIDGET ---------------------------------------------------------------------------------
    
    // backwards compatible
    add_action('admin_init', 'Zotpress_add_meta_box', 1);

    /* Adds a box to the main column on the Post and Page edit screens */
    function Zotpress_add_meta_box()
    {
        add_meta_box( 
            'ZotpressMetaBox',
            __( 'Zotpress Reference', 'Zotpress_textdomain' ),
            'Zotpress_show_meta_box',
            'post',
            'side'
        );
        add_meta_box(
            'ZotpressMetaBox',
            __( 'Zotpress Reference', 'Zotpress_textdomain' ), 
            'Zotpress_show_meta_box',
            'page',
            'side'
        );
    }
    
    /* Prints the box content */
    function Zotpress_show_meta_box()
    {
        require( 'zotpress.widget.metabox.php' );
    }

// META BOX WIDGET ---------------------------------------------------------------------------------



// REGISTER ACTIONS ---------------------------------------------------------------------------------

    function Zotpress_admin_metabox_scripts()
    {
        wp_register_script('jquery.ui.core.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.ui.core.min.js', array('jquery'));
        wp_enqueue_script('jquery.ui.core.min.js');
        
        wp_register_script('jquery.ui.widget.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.ui.widget.min.js', array('jquery'));
        wp_enqueue_script('jquery.ui.widget.min.js');
        
        wp_register_script('jquery.ui.tabs.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.ui.tabs.min.js', array('jquery'));
        wp_enqueue_script('jquery.ui.tabs.min.js');
        
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
        wp_enqueue_script('jquery.livequery.js');
    }
    
    function Zotpress_admin_scripts()
    {
        wp_enqueue_script( 'jquery' ); // For Wordpress 3.1 - TEST!
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        
        if (isset($_GET['image'])) {
            wp_register_script('zotpress.image.js', ZOTPRESS_PLUGIN_URL . 'zotpress.image.js', array('jquery','media-upload','thickbox'));
            wp_enqueue_script('zotpress.image.js');
        }
        
        if (isset($_GET['accounts'])) {
            wp_register_script('zotpress.accounts.js', ZOTPRESS_PLUGIN_URL . 'zotpress.accounts.js', array('jquery','media-upload','thickbox'));
            wp_enqueue_script('zotpress.accounts.js');
        }
        
        wp_register_script('jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array('jquery'));
        wp_enqueue_script('jquery.dotimeout.min.js');
        
        wp_register_script('jquery.qtip-1.0.0-rc3.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.qtip-1.0.0-rc3.js', array('jquery'));
        wp_enqueue_script('jquery.qtip-1.0.0-rc3.js');
        
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
        wp_enqueue_script('jquery.livequery.js');
    }
    
    function Zotpress_admin_styles()
    {
        wp_enqueue_style('thickbox');
        
        wp_register_style('zotpress.css', ZOTPRESS_PLUGIN_URL . 'zotpress.css');
        wp_enqueue_style('zotpress.css');
    }
    
    function Zotpress_admin_menu()
    {
        add_menu_page("Zotpress", "Zotpress", 3, "Zotpress", "Zotpress_options", ZOTPRESS_PLUGIN_URL."images/icon.png");
    }
    
    if (isset($_GET['page']) && $_GET['page'] == 'Zotpress') {
        add_action('admin_print_scripts', 'Zotpress_admin_scripts');
        add_action('admin_print_styles', 'Zotpress_admin_styles');
        add_action('admin_footer', 'Zotpress_admin_footer');
    }
    
    // For post and page editing only
    if (strpos( $_SERVER['SCRIPT_NAME'], "post.php" ) !== false || strpos( $_SERVER['SCRIPT_NAME'], "post-new.php" ) !== false)
        add_action('admin_print_scripts', 'Zotpress_admin_metabox_scripts');
    
    add_action('admin_menu', 'Zotpress_admin_menu');
    
    // Enqueue jQuery if it isn't already enqueued
    if (!isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] ))
        wp_enqueue_script("jquery");

    add_shortcode( 'zotpress', 'Zotpress_func' );
    add_action( 'widgets_init', 'ZotpressSidebarWidgetInit' );
    
// REGISTER ACTIONS ---------------------------------------------------------------------------------


?>