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
        
        if (!get_option("Zotpress_main_db_version") || get_option("Zotpress_main_db_version") != $Zotpress_main_db_version)
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
            
            if (!get_option("Zotpress_main_db_version"))
                add_option("Zotpress_main_db_version", $Zotpress_main_db_version);
            else if (get_option("Zotpress_main_db_version") != $Zotpress_main_db_version)
                update_option("Zotpress_main_db_version", $Zotpress_main_db_version);
        }
        
        
        // OAUTH CACHE TABLE
        
        if (!get_option("Zotpress_oauth_db_version") || get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version)
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_oauth (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($structure);
            
            if (!get_option("Zotpress_oauth_db_version"))
                add_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
            else if (get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version)
                update_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);
            
            // Initial populate
            $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_oauth (cache) VALUES ('empty')");
        }
        
        
        // ZOTERO ITEMS TABLE
        
        if (!get_option("Zotpress_zoteroItems_db_version") || get_option("Zotpress_zoteroItems_db_version") != $Zotpress_zoteroItems_db_version)
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
            
            if (!get_option("Zotpress_zoteroItems_db_version"))
                add_option("Zotpress_zoteroItems_db_version", $Zotpress_zoteroItems_db_version);
            else if (get_option("Zotpress_zoteroItems_db_version") != $Zotpress_zoteroItems_db_version)
                update_option("Zotpress_zoteroItems_db_version", $Zotpress_zoteroItems_db_version);
        }
        
        
        // ZOTERO COLLECTIONS TABLE
        
        if (!get_option("Zotpress_zoteroCollections_db_version") || get_option("Zotpress_zoteroCollections_db_version") != $Zotpress_zoteroCollections_db_version)
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
            
            if (!get_option("Zotpress_zoteroCollections_db_version"))
                add_option("Zotpress_zoteroCollections_db_version", $Zotpress_zoteroCollections_db_version);
            else if (get_option("Zotpress_zoteroCollections_db_version") != $Zotpress_zoteroCollections_db_version)
                update_option("Zotpress_zoteroCollections_db_version", $Zotpress_zoteroCollections_db_version);
        }
        
        
        // ZOTERO TAGS TABLE
        
        if (!get_option("Zotpress_zoteroTags_db_version") || get_option("Zotpress_zoteroTags_db_version") != $Zotpress_zoteroTags_db_version)
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
            
            if (!get_option("Zotpress_zoteroTags_db_version"))
                add_option("Zotpress_zoteroTags_db_version", $Zotpress_zoteroTags_db_version);
            else if (get_option("Zotpress_zoteroTags_db_version") != $Zotpress_zoteroTags_db_version)
                update_option("Zotpress_zoteroTags_db_version", $Zotpress_zoteroTags_db_version);
        }
    }

    register_activation_hook(__FILE__, 'Zotpress_install');

// INSTALL -----------------------------------------------------------------------------------------



// INITIALIZE --------------------------------------------------------------------------------------

    //if ( !get_option('Zotpress_initialized') && !$wpcom_api_key && !isset($_POST['submit']) && !isset($_GET['setup']) && strpos(strtolower($_SERVER['REQUEST_URI']), 'zotpress') === false )
    //{
    //    function Zotpress_initialize_msg()
    //    {
    //        
    //        echo "<div id='akismet-warning' class='updated fade'><p><strong>Zotpress is almost ready.</strong> You must <a href='admin.php?page=Zotpress&setup=true'>sync your Zotero account</a> to get started.</p></div>";
    //    }
    //    
    //    add_action('admin_notices', 'Zotpress_initialize_msg');
    //}

// INITIALIZE ---------------------------------------------------------------------------------------


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