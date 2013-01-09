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
                // TIME: 300 seconds = 5 minutes; 3600 seconds = 60 minutes
                var $update = false, $curl_error = false, $timelimit = 3600, $timeout = 300;
                
                
                function get_curl_contents( $url, $update ) {
                        $this->update = $update;
                        return $this->doRequest( $url, false );
                }
                
                function get_file_get_contents( $url, $update ) {
                        $this->update = $update;
                        return $this->doRequest( $url, true );
                }
                
                
                // DO REQUEST
                function doRequest( $xml_url, $use_get_file_get_contents )
                {
                        global $wpdb;
                        
                        // NOTE: When If-Modified-Since header 304 implemented, this needs to change ...
                        
                        // RECACHE IF PAST THE TIME OR IF FORCED
                        //if ($this->checkTime($zp_cache[0]->cache_time) === true || $this->update === true) // Every hour (3600), or when forced
                        //{
                        //        // UPDATE
                        //}
                        //
                        //// NOT TIME TO CHECK YET, SO JUST DISPLAY CACHED DATA
                        //else
                        //{
                        //        return $zp_cache[0]->xml_data;
                        //        exit();
                        //}
                        
                        // Get the data using CURL or file_get_contents
                        $data = $this->getXmlData( $xml_url );
                        
                        // Check for CURL errors
                        if ($this->curl_error !== false) {
                                return $this->curl_error;
                                exit();
                        }
                        
                        // Otherwise, return the data
                        else {
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
                        // Use file_get_contents (not recommended)
                        if (isset($use_get_file_get_contents) && $use_get_file_get_contents === true)
                        {
                                $data = file_get_contents($url);
                        }
                        
                        // Use cURL (recommended)
                        else
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
                                
                                //$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // For when If-Modified-Since header 304 implemented
                                
                                if ($data === false)
                                        $this->curl_error = curl_error($ch);
                                
                                curl_close($ch);
                        }
                        
                        // Make sure tags didn't return an error -- redo if so
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