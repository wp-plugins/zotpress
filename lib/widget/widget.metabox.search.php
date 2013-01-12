<?php

    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);
    
    global $wpdb;
    
    header('Content-type: text/html; charset=utf-8');
    
    $zpSearchResults = $wpdb->get_results(
        $wpdb->prepare( 
            "
                SELECT CONCAT(author, ' (', year, ') ', title) AS label, item_key AS value FROM ".$wpdb->prefix."zotpress_zoteroItems
                WHERE json LIKE %s AND author != '' ORDER BY author ASC
            ", 
            '%' . like_escape($_GET['term']) . '%'
    ), OBJECT );
    
    print json_encode($zpSearchResults);
    
    $wpdb->flush();

?>