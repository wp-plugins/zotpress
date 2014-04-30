<?php

// INSTALL -----------------------------------------------------------------------------------------
    
    function Zotpress_install()
    {
        global $wpdb;
        global $Zotpress_main_db_version;
        global $Zotpress_oauth_db_version;
        global $Zotpress_zoteroItems_db_version;
        global $Zotpress_zoteroCollections_db_version;
        global $Zotpress_zoteroTags_db_version;
        
        
        // ZOTERO ACCOUNTS TABLE
        
        if (!get_option("Zotpress_main_db_version")
                || get_option("Zotpress_main_db_version") != $Zotpress_main_db_version
                //|| $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress'") != $wpdb->prefix."zotpress"
                || is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress'"))
                )
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
            
            update_option("Zotpress_main_db_version", $Zotpress_main_db_version);
        }
        
        
        // OAUTH CACHE TABLE
        
        if (!get_option("Zotpress_oauth_db_version")
                || get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version
                //|| $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_oauth'") != $wpdb->prefix."zotpress_oauth"
                || is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_oauth'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_oauth (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            update_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
            
            // Initial populate
            if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress_oauth;") == 0)
                $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_oauth (cache) VALUES ('empty')");
        }
        
        
        // ZOTERO ITEMS TABLE
        
        if (!get_option("Zotpress_zoteroItems_db_version")
                || get_option("Zotpress_zoteroItems_db_version") != $Zotpress_zoteroItems_db_version
                //|| $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroItems'") != $wpdb->prefix."zotpress_zoteroItems"
                || is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroItems'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroItems (
                id INT(9) NOT NULL AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key TEXT,
                retrieved TEXT,
                json LONGTEXT NOT NULL,
                citation LONGTEXT,
                style VARCHAR(100) DEFAULT 'apa',
                author TEXT,
                zpdate TEXT,
                title TEXT,
                itemType VARCHAR(100),
                linkMode VARCHAR(100),
                parent VARCHAR(100),
                image TEXT,
                numchildren INT,
                year VARCHAR(10) DEFAULT '1977',
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            update_option("Zotpress_zoteroItems_db_version", $Zotpress_zoteroItems_db_version);
        }
        
        
        // ZOTERO COLLECTIONS TABLE
        
        if (!get_option("Zotpress_zoteroCollections_db_version")
                || get_option("Zotpress_zoteroCollections_db_version") != $Zotpress_zoteroCollections_db_version
                //|| $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroCollections'") != $wpdb->prefix."zotpress_zoteroCollections"
                || is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroCollections'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroCollections (
                id INT(9) NOT NULL AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                title TEXT,
                retrieved VARCHAR(100),
                parent TEXT,
                item_key TEXT,
                numCollections INT(9),
                numItems INT(9),
                listItems TEXT,
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            update_option("Zotpress_zoteroCollections_db_version", $Zotpress_zoteroCollections_db_version);
        }
        
        
        // ZOTERO TAGS TABLE
        
        if (!get_option("Zotpress_zoteroTags_db_version")
                || get_option("Zotpress_zoteroTags_db_version") != $Zotpress_zoteroTags_db_version
                //|| $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroTags'") != $wpdb->prefix."zotpress_zoteroTags"
                || is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroTags'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroTags (
                id INT(9) NOT NULL UNIQUE AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                title VARCHAR(128) BINARY NOT NULL UNIQUE,
                retrieved VARCHAR(100),
                numItems INT(9),
                listItems TEXT,
                updated INT(1) DEFAULT 1,
                PRIMARY KEY (api_user_id, title)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            update_option("Zotpress_zoteroTags_db_version", $Zotpress_zoteroTags_db_version);
        }
    }
    
    /*
    add_action( 'after_setup_theme', 'zp_enable_thumbnails');
    function zp_enable_thumbnails() {
        add_theme_support( 'post-thumbnails', array( 'zp_entry' ) );
    }
    
    if ( !post_type_exists( 'zp_entry' ) ) add_action( 'init', 'zp_create_post_type' );
    function zp_create_post_type()
    {
        register_post_type( 'zp_entry',
            array(
                'label' => __( 'Zotpress Entries' ),
                'labels' => array(
                    'name' => __( 'Zotpress Entries' ),
                    'singular_name' => __( 'Zotpress Entry' )
                ),
                'description' => 'A generic content type for all Zotero items.',
                'menu_position' => 21,
                'menu_icon' => ZOTPRESS_PLUGIN_URL.'images/icon-type.png',
                'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
                'public' => true,
                'has_archive' => true
            )
        );
        
        register_taxonomy( 'zp_collections', 'zp_entry',
            array(
                'label' => 'Zotpress Collections',
                'labels' => array(
                    'name' => __( 'Zotpress Collections' ),
                    'singular_name' => __( 'Zotpress Collection' )
                ),
                'hierarchical' => true,
                'public' => true
            )
        );
        register_taxonomy_for_object_type( 'zp_collections', 'zp_entry' );
        
        register_taxonomy( 'zp_tags', 'zp_entry',
            array(
                'label' => 'Zotpress Tags',
                'labels' => array(
                    'name' => __( 'Zotpress Tags' ),
                    'singular_name' => __( 'Zotpress Tag' )
                ),
                'public' => true
            )
        );
        register_taxonomy_for_object_type( 'zp_tags', 'zp_entry' );
    }
    */

    register_activation_hook( ZOTPRESS_PLUGIN_FILE, 'Zotpress_install' );

// INSTALL -----------------------------------------------------------------------------------------



// UNINSTALL --------------------------------------------------------------------------------------

    function Zotpress_deactivate()
    {
        global $wpdb;
        
        // Drop all tables -- originally not including accounts/main, but not sure why
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
        
        // Delete options
        delete_option( 'Zotpress_DefaultCPT' );
        delete_option( 'Zotpress_DefaultAccount' );
        delete_option( 'Zotpress_LastAutoUpdate' );
        delete_option( 'Zotpress_DefaultStyle' );
        delete_option( 'Zotpress_StyleList' );
        delete_option( 'Zotpress_DefaultAutoUpdate' );
        delete_option( 'Zotpress_update_version' );
        delete_option( 'Zotpress_main_db_version' );
        delete_option( 'Zotpress_oauth_db_version' );
        delete_option( 'Zotpress_zoteroItems_db_version' );
        delete_option( 'Zotpress_zoteroCollections_db_version' );
        delete_option( 'Zotpress_zoteroTags_db_version' );
    }
    
    register_uninstall_hook( ZOTPRESS_PLUGIN_FILE, 'Zotpress_deactivate' );

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------

    if ( !get_option("Zotpress_update_version") || get_option("Zotpress_update_version") != $Zotpress_update_version )
    {
        Zotpress_install();
        
        if ( !get_option("Zotpress_update_version") ) add_option( "Zotpress_update_version", $Zotpress_update_version, "", "no" );
    }
    
// UPDATE ------------------------------------------------------------------------------------------


?>