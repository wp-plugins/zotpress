<?php
/*
Sean Huber CURL library
Session-based caching added by Mike Purvis
Addition of get_file_get_contents option and timed sessions by Katie Seaborn

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

class CURL
{
        var $callback = false, $cache = false, $flush_cache = false;
        
        function doRequest($method, $url, $vars, $use_get_file_get_contents)
        {
            if ($this->cache)
            {
                $cache_key = md5(serialize(func_get_args()));
                
                if (!isset($_SESSION['curl_cache'])) // First time around
                {
                    $_SESSION['curl_cache'] = array();
                    $_SESSION['curl_cache_time'] = time();
                }
                
                if (isset($_SESSION['curl_cache'][$cache_key])) // Second time around
                {
                    if ($this->callback)
                    {
                        $callback = $this->callback;
                        $this->callback = false;
                        return call_user_func($callback, $_SESSION['curl_cache'][$cache_key]);
                    }
                    else // Check time
                    {
                        // When If-Modified-Since header 304 implemented, this needs to change ... shouldn't flush every 10 mins, just check
                        // and update if different
                        if ($this->checkTime() >= 600) { // Every 10 mins
                            $this->flushCache();
                            if ($use_get_file_get_contents)
                                $this->doRequest('GET', $url, 'NULL', true);
                            else
                                $this->doRequest('GET', $url, 'NULL', false);
                        } else {
                            return $_SESSION['curl_cache'][$cache_key];
                        }
                    }                               
                }
            }
            
            if ($use_get_file_get_contents)
            {
                $data = file_get_contents($url);
            }
            else // Use cURL
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                //curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt ($ch, CURLOPT_HEADER, 0);
                //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
            
            if ($data) {
                if ($this->callback)
                {
                    $callback = $this->callback;
                    $this->callback = false;
                    return call_user_func($callback, $_SESSION['curl_cache'][$cache_key] = $data);
                } else {
                    return $_SESSION['curl_cache'][$cache_key] = $data;
                }
            } else {
                return curl_error($ch);
            }
        }
        
        
        function enableCache() {
            $this->cache = true;
            if (session_id() == "") {
                session_set_cookie_params(600); // 10 mins
                session_start();
            }
        }
        
        function checkTime() {
            if (isset($_SESSION['curl_cache_time'])) {
                $diff = time() - $_SESSION['curl_cache_time'];
                return $diff;
            }
        }
        
        function flushCache() {
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
?>