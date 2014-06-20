<?php

// ADMIN -----------------------------------------------------------------------------------------
    
    //function Zotpress_admin_footer()
    //{
    //    global $wpdb;
    //    
    //    $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
    //    $zp_accounts_total = $wpdb->num_rows;
    //    
    //    // INCLUDE FILTER SCRIPT
    //    
    //    if ($zp_accounts_total > 0)
    //    {
    //        if (!isset($_GET['accounts']) || !isset($_GET['help'])) {
    //        
    //        }
    //    }
    //}

    function Zotpress_options()
    {
        // Prevent access to users who are not editors
        if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
        
        
        
        // SETUP AND IMPORT ZOTPRESS
        
        if (isset($_GET['setup']))
        {
            include('admin.setup.php');
        }
        
        else if (isset($_GET['import']))
        {
            include('admin.import.php');
        }
        
        else if (isset($_GET['selective']))
        {
            include('admin.import.selective.php');
        }
        
        
        
        
        // ADD ZOTERO ACCOUNT
        
        else if (isset($_GET['accounts']))
        {
            include('admin.accounts.php');
        }
        
        
        
        // OPTIONS PAGE
        
        else if (isset($_GET['options']))
        {
            include('admin.options.php');
        }
        
        
        
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include('admin.help.php');
        }
        
        
        
        // ADMIN CITATIONS VIEW
        
        else
        {
            include('admin.default.php');
        }
    }

// END ADMIN ------------------------------------------------------------------------------------------

?>