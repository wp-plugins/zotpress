<?php

    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);
    
    global $wpdb;
    
    header('Content-type: text/html; charset=utf-8');
    
    // Determine account
    if (get_option("Zotpress_DefaultAccount"))
    {
        $zp_api_user_id = get_option("Zotpress_DefaultAccount");
    }
    else
    {
        $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
        $zp_api_user_id = $zp_account->api_user_id;
    }

    
    $zpSearchResults = $wpdb->get_results(
        $wpdb->prepare( 
            "
                SELECT CONCAT(author, ' (', year, ') ', title) AS label, item_key AS value FROM ".$wpdb->prefix."zotpress_zoteroItems
                WHERE api_user_id='".$zp_api_user_id."' AND json LIKE %s AND author != '' ORDER BY author ASC
            ", 
            '%' . like_escape($_GET['term']) . '%'
    ), OBJECT );
    
    print json_encode($zpSearchResults);
    
    unset($zp_api_user_id);
    unset($zp_account);
    $wpdb->flush();

?>