<?php

/*
 
    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Display your Zotero collection on your Wordpress blog.
    Author: Katie Seaborn
    Version: 1.0
    Author URI: http://katieseaborn.com
    
*/

define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ));


// INSTALL -----------------------------------------------------------------------------------------
    
    function Zotpress_activate()
    {
        global $wpdb;

        $structure = "CREATE TABLE " . $wpdb->prefix . "zotpress (
            id INT(9) NOT NULL AUTO_INCREMENT,
            account_type VARCHAR(10) NOT NULL,
            api_user_id VARCHAR(10) NOT NULL,
            public_key VARCHAR(28) default NULL,
            nickname VARCHAR(200) default NULL,
            UNIQUE KEY id (id)
        );";
        $wpdb->query($structure);

        $structure = "CREATE TABLE " . $wpdb->prefix . "zotpress_images (
            id INT(9) NOT NULL AUTO_INCREMENT,
            citation_id VARCHAR(10) NOT NULL,
            image VARCHAR(300) NOT NULL,
            account_type VARCHAR(10) NOT NULL,
            api_user_id VARCHAR(10) NOT NULL,
            UNIQUE KEY id (id)
        );";
        $wpdb->query($structure);
    }

    register_activation_hook(__FILE__, 'Zotpress_activate');

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -----------------------------------------------------------------------------------------
    
    function Zotpress_admin_footer()
    {
        // Connect to database
        global $wpdb;
        
        $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
            
        $zp_accounts_total = $wpdb->num_rows;
        
        if ($zp_accounts_total > 0)
        {
            ?>
<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        <?php
            include('zotpress.display.essentials.php');
            include('zotpress.display.php');
        ?>
    });
    
</script>
<?php

        }
    }
    
    function Zotpress_admin_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('zotpress.actions.js', ZOTPRESS_PLUGIN_URL . 'zotpress.actions.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_script('zotpress.actions.js');
        wp_register_script('jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'jquery.dotimeout.min.js', array('jquery'));
        wp_enqueue_script('jquery.dotimeout.min.js');
        wp_register_script('jquery.qtip-1.0.0-rc3.js', ZOTPRESS_PLUGIN_URL . 'jquery.qtip-1.0.0-rc3.js', array('jquery'));
        wp_enqueue_script('jquery.qtip-1.0.0-rc3.js');
        wp_register_script('jquery.livequery.js', ZOTPRESS_PLUGIN_URL . 'jquery.livequery.js', array('jquery'));
        wp_enqueue_script('jquery.livequery.js');
    }
    
    function Zotpress_admin_styles() {
        wp_enqueue_style('thickbox');
        wp_register_style('zotpress.css', ZOTPRESS_PLUGIN_URL . 'zotpress.css');
        wp_enqueue_style('zotpress.css');
    }
    
    function Zotpress_admin_menu()
    {
        add_menu_page("Zotpress", "Zotpress", 3, "Zotpress", "Zotpress_options", ZOTPRESS_PLUGIN_URL."icon.png");
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
            include('zotpress.image.php');
        }
            
            
            
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include('zotpress.help.php');
        }
            
            
            
        // VIEW CITATIONS
        
        else
        {
            global $wpdb;
            
                    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
            
        $zp_accounts_total = $wpdb->num_rows;
            include('zotpress.default.php');
        }
        
        
    }

// ADMIN -----------------------------------------------------------------------------------------



// SHORTCODE -----------------------------------------------------------------------------------------

    function Zotpress_func($atts) {
            extract(shortcode_atts(array(
                
                    'api_user_id' => false,
                    'nickname' => false,
                    'author' => false,
                    
                    'data_type' => "items",
                    
                    'collection_id' => false,
                    'item_key' => false,
                    'tag_name' => false,
                    
                    'content' => "bib",
                    'style' => "apa",
                    'order' => false,
                    'sort' => false,
                    'limit' => "50",
                    
                    'image' => "no"
                    
            ), $atts));
            
            // Format attritbutes
            $api_user_id = str_replace('"','',html_entity_decode($api_user_id));
            $nickname = str_replace('"','',html_entity_decode($nickname));
            $author = str_replace('"','',html_entity_decode($author));
            
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
            
            // Connect to database
            global $wpdb;
            
            if ($api_user_id != false)
                $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'");
            else if ($nickname != false)
                $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'");
            else
                $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
                
            $zp_accounts_total = $wpdb->num_rows;
            
            if ($zp_accounts_total > 0)
            {
                include('zotpress.shortcode.php');
                return "<div id='zp-Zotpress'><span class='zp-Loading'><span>loading</span></span></div>\n";
            }
    }
    
// SHORTCODE -----------------------------------------------------------------------------------------


// REGISTER ACTIONS ---------------------------------------------------------------------------------

    if (isset($_GET['page']) && $_GET['page'] == 'Zotpress') {
        add_action('admin_print_scripts', 'Zotpress_admin_scripts');
        add_action('admin_print_styles', 'Zotpress_admin_styles');
        add_action('admin_footer', 'Zotpress_admin_footer');
    }
    
    add_action('admin_menu', 'Zotpress_admin_menu');

    add_shortcode('zotpress', 'Zotpress_func');
    
// REGISTER ACTIONS ---------------------------------------------------------------------------------


?>