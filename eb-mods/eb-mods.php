<?php 
function eb_zotpress_get_account($api_user_id = false, $nickname = false) {
	global $wpdb;

	$hash = 'zotpress_api_user_'.$api_user_id.$nickname;

	$zp_account = get_transient($hash);
	if($zp_account === false) {
		if($api_user_id || $nickname) {
			if($api_user_id) {
				$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id = %d", $api_user_id);
			}
			else  {
				$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname = %s", $nickname);
			}
		}
		else {
			$query = "SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1";
		}
		$zp_account = $wpdb->get_row($query);

		set_transient($hash, $zp_account);
	}

	return $zp_account;
}

function eb_zotpress_refresh_account($api_user_id = false, $nickname = false) {
	$hash = 'zotpress_api_user_'.$api_user_id.$nickname;

	delete_transient($hash);
}


function eb_zotpress_get_cache($request_id, $api_user_id) {
	global $wpdb;

	$cache_ver = get_option('zotpress_cache_ver_user_'.$api_user_id, 1);
	$hash = 'zotpress_cache_'.md5($request_id.$api_user_id.$cache_ver);

	$zp_account = get_transient($hash);
	if($zp_account === false) {
		$zp_account = $wpdb->get_results($wpdb->prepare(
			"SELECT DISTINCT ".$wpdb->prefix."zotpress_cache.*
			FROM ".$wpdb->prefix."zotpress_cache
			WHERE ".$wpdb->prefix."zotpress_cache.request_id = %s
			AND ".$wpdb->prefix."zotpress_cache.api_user_id = %d", 
			$request_id, $api_user_id
		));

		set_transient($hash, $zp_account);
	}

	return $zp_account;
}

function eb_zotpress_refresh_cache($request_id, $api_user_id) {
	$cache_ver = get_option('zotpress_cache_ver_user_'.$api_user_id, 1);
	$hash = 'zotpress_cache_'.md5($request_id.$api_user_id.$cache_ver);

	delete_transient($hash);
}


function eb_zotpress_escape_array($arr) {
    global $wpdb;
    $escaped = array();
    foreach($arr as $k => $v){
    	$v = trim($v);
    	if(!$v)
    		continue;

        if(is_numeric($v))
            $escaped[] = $wpdb->prepare('%d', $v);
        else
            $escaped[] = $wpdb->prepare('%s', $v);
    }
    return implode(',', $escaped);	
}

add_action( 'plugins_loaded', 'eb_zotpress_remove_filters' );
function eb_zotpress_remove_filters() {
	remove_filter( 'http_request_timeout', 'Zotpress_change_timeout' );
}