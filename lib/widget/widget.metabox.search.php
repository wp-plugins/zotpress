<?php

    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
    
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
                SELECT author, json, CONCAT(' (', year, '). ', title, '.') AS label, item_key AS value FROM ".$wpdb->prefix."zotpress_zoteroItems
                WHERE api_user_id='".$zp_api_user_id."' AND json LIKE %s AND itemType NOT IN ('attachment', 'note') ORDER BY author ASC
            ", 
            '%' . $wpdb->esc_like($_GET['term']) . '%'
    ), OBJECT );
    
    $zpSearch = array();
    
    if ( count($zpSearchResults) > 0 )
    {
        foreach ( $zpSearchResults as $zpResult )
        {
            // Deal with author
            $author = $zpResult->author;
            $zpResultJSON = json_decode( $zpResult->json );
            
            if ( $author == "" )
            {
                if ( isset($zpResultJSON->creators) && count($zpResultJSON->creators) > 0 )
                    foreach ( $zpResultJSON->creators as $i => $creator )
                        if ( $i != (count($zpResultJSON->creators)-1) )
                            $author .= $creator->name . ', ';
                        else
                            $author .= $creator->name;
            }
            
            array_push( $zpSearch, array( "label" => $author.$zpResult->label, "value" => $zpResult->value) );
        }
    }
    
    echo json_encode($zpSearch);
    
    unset($zp_api_user_id);
    unset($zp_account);
    $wpdb->flush();

?>