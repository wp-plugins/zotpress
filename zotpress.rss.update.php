<?php

    // Include WordPress
    require('../../../wp-load.php');
    
    if (!defined('WP_USE_THEMES'))
        define('WP_USE_THEMES', false);
    
    // Access Wordpress db
    global $wpdb;
    
    // Get existing output
    $zpcache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE instance_id='".$_GET['instance_id']."';");
    
    // Update output
    foreach ($zpcache as $key => $zpcache_instance) {
        $temp = addslashes($zpcache_instance->zpoutput);
        $temp .= htmlentities( urldecode( $_GET['output'] ) );
        $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET zpoutput='".$temp."' WHERE instance_id='".$_GET['instance_id']."';");
    }

?>