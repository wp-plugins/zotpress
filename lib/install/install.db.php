<?php

// INSTALL -----------------------------------------------------------------------------------------
    
    function Zotpress_install()
    {
        global $wpdb;
        $Zotpress_main_db_version = "5.2";
        $Zotpress_oauth_db_version = "5.0.5";
        $Zotpress_zoteroItems_db_version = "5.0.5";
        $Zotpress_zoteroCollections_db_version = "5.0.5";
        $Zotpress_zoteroTags_db_version = "5.0.5";
        $Zotpress_zoteroRelItemColl_db_version = "5.2";
        $Zotpress_zoteroRelItemTags_db_version = "5.2";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // ZOTERO ACCOUNTS TABLE
        
        if (!get_option("Zotpress_main_db_version")
                || get_option("Zotpress_main_db_version") != $Zotpress_main_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress (
                id INT(9) NOT NULL AUTO_INCREMENT,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                public_key VARCHAR(28) default NULL,
                nickname VARCHAR(200) default NULL,
                version VARCHAR(10) default '5.1',
                UNIQUE KEY id (id)
            );";
            
            dbDelta($structure);
            
            update_option("Zotpress_main_db_version", $Zotpress_main_db_version);
        }
        
        
        // OAUTH CACHE TABLE
        
        if (!get_option("Zotpress_oauth_db_version")
                || get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_oauth'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_oauth (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            dbDelta($structure);
            
            update_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
            
            // Initial populate
            if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress_oauth;") == 0)
                $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_oauth (cache) VALUES ('empty')");
        }
        
        
        // ZOTERO ITEMS TABLE
        
        if (
                !get_option("Zotpress_zoteroItems_db_version")
                || get_option("Zotpress_zoteroItems_db_version") != $Zotpress_zoteroItems_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroItems'"))
           )
        {
			$table_name = $wpdb->prefix . "zotpress_zoteroItems";
			
            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
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
                numchildren INT(10),
                year VARCHAR(10) DEFAULT '1977',
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id)
            );";
            
            dbDelta( $structure );
            
            update_option( "Zotpress_zoteroItems_db_version", $Zotpress_zoteroItems_db_version );
        }
        
        
        // ZOTERO COLLECTIONS TABLE
        
        if (!get_option("Zotpress_zoteroCollections_db_version")
                || get_option("Zotpress_zoteroCollections_db_version") != $Zotpress_zoteroCollections_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroCollections'"))
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
            
            dbDelta($structure);
            
            update_option("Zotpress_zoteroCollections_db_version", $Zotpress_zoteroCollections_db_version);
        }
        
        
        // ZOTERO TAGS TABLE
        
        if (!get_option("Zotpress_zoteroTags_db_version")
                || get_option("Zotpress_zoteroTags_db_version") != $Zotpress_zoteroTags_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroTags'"))
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
            
            dbDelta($structure);
            
            update_option("Zotpress_zoteroTags_db_version", $Zotpress_zoteroTags_db_version);
        }
        
        
        // ZOTERO RELATIONSHIP TABLE FOR ITEMS AND COLLECTIONS
        
        if (!get_option("Zotpress_zoteroRelItemColl_db_version")
                || get_option("Zotpress_zoteroRelItemColl_db_version") != $Zotpress_zoteroRelItemColl_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroRelItemColl'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroRelItemColl (
                id INT(9) NOT NULL UNIQUE AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                collection_key VARCHAR(50),
                PRIMARY KEY (id)
            );";
            
            dbDelta($structure);
            
            update_option("Zotpress_zoteroRelItemColl_db_version", $Zotpress_zoteroRelItemColl_db_version);
        }
        
        
        
        // ZOTERO RELATIONSHIP TABLE FOR ITEMS AND TAGS
        
        if (!get_option("Zotpress_zoteroRelItemTags_db_version")
                || get_option("Zotpress_zoteroRelItemTags_db_version") != $Zotpress_zoteroRelItemTags_db_version
                //|| is_null($wpdb->get_var("SELECT COUNT(*) FROM '".$wpdb->prefix."zotpress_zoteroRelItemTags'"))
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroRelItemTags (
                id INT(9) NOT NULL UNIQUE AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                tag_title VARCHAR(128),
                PRIMARY KEY (id)
            );";
            
            dbDelta($structure);
            
            update_option("Zotpress_zoteroRelItemTags_db_version", $Zotpress_zoteroRelItemTags_db_version);
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
        global $current_user;
        
        // Drop all tables -- originally not including accounts/main, but not sure why
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemColl;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemTags;");
        
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
        delete_option( 'Zotpress_zoteroRelItemColl_db_version' );
        delete_option( 'Zotpress_zoteroRelItemTags_db_version' );
        
        // Delete user meta
        delete_user_meta( $current_user->ID, 'zotpress_5_2_ignore_notice' );
    }
    
    register_uninstall_hook( ZOTPRESS_PLUGIN_FILE, 'Zotpress_deactivate' );

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------


    if ( !get_option( "Zotpress_update_version" )
            || get_option("Zotpress_update_version") != $GLOBALS['Zotpress_update_version'] )
    {
        Zotpress_install();
        
        // Add or update version number
        if ( !get_option( "Zotpress_update_version" ) ) {
            add_option( "Zotpress_update_version", $GLOBALS['Zotpress_update_version'], "", "no" );
        }
        else {
            update_option( "Zotpress_update_version", $GLOBALS['Zotpress_update_version'] );
        }
    }
    
// UPDATE ------------------------------------------------------------------------------------------


?>