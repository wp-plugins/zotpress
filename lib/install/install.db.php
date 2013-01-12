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
        
        
        // ACCOUNTS TABLE
        
        if (!get_option("Zotpress_main_db_version")
                || get_option("Zotpress_main_db_version") != $Zotpress_main_db_version
                || $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress'") != $wpdb->prefix."zotpress"
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
                || $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_oauth'") != $wpdb->prefix."zotpress_oauth"
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
                || $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroItems'") != $wpdb->prefix."zotpress_zoteroItems"
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
                || $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroCollections'") != $wpdb->prefix."zotpress_zoteroCollections"
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
                || $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."zotpress_zoteroTags'") != $wpdb->prefix."zotpress_zoteroTags"
                )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_zoteroTags (
                id INT(9) NOT NULL AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                title TEXT,
                retrieved VARCHAR(100),
                numItems INT(9),
                listItems TEXT,
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            update_option("Zotpress_zoteroTags_db_version", $Zotpress_zoteroTags_db_version);
        }
    }

    register_activation_hook(ZOTPRESS_PLUGIN_FILE, 'Zotpress_install');

// INSTALL -----------------------------------------------------------------------------------------



// UNINSTALL --------------------------------------------------------------------------------------

    function Zotpress_deactivate()
    {
        global $wpdb;
        
        // Drop all tables except accounts/main
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
    }
    
    //register_deactivation_hook(ZOTPRESS_PLUGIN_FILE, 'Zotpress_deactivate');
    register_uninstall_hook(ZOTPRESS_PLUGIN_FILE, 'Zotpress_deactivate');

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------
    
    function Zotpress_update()
    {
        global $Zotpress_main_db_version;
        global $Zotpress_oauth_db_version;
        global $Zotpress_zoteroItems_db_version;
        global $Zotpress_zoteroCollections_db_version;
        global $Zotpress_zoteroTags_db_version;
        
        if (
            get_option("Zotpress_main_db_version") != $Zotpress_main_db_version
            || get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version
            || get_option("Zotpress_zoteroItems_db_version") != $Zotpress_zoteroItems_db_version
            || get_option("Zotpress_zoteroCollections_db_version") != $Zotpress_zoteroCollections_db_version
            || get_option("Zotpress_zoteroTags_db_version") != $Zotpress_zoteroTags_db_version
            )
        {
            Zotpress_install();
        }
    }
    
    add_action('plugins_loaded', 'Zotpress_update');
    
// UPDATE ------------------------------------------------------------------------------------------


?>