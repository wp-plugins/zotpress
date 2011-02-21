
<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        <?php
        
        $zp_multiple = false;
        
        if ($api_user_id !== false || $nickname !== false)
            $zp_multiple = true;
            
        if ($zp_multiple === false) {
            foreach ($zp_accounts as $account) {
                if (($api_user_id !== false && $api_user_id == $account->api_user_id)
                        || ($nickname !== false && $nickname == $account->nickname)) {
                    if ($download == "yes") {
                        echo "DisplayCitationsWithDownloads('".$account->account_type."', '".$account->api_user_id."', '".$data_type."', '".$collection_id."', '".$item_key."', '".$tag_name."', '".$content."', '".$style."', '".$order."', '".$sort."', '".$year."', '".$download."', '".$author."', '".$limit."', '".$image."', '".$zp_instance_id."', '".ZOTPRESS_PLUGIN_URL."');";
                    } else {
                        echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$data_type."', '".$collection_id."', '".$item_key."', '".$tag_name."', '".$content."', '".$style."', '".$order."', '".$sort."', '".$year."', '".$download."', '".$author."', '".$limit."', '".$image."', '".$zp_instance_id."', false, '".ZOTPRESS_PLUGIN_URL."');";
                    }
                }
            }
        }
        
        else {
            foreach ($zp_accounts as $account) {
                if ($download == "yes") {
                    echo "DisplayCitationsWithDownloads('".$account->account_type."', '".$account->api_user_id."', '".$data_type."', '".$collection_id."', '".$item_key."', '".$tag_name."', '".$content."', '".$style."', '".$order."', '".$sort."', '".$year."', '".$download."', '".$author."', '".$limit."', '".$image."', '".$zp_instance_id."', '".ZOTPRESS_PLUGIN_URL."');";
                } else {
                    echo "DisplayCitations('".$account->account_type."', '".$account->api_user_id."', '".$data_type."', '".$collection_id."', '".$item_key."', '".$tag_name."', '".$content."', '".$style."', '".$order."', '".$sort."', '".$year."', '".$download."', '".$author."', '".$limit."', '".$image."', '".$zp_instance_id."', false, '".ZOTPRESS_PLUGIN_URL."');";
                }
            }
        }
        
        ?>
        
    });
</script>
