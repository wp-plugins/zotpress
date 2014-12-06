<?php

// ADMIN -----------------------------------------------------------------------------------------

    function Zotpress_options()
    {
        // Prevent access to users who are not editors
        if ( !current_user_can('edit_others_posts') && !is_admin() )
			wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
        
        
        
        // SETUP AND IMPORT PAGES
        
        if (isset($_GET['setup']))
        {
            include( dirname(__FILE__) . '/admin.setup.php' );
        }
        
        else if (isset($_GET['import']))
        {
            include( dirname(__FILE__) . '/../import/import.php' );
        }
        
        else if (isset($_GET['selective']))
        {
            include( dirname(__FILE__) . '/../import/import.selective.php' );
        }
        
        
        
        
        // ACCOUNTS PAGE
        
        else if (isset($_GET['accounts']))
        {
            include( dirname(__FILE__) . '/admin.accounts.php' );
        }
        
        
        
        // OPTIONS PAGE
        
        else if (isset($_GET['options']))
        {
            include( dirname(__FILE__) . '/admin.options.php' );
        }
        
        
        
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include( dirname(__FILE__) . '/admin.help.php' );
        }
        
        
        
        // BROWSE PAGE
        
        else
        {
            include( dirname(__FILE__) . '/admin.browse.php' );
        }
    }

// END ADMIN ------------------------------------------------------------------------------------------

?>