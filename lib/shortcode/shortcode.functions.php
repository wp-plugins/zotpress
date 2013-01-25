<?php

    
    
    // GET YEAR
    // Used by: In-Text Shortcode, In-Text Bibliography Shortcode
    function zp_get_year($date)
    {
	preg_match_all( '/(\d{4})/', $date, $matches );
	return $matches[0][0];
    }
    
    
    
    // SUBVAL SORT
    // Thanks to http://www.firsttube.com/read/sorting-a-multi-dimensional-array-with-php/
    // Used by: Bibliography Shortcode, In-Text Bibliography Shortcode
    function subval_sort($a, $subkey, $sort)
    {
	foreach($a as $k=>$v) {
	    if ($subkey == "date")
		$b[$k] = zp_get_year(strtolower($v[$subkey]));
	    else
		$b[$k] = strtolower($v[$subkey]);
	}
	
        strtolower($sort) == "asc" ? asort($b) : arsort ($b);
	
	foreach($b as $key=>$val) {
		$c[$key] = $a[$key];
	}
	return $c;
    }
    
    
    
    // Thanks to user "Alex" at http://www.phpfreaks.com/forums/index.php?topic=310949.0
    function replace_skip($str, $find, $replace, $skip = 1) {
	$cpos = 0;
	for($i = 0, $len = strlen($find);$i < $skip;++$i) {
	    if(($pos = strpos(substr($str, $cpos), $find)) !== false) {
		$cpos += $pos + $len;
	    }
	}
	return substr($str, 0, $cpos) . str_replace($find, $replace, substr($str, $cpos));
    }
    
    
    
    function zp_get_fullname_author_items ($wpdb, $author)
    {
	$zp_authors_items = "";
	$zp_authors_query = "SELECT item_key, json FROM ".$wpdb->prefix."zotpress_zoteroItems WHERE author LIKE '%".$author[1]."%'";
	$zp_authors = $wpdb->get_results($zp_authors_query, ARRAY_A);
	
	// Create item_key list
	foreach ($zp_authors as $zp_author)
	{
	    $zp_author_json = json_decode($zp_author["json"]);
	    
	    foreach ($zp_author_json->creators as $zp_creators) {
		if (strtolower($zp_creators->firstName) == strtolower(trim($author[0])) && strtolower($zp_creators->lastName) == strtolower(trim($author[1]))) {
		    if (strlen($zp_authors_items) == 0)
			$zp_authors_items .= $zp_author["item_key"];
		    else
			$zp_authors_items .= "," . $zp_author["item_key"];
		}
	    }
	}
	
	return $zp_authors_items;
	
	// Clean up
	$wpdb->flush();
	unset($zp_authors);
	unset($zp_authors_query);
	unset($zp_authors_items);
    }
    
    
    
    function zp_get_exclusive_items ($wpdb, $type, $ids)
    {
	$zp_exclusive_items = "";
	
	$zp_exclusive_items_query = "SELECT GROUP_CONCAT(listitems SEPARATOR ',') AS listitems FROM ".$wpdb->prefix."zotpress_zotero".$type." WHERE ";
	
	if ($type == "collections")
	    $zp_exclusive_items_query .= "item_key";
	else if ($type == "tags")
	    $zp_exclusive_items_query .= "title";
	
	$zp_exclusive_items_query .= " IN ('".implode("','", $ids)."') GROUP BY 'all';";
	
	$zp_listitems = $wpdb->get_results($zp_exclusive_items_query, ARRAY_A);
	
	// Get exclusive items
	$zp_listitems_counted = array_count_values(explode(",", $zp_listitems[0]["listitems"]));
	
	foreach ($zp_listitems_counted as $item => $count)
	    if ($count > 1)
		$zp_exclusive_items .= $item . ",";
	
	return substr_replace($zp_exclusive_items, "", -1);
	
	// Clean up
	$wpdb->flush();
	unset($zp_listitems);
	unset($zp_exclusive_items);
	unset($zp_listitems_counted);
	unset($zp_exclusive_items_query);
    }
    
    
    
    function zp_get_subcollections ($wpdb, $api_user_id, $parent, $sortby, $order)
    {
	$zp_query = "SELECT ".$wpdb->prefix."zotpress_zoteroCollections.* FROM ".$wpdb->prefix."zotpress_zoteroCollections";
	$zp_query .= " WHERE api_user_id='".$api_user_id."' AND parent = '".$parent."' ";
	
	// Sort by and sort direction
	if ($sortby)
	{
	    if ($sortby == "default")
		$sortby = "retrieved";
	    else if ($sortby == "date" || $sortby == "author")
		continue;
	    
	    $zp_query .= " ORDER BY ".$sortby." " . $order;
	}
	
	$zp_results = $wpdb->get_results($zp_query, OBJECT);
	
	$zp_output = "<ul>\n";
	
	foreach ($zp_results as $zp_collection)
	{
	    $zp_output .= "<li rel=\"" . $zp_collection->item_key . "\">" . $zp_collection->title . "</li>\n";
	    
	    if ($zp_collection->numCollections > 0)
		$zp_output .= zp_get_subcollections($wpdb, $api_user_id, $zp_collection->item_key, $sortby, $order);
	}
	
	$zp_output .= "</ul>\n";
	
	return $zp_output;
    }
    
    
    
?>