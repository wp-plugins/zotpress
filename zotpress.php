<?php

/*
 
    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Bringing Zotero and scholarly blogging to your WordPress website.
    Author: Katie Seaborn
    Version: 5.2.7
    Author URI: http://katieseaborn.com
    
*/

/*
 
    Copyright 2014 Katie Seaborn
    
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
    
    //add_option( 'ZOTPRESS_PASSCODE', substr(number_format(time() * rand(),0,'',''),0,10) ); /* Thanks to http://elementdesignllc.com/2011/06/generate-random-10-digit-number-in-php/ */
    
    define('ZOTPRESS_PLUGIN_FILE',  __FILE__ );
    define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( ZOTPRESS_PLUGIN_FILE ));
    define('ZOTPRESS_PLUGIN_DIR', dirname( __FILE__ ));
    define('ZOTPRESS_EXPERIMENTAL_EDITOR', FALSE); // Whether experimental editor feature is active or not
    define('ZOTPRESS_VERSION', '5.2.6' );
    
    $GLOBALS['zp_is_shortcode_displayed'] = false;
    $GLOBALS['zp_shortcode_instances'] = array();
    
    $GLOBALS['Zotpress_update_db_by_version'] = "5.2.5"; // Only change if updating db

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
    
    function Zotpress_add_meta_box()
    {
        $zp_default_cpt = "post,page";
        if (get_option("Zotpress_DefaultCPT"))
            $zp_default_cpt = get_option("Zotpress_DefaultCPT");
        $zp_default_cpt = explode(",",$zp_default_cpt);
        
        foreach ($zp_default_cpt as $post_type )
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
    add_action('admin_init', 'Zotpress_add_meta_box', 1); // backwards compatible
    
    function Zotpress_show_meta_box()
    {
        require("lib/widget/widget.metabox.php");
    }
    
// META BOX WIDGET ---------------------------------------------------------------------------------



// REGISTER ACTIONS ---------------------------------------------------------------------------------
    
    /**
    * Admin scripts and styles
    */
    function Zotpress_admin_scripts_css($hook)
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_media();
        wp_enqueue_script( 'jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'zotpress.default.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.default.js', array( 'jquery' ) );
        
        if ( 'post.php' == $hook )
        {
            wp_enqueue_script( 'jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-autocomplete' ) );
            wp_enqueue_script( 'zotpress.widget.metabox.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.metabox.js', array( 'jquery' ) );
        }
        else
        {
            wp_enqueue_script( 'jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array( 'jquery' ) );
        }
        
        if ( isset($_GET['accounts']) || isset($_GET['setup']) || isset($_GET['import']) || isset($_GET['selective']) )
        {
            wp_register_script('zotpress.accounts.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.accounts.js', array('jquery','media-upload','thickbox'));
            wp_enqueue_script('zotpress.accounts.js');
        }
        
        wp_enqueue_style( 'zotpress.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.css' );
        wp_enqueue_style( 'ZotpressGoogleFonts.css', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,600|Droid+Serif:400,400italic,700italic|Oswald:300,400' );
    }
    add_action( 'admin_enqueue_scripts', 'Zotpress_admin_scripts_css' );
    
    
    /**
    * Add Zotpress to admin menu
    */
    function Zotpress_admin_menu()
    {
        add_menu_page("Zotpress", "Zotpress", "edit_posts", "Zotpress", "Zotpress_options", ZOTPRESS_PLUGIN_URL."images/icon.png");
    }
    add_action( 'admin_menu', 'Zotpress_admin_menu' );
    
    
    /**
    * Add shortcode styles to user's theme
    * Note this always displays: there's no way to conditionally display, because shortcodes are checked after css is included
    */
    function Zotpress_theme_includes()
    {
        wp_register_style('zotpress.shortcode.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.shortcode.css');
        wp_enqueue_style('zotpress.shortcode.css');
    }
    add_action('wp_print_styles', 'Zotpress_theme_includes');
    
    
    /**
    * Change HTTP request timeout
    */
    function Zotpress_change_timeout($time)
    {
        return 60; // seconds
    }
    add_filter('http_request_timeout', 'Zotpress_change_timeout');
    
    
    /**
    * Testing: Alternative button registration
    */
    function zotpress_buttonhooks()
    {
        // Determine default editor features status
        $zp_default_editor = "editor_enable";
        if (get_option("Zotpress_DefaultEditor")) $zp_default_editor = get_option("Zotpress_DefaultEditor");
        
        if ( ( 'post.php' != $hook || 'page.php' != $hook ) && $zp_default_editor != 'editor_enable' )
            return;
        
        // Only add hooks when the current user has permissions AND is in Rich Text editor mode
        if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') )
        {
            add_filter("mce_external_plugins", "zotpress_register_tinymce_javascript");
            add_filter("mce_buttons", "zotpress_register_tinymce_buttons");
        }
    }
   if ( ZOTPRESS_EXPERIMENTAL_EDITOR ) add_action('init', 'zotpress_buttonhooks');
    
    // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
    function zotpress_register_tinymce_javascript($plugin_array)
    {
        $plugin_array['zotpress'] = plugins_url('/lib/tinymce-plugin/zotpress-tinymce-plugin.js', __FILE__);
        return $plugin_array;
    }
    
    function zotpress_register_tinymce_buttons($buttons)
    {
        array_push($buttons, "zotpress-cite", "zotpress-list", "zotpress-bib" );
        return $buttons;
    }
   
   
    /**
    * Metabox styles
    */
    function Zotpress_admin_post_styles()
    {
        wp_register_style('zotpress.metabox.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.metabox.css');
        wp_enqueue_style('zotpress.metabox.css');
        
        wp_enqueue_style('jquery-ui-tabs', ZOTPRESS_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom.css');
    }
    add_action('admin_print_styles-post.php', 'Zotpress_admin_post_styles');
    add_action('admin_print_styles-post-new.php', 'Zotpress_admin_post_styles');
    
    
    // CKEDITOR SCRIPTS & STYLES
    // In progress and experimental
    
    //function Zotpress_admin_editor_scripts()
    //{
    //    //wp_register_script('zotpress.widget.ckeditor.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.ckeditor.js', array('jquery'));
    //    //wp_enqueue_script('zotpress.widget.ckeditor.js');
    //}
    
    //function Zotpress_admin_ckeditor_css()
    //{
    //    wp_register_style('zotpress.ckeditor.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.ckeditor.css');
    //    wp_enqueue_style('zotpress.ckeditor.css');
    //}
    
    
    // Enqueue jQuery in theme if it isn't already enqueued
    // Thanks to WordPress user "eceleste"
    //if (!isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] )) wp_enqueue_script("jquery");
    function Zotpress_enqueue_scripts()
    {
        if (!isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] )) wp_enqueue_script("jquery");
    }
    add_action( 'wp_enqueue_scripts' , 'Zotpress_enqueue_scripts' );

    // Add shortcodes and sidebar widget
    add_shortcode( 'zotpress', 'Zotpress_func' );
    add_shortcode( 'zotpressInText', 'Zotpress_zotpressInText' );
    add_shortcode( 'zotpressInTextBib', 'Zotpress_zotpressInTextBib' );
    add_action( 'widgets_init', 'ZotpressSidebarWidgetInit' );
    
    // Conditionally serve shortcode scripts
    function Zotpress_theme_conditional_scripts_footer()
    {
        if ( $GLOBALS['zp_is_shortcode_displayed'] === true )
        {
            if ( !is_admin() ) wp_enqueue_script('jquery');
            wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
            wp_enqueue_script('jquery.livequery.js');
			
			wp_enqueue_script("jquery-effects-core");
			wp_enqueue_script("jquery-effects-highlight");
            
            wp_register_script('zotpress.frontend.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.frontend.js', array('jquery'));
            wp_enqueue_script('zotpress.frontend.js');
        }
    }
    add_action('wp_footer', 'Zotpress_theme_conditional_scripts_footer');
    
    
	
    // 5.2 - Notice of required re-import
    // Thanks to http://wptheming.com/2011/08/admin-notices-in-wordpress/
    
    function zotpress_5_2_admin_notice()
    {
        global $wpdb;
        global $current_user;
        
        // See if any accounts are the old version
        $temp_version_count =
                $wpdb->get_var( "SELECT COUNT(version) FROM ".$wpdb->prefix."zotpress
                                            WHERE version != '".$GLOBALS['Zotpress_update_db_by_version']."';" );
        
        if ( $temp_version_count > 0
                && !get_user_meta($current_user->ID, 'zotpress_5_2_ignore_notice')
                && ( current_user_can('edit_posts') || current_user_can('edit_pages') )
                && ( !isset($_GET['setup']) && !isset($_GET['selective']) && !isset($_GET['import']) )
            )
        {
            echo '<div class="error"><p>';
            printf(__('<strong>URGENT:</strong> Due to major changes in Zotpress, your Zotero account(s) need to be <a href="admin.php?page=Zotpress&accounts=true">re-imported</a>. | <a href="%1$s">Hide Notice</a>'), 'admin.php?page=Zotpress&zotpress_5_2_ignore=0');
            echo "</p></div>";
        }
    }
    add_action( 'admin_notices', 'zotpress_5_2_admin_notice' );
    
    function zotpress_5_2_ignore()
    {
        global $current_user;
        if ( isset($_GET['zotpress_5_2_ignore']) && $_GET['zotpress_5_2_ignore'] == '0' )
            add_user_meta($current_user->ID, 'zotpress_5_2_ignore_notice', 'true', true);
    }
    add_action('admin_init', 'zotpress_5_2_ignore');
	
	
    
    // 5.2.7 Notice of Zotpress survey
    
    function zotpress_survey_notice()
    {
        global $current_user;
		
		if ( !get_user_meta($current_user->ID, 'zotpress_survey_notice_ignore') )
		{
			echo '<div class="updated"><p>';
			printf(__('Would you like to participate in research on Zotpress? <a title="Zotpress survey" href="http://imdc.ca/survey/776698/" target="_blank">Take the survey!</a> | <a href="%1$s">Hide Notice</a>'), 'admin.php?page=Zotpress&zotpress_survey_notice_ignore=0');
			echo "</p></div>";
		}
    }
    add_action( 'admin_notices', 'zotpress_survey_notice' );
    
    function zotpress_survey_notice_ignore()
    {
        global $current_user;
        if ( isset($_GET['zotpress_survey_notice_ignore']) && $_GET['zotpress_survey_notice_ignore'] == '0' )
            add_user_meta($current_user->ID, 'zotpress_survey_notice_ignore', 'true', true);
    }
    add_action('admin_init', 'zotpress_survey_notice_ignore');
    
// REGISTER ACTIONS ---------------------------------------------------------------------------------


?>