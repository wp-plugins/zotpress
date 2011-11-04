<?php

    function Zotpress_zotpressInTextBib ($atts)
    {
        // IN PROGRESS - PENDING IN-TEXT API
        extract(shortcode_atts(array(
            'style' => "apa"
        ), $atts));
        
        
        // FORMAT PARAMETERS
        $style = str_replace('"','',html_entity_decode($style));
        
        // DISPLAY IN-TEXT BIBLIOGRAPHY
        $zp_output = "\n<div id=\"zp-Zotpress-InText-Bibliography\">";
        foreach ($GLOBALS['zp_shortcode_instances'] as $zp_instance)
            $zp_output .= $zp_instance;
        $zp_output .= "</div>\n\n";
        
        return $zp_output;
    }

?>