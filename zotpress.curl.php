<?php
/*
Sean Huber CURL library
Session-based caching added by Mike Purvis
Caching, get_file_get_contents option, and timed sessions by Katie Seaborn

This library is a basic implementation of CURL capabilities.

==================================== USAGE ====================================
It exports the CURL object globally, so set a callback with setCallback($func).
(Use setCallback(array('class_name', 'func_name')) to set a callback as a func
that lies within a different class)
Then use one of the CURL request methods:

get($url);
post($url, $vars); vars is a urlencoded string in query string format.

Your callback function will then be called with 1 argument, the response text.
If a callback is not defined, your request will return the response text.
*/

if (!class_exists('CURL'))
{
        class CURL
        {
                var $callback = false, $cache = false, $flush_cache = false;
                
                function doRequest($method, $url, $vars, $use_get_file_get_contents)
                {
                        
                        global $wpdb;
                    
                        if ($this->cache)
                        {
                                // GET CACHE KEY FOR THIS REQUEST
                                $cache_key = md5(serialize(func_get_args()));
                                
                                    
                                // CHECK FOR EXISTING CACHED REQUEST
                                $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".$cache_key."'");
                                
                                $zp_cache_total = $wpdb->num_rows;
                                
                                
                                // IF NOT CACHED, CACHE IT
                                if ($zp_cache_total == 0)
                                {
                                    $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_cache (cache_key, xml_data, cache_time) VALUES ('".$cache_key."', 'FALSE', '".time()."')");
                                }
                                
                                
                                // IF CACHED, CALLBACK OR CHECK IT
                                else
                                {
                                        if ($this->callback)
                                        {
                                                $callback = $this->callback;
                                                $this->callback = false;
                                                return call_user_func($callback, $wpdb->xml_data);
                                        }
                                        else
                                        {
                                                // NOTE:
                                                // When If-Modified-Since header 304 implemented, this needs to change ...
                                                
                                                // TIME TO CHECK
                                                if ($this->checkTime($zp_cache[0]->cache_time) >= 3600) // Every hour
                                                {
                                                    // REMOVE CACHED DATA, THEN UDPATE CACHE
                                                    $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='FALSE', cache_time='".time()."' WHERE cache_key='".$cache_key."';");
                                                    $this->cache = false;
                                                    
                                                    if ($use_get_file_get_contents)
                                                        $this->doRequest('GET', $url, 'NULL', true);
                                                    else
                                                        $this->doRequest('GET', $url, 'NULL', false);
                                                }
                                                
                                                // NOT TIME TO CHECK YET, SO JUST DISPLAY CACHED DATA
                                                else
                                                {
                                                    return $zp_cache[0]->xml_data;
                                                }
                                        }
                                }
                        }
                    
                    
                        // GET XML DATA
                        
                        if ($use_get_file_get_contents)
                        {
                            $data = file_get_contents($url);
                        }
                        else // Use cURL
                        {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt ($ch, CURLOPT_HEADER, 0);
                            curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
                            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
                            
                            if ($method == 'POST') {
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
                            }
                            
                            $data = curl_exec($ch);
                            curl_close($ch);
                        }
                        
                        if ($data)
                        {
                            if ($this->callback)
                            {
                                $callback = $this->callback;
                                $this->callback = false;
                                return call_user_func($callback, $data);
                            }
                            
                            // ADD DATA TO CACHE
                            else
                            {
                                $zp_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_cache WHERE cache_key='".md5(serialize(func_get_args()))."'");
                                $wpdb->query("UPDATE ".$wpdb->prefix."zotpress_cache SET xml_data='".mysql_real_escape_string($data)."' WHERE cache_key='".$zp_cache[0]->cache_key."';");
                                return $data;
                            }
                        }
                        else
                        {
                            return curl_error($ch);
                        }
                }
                
                
                function enableCache() {
                    $this->cache = true;
                }
                
                function checkTime($cache_time) {
                    if (isset($cache_time)) {
                        $diff = time() - $cache_time;
                        return $diff;
                    }
                }
                
                function flushCache() { // DEPRECATED
                    unset($_SESSION['curl_cache']);
                    unset($_SESSION['curl_cache_time']);
                }
                
                function get($url) {
                    return $this->doRequest('GET', $url, 'NULL', false);
                }
                
                function get_file_get_contents($url) {
                    return $this->doRequest('GET', $url, 'NULL', true);
                }
                
                function post($url, $vars) {
                    return $this->doRequest('POST', $url, $vars, false);
                }
                
                function setCallback($func_name) {
                    $this->callback = $func_name;
                }
        }
}

?>