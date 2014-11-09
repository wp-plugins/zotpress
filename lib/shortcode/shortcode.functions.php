<?php

    
    
    // GET YEAR
    // Used by: In-Text Shortcode, In-Text Bibliography Shortcode
    function zp_get_year($date, $yesnd)
    {
		$date_return = false;
		
		preg_match_all( '/(\d{4})/', $date, $matches );
		
		if (is_null($matches[0][0]))
			if ( $yesnd === true )
				$date_return = "n.d.";
			else
				$date_return = "";
		else
			$date_return = $matches[0][0];
		
		return $date_return;
    }
	
	
	// GET DATE
	// Used by: n/a
	function zp_get_date($date)
	{
		$year = zp_get_year($date); // 4 digits
		
		preg_match_all( '/(\w)/', $date, $matches );
		//var_dump($matches);
		
		$month = date_parse( $matches[0][0] );
		
		//var_dump($date['month']);
		
		//var_dump($month);
	}
    
    
    
    // SUBVAL SORT
    // Used by: Bibliography Shortcode, In-Text Bibliography Shortcode
    function subval_sort($item_arr, $sortby, $sort)
    {
		// Format sort order
		if ( strtolower($sort) == "desc" ) $sort = SORT_DESC; else $sort = SORT_ASC;
		
		// Author or date
		if ( $sortby == "author" || $sortby == "date" )
		{
			foreach ($item_arr as $key => $val)
			{
				$author[$key] = $val["author"];
				$date[$key] = $val["date"];
			}
		}
		
		// Title
		else if ( $sortby == "title" )
		{
			foreach ($item_arr as $key => $val)
			{
				$title[$key] = $val["title"];
				$author[$key] = $val["author"];
			}
		}
		
		if ( $sortby == "author" ) array_multisort( $author, $sort, $date, $sort, $item_arr );
		else if ( $sortby == "date" ) array_multisort( $date, $sort, $author, $sort, $item_arr );
		else if ( $sortby == "title" ) array_multisort( $title, $sort, $author, $sort, $item_arr );
		
		return $item_arr;
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
    
    
    
    function zp_get_subcollections ($wpdb, $api_user_id, $parent, $sortby, $order, $link=false)
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
			$zp_output .= "<li rel=\"" . $zp_collection->item_key . "\">";
			if ($link == "yes")
			{
				$zp_output .= "<a class='zp-CollectionLink' title='" . $zp_collection->title . "' rel='" . $zp_collection->item_key . "' href='" . $_SERVER["REQUEST_URI"];
				if ( strpos($_SERVER["REQUEST_URI"], "?") === false ) { $zp_output .= "?"; } else { $zp_output .= "&"; }
				$zp_output .= "zpcollection=" . $zp_collection->item_key . "'>";
			}
			$zp_output .= $zp_collection->title;
			if ($link == "yes") { $zp_output .= "</a>"; }
			$zp_output .= "</li>\n";
			
			if ($zp_collection->numCollections > 0)
			$zp_output .= zp_get_subcollections($wpdb, $api_user_id, $zp_collection->item_key, $sortby, $order, $link);
		}
		
		$zp_output .= "</ul>\n";
		
		return $zp_output;
    }
    
    
    
?>