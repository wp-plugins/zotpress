<?php

/*
 
    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Display your Zotero citations on your Wordpress blog.
    Author: Katie Seaborn
    Version: 4.4
    Author URI: http://katieseaborn.com
    
*/


// GLOBAL VARS ----------------------------------------------------------------------------------
    
    define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    
    $GLOBALS['zp_is_shortcode_displayed'] = false;
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
        <?php include('zotpress.admin.display.filter.php'); ?>
        
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
            // Check vars
            $zp_citation_id = false;
            if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['citation_id']))
                $zp_citation_id = htmlentities(trim($_GET['citation_id']));
                
            if ($zp_citation_id !== false)
            {
                global $wpdb;
                
                $zp_image = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_images WHERE citation_id='".$zp_citation_id."'");
                
                include('zotpress.image.php');
            }
        }
        
        
        
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include('zotpress.admin.help.php');
        }
        
        
        
        // ADMIN CITATIONS VIEW
        
        else
        {
            include('zotpress.admin.default.php');
        }
    }

// ADMIN -----------------------------------------------------------------------------------------



// SHORTCODE -----------------------------------------------------------------------------------------

    include('zotpress.shortcode.php');
    include('zotpress.shortcode.intext.php');
    
// SHORTCODE -----------------------------------------------------------------------------------------



// SIDEBAR WIDGET -----------------------------------------------------------------------------------
    
    include('zotpress.widget.sidebar.php');

// SIDEBAR WIDGET -----------------------------------------------------------------------------------



// META BOX WIDGET ---------------------------------------------------------------------------------
    
    add_action('admin_init', 'Zotpress_add_meta_box', 1); // backwards compatible

    /* Adds a box to the main column on the Post and Page edit screens */
    function Zotpress_add_meta_box()
    {
        $post_types=get_post_types('','names');
        
        foreach ($post_types as $post_type )
        {
            add_meta_box( 
                'ZotpressMetaBox',
                __( 'Zotpress Reference', 'Zotpress_textdomain' ),
                'Zotpress_show_meta_box',
                $post_type,
                'side'
            );
        }
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
        
        if (isset($_GET['image']) || isset($_GET['loaded'])) {
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
    
    function Zotpress_theme_styles()
    {
        wp_register_style('zotpress.shortcode.css', ZOTPRESS_PLUGIN_URL . 'zotpress.shortcode.css');
        wp_enqueue_style('zotpress.shortcode.css');
        
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
        wp_enqueue_script('jquery.livequery.js');
    }
    
    /* ADD ACTIONS */
    
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

    // Shortcodes and sidebar widget
    add_shortcode( 'zotpress', 'Zotpress_func' );
    add_shortcode( 'zotpressInText', 'Zotpress_zotpressInText' );
    add_action( 'widgets_init', 'ZotpressSidebarWidgetInit' );
    
    // Include styles of shortcode displayed
    add_action( 'wp_print_styles', 'Zotpress_theme_styles' );
    
// REGISTER ACTIONS ---------------------------------------------------------------------------------


?>