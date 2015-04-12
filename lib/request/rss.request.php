<?php

/*
        Zotpress Request
        Based on Sean Huber's CURL library with additions by Mike Purvis.
*/

if (!class_exists('ZotpressRequest'))
{
        class ZotpressRequest
        {
                // TIME: 300 seconds = 5 minutes; 3600 seconds = 60 minutes
                var $update = false, $request_error = false, $timelimit = 3600, $timeout = 300;
                
                
                function get_request_contents( $url, $update ) {
                        $this->update = $update;
                        return $this->doRequest( $url );
                }
                
                // DO REQUEST
                function doRequest( $xml_url )
                {
                        //global $wpdb;
                        
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
                        
                        // Get the data
                        $data = $this->getXmlData( $xml_url );
                        
                        // Check for request errors
                        if ($this->request_error !== false) {
                                return $this->request_error;
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
                        $response = wp_remote_get( $url, array( 'headers' => array("Zotero-API-Version" => "2") ) );
                        
                        if ( is_wp_error($response) || ! isset($response['body']) )
                        {
                                $this->request_error = $response->get_error_message();
                                
                                if ($response->get_error_code() == "http_request_failed")
                                {
                                        // Try again with less restrictions
                                        add_filter('https_ssl_verify', '__return_false'); //add_filter('https_local_ssl_verify', '__return_false');
                                        $response = wp_remote_get( $url, array( 'headers' => array("Zotero-API-Version" => "2") ) );
                                        
                                        if ( is_wp_error($response) || ! isset($response['body']) )
                                        {
                                                $this->request_error = $response->get_error_message();
                                        }
                                        else if ( $response == "An error occurred" || ( isset($response['body']) && $response['body'] == "An error occurred") )
                                        {
                                                $this->request_error = "WordPress was unable to import from Zotero. This is likely caused by an incorrect citation style name. For example, 'mla' is now 'modern-language-association'. Use the name found in the style's URL at the Zotero Style Repository.";
                                        }
                                        else // no errors this time
                                        {
                                                $this->request_error = false;
                                        }
                                }
                        }
                        else if ( $response == "An error occurred" || ( isset($response['body']) && $response['body'] == "An error occurred") )
                        {
                                $this->request_error = "WordPress was unable to import from Zotero. This is likely caused by an incorrect citation style name. For example, 'mla' is now 'modern-language-association'. Use the name found in the style's URL at the Zotero Style Repository.";
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