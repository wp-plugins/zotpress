<?php

/*
 
    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Bring Zotero and scholarly blogging to your Wordpress site.
    Author: Katie Seaborn
    Version: 5.0.6
    Author URI: http://katieseaborn.com
    
*/

/*
 
    Copyright 2013 Katie Seaborn
    
    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at
    
        http://www.apache.org/licenses/LICENSE-2.0
    
    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
    
*/



// GLOBAL VARS ----------------------------------------------------------------------------------
    
    define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    define('ZOTPRESS_PLUGIN_FILE',  __FILE__ );
    
    $GLOBALS['zp_is_shortcode_displayed'] = false;
    $GLOBALS['zp_shortcode_instances'] = array();
    
    $Zotpress_main_db_version = "5.0.4";
    $Zotpress_oauth_db_version = "5.0.4";
    $Zotpress_zoteroItems_db_version = "5.0.4";
    $Zotpress_zoteroCollections_db_version = "5.0.4";
    $Zotpress_zoteroTags_db_version = "5.0.4";

// GLOBAL VARS ----------------------------------------------------------------------------------
    


// INSTALL -----------------------------------------------------------------------------------------

    include("lib/install/install.db.php");

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -------------------------------------------------------------------------------------------
    
    include("lib/admin/admin.php");

// END ADMIN --------------------------------------------------------------------------------------



// SHORTCODE -------------------------------------------------------------------------------------

    include("lib/shortcode/shortcode.php");
    include("lib/shortcode/shortcode.intext.php");
    include("lib/shortcode/shortcode.intextbib.php");
    
// SHORTCODE -------------------------------------------------------------------------------------



// SIDEBAR WIDGET -------------------------------------------------------------------------------
    
    include("lib/widget/widget.sidebar.php");

// SIDEBAR WIDGET -------------------------------------------------------------------------------



// META BOX WIDGET -----------------------------------------------------------------------------
    
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
        require("lib/widget/widget.metabox.php");
    }

// META BOX WIDGET ---------------------------------------------------------------------------------



// REGISTER ACTIONS ---------------------------------------------------------------------------------
    
    function Zotpress_admin_metabox_scripts()
    {
        // Requires ui core, widget, position, autocomplete, tabs
        $zp_jquery_dependencies = array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-autocomplete');
        
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', $zp_jquery_dependencies);
        wp_enqueue_script('jquery.livequery.js');
        
        wp_register_script('jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array('jquery'));
        wp_enqueue_script('jquery.dotimeout.min.js');
        
        wp_register_script('zotpress.widget.metabox.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.metabox.js', array('jquery'));
        wp_enqueue_script('zotpress.widget.metabox.js');
    }
    
    // EDITOR SCRIPTS & STYLES
    // In progress, and experimental
    
    //function Zotpress_admin_editor_scripts()
    //{
    //    wp_register_script('zotpress.widget.tinymce.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.tinymce.js', array('jquery'));
    //    wp_enqueue_script('zotpress.widget.tinymce.js');
    //    
    //    //wp_register_script('zotpress.widget.ckeditor.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.ckeditor.js', array('jquery'));
    //    //wp_enqueue_script('zotpress.widget.ckeditor.js');
    //}
    
    //function Zotpress_admin_ckeditor_css()
    //{
    //    wp_register_style('zotpress.ckeditor.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.ckeditor.css');
    //    wp_enqueue_style('zotpress.ckeditor.css');
    //}
    
    
    // GENERAL SCRIPTS & STYLES
    
    function Zotpress_admin_scripts()
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        
        wp_register_script('zotpress.image.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.image.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_script('zotpress.image.js');
        
        if (isset($_GET['accounts']) || isset($_GET['setup'])) {
            wp_register_script('zotpress.accounts.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.accounts.js', array('jquery','media-upload','thickbox'));
            wp_enqueue_script('zotpress.accounts.js');
        }
        
        wp_register_script('jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array('jquery'));
        wp_enqueue_script('jquery.dotimeout.min.js');
        
        wp_register_script('jquery.qtip.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.qtip.min.js', array('jquery'));
        wp_enqueue_script('jquery.qtip.min.js');
        
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
        wp_enqueue_script('jquery.livequery.js');
        
        wp_register_script('zotpress.default.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.default.js', array('jquery'));
        wp_enqueue_script('zotpress.default.js');
    }
    
    function Zotpress_admin_styles()
    {
        wp_enqueue_style('thickbox');
        
        wp_register_style('zotpress.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.css');
        wp_enqueue_style('zotpress.css');
        
        wp_register_style('jquery.qtip.min.css', ZOTPRESS_PLUGIN_URL . 'css/jquery.qtip.min.css');
        wp_enqueue_style('jquery.qtip.min.css');
        
        wp_register_style('Monda.css', 'http://fonts.googleapis.com/css?family=Droid+Serif:400,400italic,700italic|Oswald:400,300');
        wp_enqueue_style('Monda.css');
    }
    
    function Zotpress_admin_post_styles()
    {
        wp_register_style('zotpress.metabox.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.metabox.css');
        wp_enqueue_style('zotpress.metabox.css');
        
        wp_enqueue_style('jquery-ui-tabs', ZOTPRESS_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom.css');
    }
    
    function Zotpress_admin_menu()
    {
        add_menu_page("Zotpress", "Zotpress", 3, "Zotpress", "Zotpress_options", ZOTPRESS_PLUGIN_URL."images/icon.png");
    }
    
    function Zotpress_theme_includes()
    {
        // Always displays: there's no way to conditionally display, because shortcodes are checked after css is included
        wp_register_style('zotpress.shortcode.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.shortcode.css');
        wp_enqueue_style('zotpress.shortcode.css');
    }
    
    // SESSION HANDLING
    // Thanks to http://devondev.com/2012/02/03/using-the-php-session-in-wordpress/
    
    function Zotpress_import_session()
    {
        if (!session_id()) { session_start(); }
    }
    
    function Zotpress_import_session_end()
    {
        //session_destroy(); // just in case there's more than one session
        if (!session_id()) { session_start(); }
        unset($_SESSION['zp_session']);
    }
    
    /*
     
        ADD ACTIONS
        
    */
    
    if (isset($_GET['page']) && $_GET['page'] == 'Zotpress')
    {
        add_action('init', 'Zotpress_import_session');
        
        add_action('admin_print_scripts', 'Zotpress_admin_scripts');
        add_action('admin_print_styles', 'Zotpress_admin_styles');
        add_action('admin_footer', 'Zotpress_admin_footer');
    }
    
    add_action('wp_logout', 'Zotpress_import_session_end');
    add_action('wp_login', 'Zotpress_import_session_end');
    
    // For post and page editing and CKEDITOR only
    if (
            strpos( $_SERVER['SCRIPT_NAME'], "post.php" ) !== false
            || strpos( $_SERVER['SCRIPT_NAME'], "zotpress.widget.ckeditor.php" ) !== false
            || strpos( $_SERVER['SCRIPT_NAME'], "post-new.php" ) !== false
        )
    {
        add_action('admin_print_scripts', 'Zotpress_admin_metabox_scripts');
        //add_action('admin_footer', 'Zotpress_admin_editor_scripts');
        add_action('admin_print_styles', 'Zotpress_admin_post_styles');
        //add_action('admin_print_styles', 'Zotpress_admin_ckeditor_css');
    }
    
    add_action('admin_menu', 'Zotpress_admin_menu');
    
    // Enqueue jQuery in theme if it isn't already enqueued
    if (!isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] ))
        wp_enqueue_script("jquery");

    // Shortcodes and sidebar widget
    add_shortcode( 'zotpress', 'Zotpress_func' );
    add_shortcode( 'zotpressInText', 'Zotpress_zotpressInText' );
    add_shortcode( 'zotpressInTextBib', 'Zotpress_zotpressInTextBib' );
    add_action( 'widgets_init', 'ZotpressSidebarWidgetInit' );
    
    // Conditionally serve shortcode scripts
    function Zotpress_theme_conditional_scripts_footer()
    {
        if ( $GLOBALS['zp_is_shortcode_displayed'] === true)
        {
            if (!is_admin()) {
                wp_enqueue_script('jquery');
            }
            wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
            wp_enqueue_script('jquery.livequery.js');
            
            wp_register_script('zotpress.autoupdate.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.autoupdate.js', array('jquery'));
            wp_enqueue_script('zotpress.autoupdate.js');
        }
    }

    add_action('wp_footer', 'Zotpress_theme_conditional_scripts_footer');
    add_action('wp_print_styles', 'Zotpress_theme_includes');
    
    // Metabox
    add_action('admin_print_styles-post.php', 'Zotpress_admin_post_styles');
    add_action('admin_print_styles-post-new.php', 'Zotpress_admin_post_styles');
    
// REGISTER ACTIONS ---------------------------------------------------------------------------------


?>