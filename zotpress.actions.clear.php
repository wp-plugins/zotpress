<?php


    // Include WordPress
    require('../../../wp-load.php');
    define('WP_USE_THEMES', false);


    // Set up XML document
    $xml = "<result success='false' />\n";
    

    /*
     
       CLEAR CACHE
        
    */

    if (isset( $_GET['cache'] ))
    {
        // Truncate table
        $wpdb->query( "TRUNCATE ".$wpdb->prefix."zotpress_cache" );
        
        // Display success XML
        $xml = "<result success='true' />\n";
    }
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<cache>\n";
    echo $xml;
    echo "</cache>";

?>