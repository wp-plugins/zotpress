<?php

	// Include WordPress
	require('../../../../../wp-load.php');

	define('WP_USE_THEMES', false);
	
	
	// SET UP VARS
	
	$zp_instance_id = false;
	if (isset($_GET['zp_instance_id']) && preg_match("/^[a-zA-Z0-9-_]+$/", $_GET['zp_instance_id']))
		$zp_instance_id = trim(urldecode($_GET['zp_instance_id']));
	
	
	$zp_return_url = get_bloginfo("url");
	$zp_url_regex = "((https?|ftp)\:\/\/)?([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?([a-z0-9-.]*)\.([a-z]{2,3})(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?";
	
	if (isset($_GET['zp_return_url']) && preg_match("/".$zp_url_regex."/", $_GET['zp_return_url']))
		$zp_return_url = trim(urldecode($_GET['zp_return_url']));
	
	header("Refresh: 0; url=".$zp_return_url);
	exit;
	
?>