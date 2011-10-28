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
        
        $zp_output = "\n<script type='text/javascript'>jQuery(document).ready(function(){";
        $zp_output .= "jQuery('#zp-Zotpress-InText-Bibliography').livequery(function(){";
        
        foreach ($GLOBALS['zp_shortcode_instances'] as $zp_instance)
            $zp_output .= $zp_instance . "";
        
        $zp_output .= "});";
        $zp_output .= "});</script>\n";
        
        $zp_output .= "\n<div id='zp-Zotpress-InText-Bibliography'></div>\n\n";
        
        return $zp_output;
    }

?>