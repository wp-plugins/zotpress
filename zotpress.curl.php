<?php

/*
        Sean Huber CURL library
        Session-based caching added by Mike Purvis
        Caching, get_file_get_contents option, and timed sessions by Katie Seaborn
        
        This library is a basic implementation of CURL capabilities.
*/


if (!class_exists('CURL'))
{
        class CURL
        {
                // Set up variables
                var $cache = false, $initial = false, $recache = false, $curl_error = false, $timelimit = 3600, $timeout = 300, $shortcode_request = false, $instance_id = false;
                
                
                function setRequestUri( $zp_shortcode_request )
                {
                        $this->shortcode_request = $zp_shortcode_request;
                }
                
                
                function setInstanceId( $zp_instance_id )
                {
                        $this->instance_id = $zp_instance_id;
                }
                
                
                // DO REQUEST
                
                function doRequest( $url, $use_get_file_get_contents )
                {
                        //echo "START DOREQUEST-";
                        global $wpdb;
                        
                        
                        // CHECK IF CACHED DATA EXISTS
                        
                        if ($this->initial === true)
                        {
                                //echo "INITIAL-";
                                //$cache_key = md5(serialize(func_get_args()));
                                $cache_key = $url;
                                $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".$cache_key."'");
                                $zp_cache_total = $wpdb->num_rows;
                                
                                if ($zp_cache_total == 0)
                                {
                                        //echo "NONE, SO CACHE-";
                                        $this->cache = true;
                                }
                                else
                                {
                                        //echo "CACHED (".$zp_cache_total."), SO DISPLAY AND EXIT-";
                                        return $zp_cache[0]->xml_data;
                                        exit();
                                }
                        }
                        
                        
                        // CACHE DATA
                    
                        if ($this->cache === true)
                        {
                                //echo "CACHE-";
                                if ($this->initial === true)
                                {
                                        //echo "INITIAL SET TO FALSE-";
                                        $this->initial = false;
                                }
                                else
                                {
                                        //echo "INITIAL FALSE, SO CHECK CACHE-";
                                        //$cache_key = md5(serialize(func_get_args()));
                                        $cache_key = $url;
                                        $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".$cache_key."'");
                                        $zp_cache_total = $wpdb->num_rows;
                                }
                                
                                // CHECK FOR EXISTING CACHED REQUEST
                                // If not cached, prepare to cache it -- we're just inserting an empty placeholder here
                                if ($zp_cache_total == 0)
                                {
                                        //echo "NO CACHE, SO ENTER FALSE INTO DB-";
                                        //echo $this->shortcode_request." WHERE cache_key='".$cache_key."';";
                                        $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_cache (cache_key, xml_data, cache_time, instance_id) VALUES ('".$cache_key."', 'FALSE', '".time()."', '".$this->instance_id."')");
                                        $wpdb->query($this->shortcode_request." WHERE cache_key='".$cache_key."';");
                                }
                                
                                
                                // IF CACHED, CALLBACK OR DISPLAY AND CHECK IT
                                else
                                {
                                        //echo "CACHED IN DB-";
                                        // NOTE:
                                        // When If-Modified-Since header 304 implemented, this needs to change ...
                                        
                                        // TIME TO CHECK
                                        if ($this->checkTime($zp_cache[0]->cache_time) >= $this->timelimit || $this->recache === true) // Every hour (3600), or when forced
                                        {
                                                //echo "TIME TO CHECK OR FORCED, CACHE SET TO FALSE, TURNED OFF, RECACHE OFF, DOREQUEST AGAIN-";
                                                // Empty the cache for this query and update the timestamp
                                                $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='FALSE', cache_time='".time()."' WHERE cache_key='".$cache_key."';");
                                                $this->cache = false;
                                                
                                                // Turn off re-caching
                                                $this->recache = false;
                                                
                                                // Get the new data
                                                $this->doRequest( $url, $use_get_file_get_contents );
                                        }
                                        
                                        // NOT TIME TO CHECK YET, SO JUST DISPLAY CACHED DATA
                                        else
                                        {
                                                //echo "NOT TIME YET (".$this->checkTime($zp_cache[0]->cache_time)."/".$this->timelimit."), SO DISPLAY FROM DB AND EXIT-";
                                                return $zp_cache[0]->xml_data;
                                                exit();
                                        }
                                }
                        }
                        
                        
                        // CHECK FOR DATA
                        
                        //if ($this->initial === true)
                        //{
                        //        $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".md5(serialize(func_get_args()))."'");
                        //        return $zp_cache[0]->xml_data;
                        //}
                        //
                        //else
                        //{
                                //echo "RUN CURL-";
                                $data = $this->getXmlData( $url );
                                
                                // Check for curl errors
                                if ($this->curl_error !== false)
                                {
                                        //echo "CURL ERROR-";
                                        return $this->curl_error;
                                        exit();
                                }
                                else // Add data to cache
                                {
                                        //echo "GOT NEW CURL DATA, UPDATE DB-";
                                        //$cache_key = md5(serialize(func_get_args()));
                                        $cache_key = $url;
                                        //$zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".$cache_key."'");
                                        //$wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='".mysql_real_escape_string($data)."' WHERE cache_key='".$zp_cache[0]->cache_key."';");
                                        $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='".mysql_real_escape_string($data)."' WHERE cache_key='".$cache_key."';");
                                        return $data;
                                }
                        //}
                        //echo "DO REQUEST END-";
                }
                
                
                function enableCache()
                {
                        //echo "ENABLE CACHE-";
                        $this->cache = true;
                }
                
                function reCache( $cacheOrNot )
                {
                        //echo "SET RECACHE-";
                        $this->recache = $cacheOrNot;
                }
                
                function checkTime( $cache_time )
                {
                        if (isset( $cache_time )) {
                                $diff = time() - $cache_time;
                                return $diff;
                        }
                }
                
                function setInitial() {
                        //echo "SET INITIAL-";
                        $this->initial = true;
                }
                
                function get_curl_contents( $url ) {
                        //echo "RUN GETCURLCONTENTS-";
                        return $this->doRequest( $url, false );
                }
                
                function get_file_get_contents( $url ) {
                        return $this->doRequest( $url, true );
                }
                
                function getXmlData( $url )
                {
                        if ($use_get_file_get_contents)
                        {
                                $data = file_get_contents($url);
                        }
                        else // Use cURL
                        {
                                ini_set('max_execution_time', $this->timeout); // Avoid timeout error
                                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); // Five minutes
                                curl_setopt ($ch, CURLOPT_HEADER, 0);
                                curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
                                //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                                //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
                                
                                $data = curl_exec($ch);
                                
                                if ($data === false)
                                        $this->curl_error = curl_error($ch);
                                
                                curl_close($ch);
                        }
                        
                        //echo "RETURNING NEW DATA-";
                        
                        return $data;
                }
        }
}

?>