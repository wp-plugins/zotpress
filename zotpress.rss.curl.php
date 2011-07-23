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
                // 300 seconds = 5 minutes
                // 3600 seconds = 60 minutes
                var $recache = false, $curl_error = false, $timelimit = 3600, $timeout = 300, $shortcode_request = false, $instance_id = false;
                
                
                function setRequestUri( $zp_shortcode_request )
                {
                        $this->shortcode_request = $zp_shortcode_request;
                }
                
                function setInstanceId( $zp_instance_id )
                {
                        $this->instance_id = $zp_instance_id;
                }
                
                function get_curl_contents( $url, $recache ) {
                        $this->recache = $recache;
                        return $this->doRequest( $url, false );
                }
                
                function get_file_get_contents( $url, $recache ) {
                        $this->recache = $recache;
                        return $this->doRequest( $url, true );
                }
                
                
                // DO REQUEST           $cache_key is the URL
                function doRequest( $cache_key, $use_get_file_get_contents )
                {
                        global $wpdb;
                        
                        
                        // CHECK IF CACHED DATA EXISTS
                        $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".$cache_key."'");
                        $zp_cache_total = $wpdb->num_rows;
                        
                        
                        // If not cached, prepare to cache it -- we're just inserting an empty placeholder here
                        if ($zp_cache_total == 0)
                        {
                                $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_cache (cache_key, xml_data, cache_time, instance_id) VALUES ('".$cache_key."', 'FALSE', '".time()."', '".$this->instance_id."')");
                                $wpdb->query($this->shortcode_request." WHERE cache_key='".$cache_key."';");
                        }
                        
                        
                        // IF CACHED, DISPLAY AND CHECK IT
                        else
                        {
                                // NOTE: When If-Modified-Since header 304 implemented, this needs to change ...
                                
                                // RECACHE IF PAST THE TIME OR IF FORCED
                                if ($this->checkTime($zp_cache[0]->cache_time) === true || $this->recache === true) // Every hour (3600), or when forced
                                {
                                        // Empty the cache for this query and update the timestamp
                                        $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='FALSE', cache_time='".time()."' WHERE cache_key='".$cache_key."';");
                                        $this->recache = false;
                                }
                                
                                // NOT TIME TO CHECK YET, SO JUST DISPLAY CACHED DATA
                                else
                                {
                                        return $zp_cache[0]->xml_data;
                                        exit();
                                }
                        }
                        
                        
                        // GET DATA
                        $data = $this->getXmlData( $cache_key );
                        
                        // Check for curl errors
                        if ($this->curl_error !== false)
                        {
                                return $this->curl_error;
                                exit();
                        }
                        else // Add data to cache
                        {
                                $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='".mysql_real_escape_string($data)."' WHERE cache_key='".$cache_key."';");
                                return $data;
                        }
                }
                
                
                function checkTime( $cache_time )
                {
                        if (isset( $cache_time ))
                        {
                                $diff = time() - $cache_time;
                                
                                if ($diff >= $this->timelimit)
                                        return true;
                                else
                                        return false;
                        }
                        else // No cache time set
                        {
                                return false;
                        }
                }
                
                function getXmlData( $url )
                {
                        if (isset($use_get_file_get_contents) && $use_get_file_get_contents === true)
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
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                
                                $data = curl_exec($ch);
                                
                                if ($data === false)
                                        $this->curl_error = curl_error($ch);
                                
                                curl_close($ch);
                        }
                        
                        // Make sure tags didn't return an error -- redo if it did
                        if ($data == "Tag not found")
                        {
                                $url_break = explode("/", $url);
                                $url = $url_break[0]."//".$url_break[2]."/".$url_break[3]."/".$url_break[4]."/".$url_break[7];
                                $url = str_replace("=50", "=5", $url);
                                
                                $data = $this->getXmlData( $url );
                        }
                        
                        return $data;
                }
        }
}

?>