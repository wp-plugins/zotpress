<?php

    // Thanks to rosty dot kerei at gmail dot com at php.net
    function unicode_urldecode($url)
    {
        preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
       
        foreach ($a[1] as $uniord)
        {
            $dec = hexdec($uniord);
            $utf = '';
           
            if ($dec < 128)
            {
                $utf = chr($dec);
            }
            else if ($dec < 2048)
            {
                $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
            else
            {
                $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
           
            $url = str_replace('%u'.$uniord, $utf, $url);
        }
       
        return urldecode($url);
    }
    
    
    // Thanks to http://www.firsttube.com/read/sorting-a-multi-dimensional-array-with-php/
    function subval_sort($a, $subkey, $sort)
    {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
        if (strtolower($sort) == "asc")
            asort($b);
        else
            arsort ($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
    }
    
?>