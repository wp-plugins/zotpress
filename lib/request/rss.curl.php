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
                function doRequest( $xml_url, $use_get_file_get_contents ) // won't need the second param anymore ...
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
                        //header( 'Zotero-API-Version: 2' );
                        $response = wp_remote_get( $url );
                        
                        if ( is_wp_error($response) || ! isset($response['body']) )
                        {
                                $this->curl_error = $response->get_error_message();
                                
                                if ($response->get_error_code() == "http_request_failed")
                                {
                                        // Try again with less restrictions
                                        add_filter('https_ssl_verify', '__return_false'); //add_filter('https_local_ssl_verify', '__return_false');
                                        
                                        $response = wp_remote_get( $url );
                                        
                                        if ( is_wp_error($response) || ! isset($response['body']) )
                                                $this->curl_error = $response->get_error_message();
                                        else // no errors this time
                                                $this->curl_error = false;
                                }
                        }
                        else if ( $response == "An error occurred" )
                        {
                                $this->curl_error = "An error occurred: WordPress was unable to import from Zotero using this request URL.";
                        }
                        
                        $data = wp_remote_retrieve_body( $response ); // Thanks to Trainsmart.com developer!
                        
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