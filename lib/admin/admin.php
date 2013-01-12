<?php

// ADMIN -----------------------------------------------------------------------------------------
    
    function Zotpress_admin_footer()
    {
        global $wpdb;
        
        $zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
        $zp_accounts_total = $wpdb->num_rows;
        
        // INCLUDE FILTER SCRIPT
        
        if ($zp_accounts_total > 0)
        {
            if (!isset($_GET['accounts']) || !isset($_GET['help'])) {
            ?>
            <script type="text/javascript">
                
                jQuery(document).ready(function()
                {
                    <?php include('admin.display.filter.php'); ?>
                    
                    /*
                        
                        CITATION IMAGE HOVER
                        
                    */
                    
                    jQuery('div#zp-List').delegate("div.zp-Entry-Image", "hover", function () {
                        jQuery(this).toggleClass("hover");
                    });
                    
                });
                
            </script>
            <?php
            }
        }
    }

    function Zotpress_options()
    {
        // Keep out those without access!
        if (!current_user_can('edit_others_posts'))  {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        
        
        // SETUP ZOTPRESS
        
        if (isset($_GET['setup']))
        {
            include('admin.setup.php');
        }
        
        
        
        
        // ADD ZOTERO ACCOUNT
        
        else if (isset($_GET['accounts']))
        {
            include('admin.accounts.php');
        }
        
        
        
        // IMAGE PAGE
        
        else if (isset($_GET['image']))
        {
            include('admin.display.image.php');
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