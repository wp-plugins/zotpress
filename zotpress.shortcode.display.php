

        <?php
        
        $zp_multiple_accounts_accounts = false;
        
        if ($GLOBALS['zp_shortcode_attrs']['api_user_id'] !== false || $GLOBALS['zp_shortcode_attrs']['nickname'] !== false)
            $zp_multiple_accounts = true;
            
        if ($zp_multiple_accounts === false) // User id or nickname specified
        {
            foreach ($GLOBALS['zp_accounts'] as $account)
            {
                if (($GLOBALS['zp_shortcode_attrs']['api_user_id'] !== false && $GLOBALS['zp_shortcode_attrs']['api_user_id'] == $account->api_user_id)
                        || ($GLOBALS['zp_shortcode_attrs']['nickname'] !== false && $GLOBALS['zp_shortcode_attrs']['nickname'] == $account->nickname))
                {
                    echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$GLOBALS['zp_shortcode_attrs']['data_type']."', '".$GLOBALS['zp_shortcode_attrs']['collection_id']."', '".$GLOBALS['zp_shortcode_attrs']['item_key']."', '".$GLOBALS['zp_shortcode_attrs']['tag_name']."', '".$GLOBALS['zp_shortcode_attrs']['content']."', '".$GLOBALS['zp_shortcode_attrs']['style']."', '".$GLOBALS['zp_shortcode_attrs']['order']."', '".$GLOBALS['zp_shortcode_attrs']['sort']."', '".$GLOBALS['zp_shortcode_attrs']['year']."', '".$GLOBALS['zp_shortcode_attrs']['download']."', '".$GLOBALS['zp_shortcode_attrs']['author']."', '".$GLOBALS['zp_shortcode_attrs']['limit']."', '".$GLOBALS['zp_shortcode_attrs']['image']."', '".$GLOBALS['zp_instance_id']."', false);\n        ";
                }
            }
        }
        
        // Multiple shortcode calls
        else
        {
            foreach ($GLOBALS['zp_accounts'] as $account)
            {
                echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$GLOBALS['zp_shortcode_attrs']['data_type']."', '".$GLOBALS['zp_shortcode_attrs']['collection_id']."', '".$GLOBALS['zp_shortcode_attrs']['item_key']."', '".$GLOBALS['zp_shortcode_attrs']['tag_name']."', '".$GLOBALS['zp_shortcode_attrs']['content']."', '".$GLOBALS['zp_shortcode_attrs']['style']."', '".$GLOBALS['zp_shortcode_attrs']['order']."', '".$GLOBALS['zp_shortcode_attrs']['sort']."', '".$GLOBALS['zp_shortcode_attrs']['year']."', '".$GLOBALS['zp_shortcode_attrs']['download']."', '".$GLOBALS['zp_shortcode_attrs']['author']."', '".$GLOBALS['zp_shortcode_attrs']['limit']."', '".$GLOBALS['zp_shortcode_attrs']['image']."', '".$GLOBALS['zp_instance_id']."', false);\n        ";
            }
        }
        
        ?>
