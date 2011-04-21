
<!-- START OF ZOTPRESS CODE -->

<style type="text/css">
<!--
    div.zp-Zotpress {
        margin: 1em 0;
    }
    div.zp-ZotpressInner {
        display: none;
    }
    div.zp-Zotpress div.zp-Entry {
        position: relative;
        clear: both;
    }
    div.zp-Zotpress div.zp-Entry.zp-Image {
        min-height: 170px;
    }
    div.zp-Zotpress div.zp-Entry-Image {
        position: absolute;
        top: 0;
        left: 0;
        /*float: left;*/
    }
    div.zp-Zotpress div.zp-Entry-Image-Crop {
        overflow: hidden;
        width: 150px;
        height: 150px;
    }
    div.zp-Zotpress div.csl-bib-body {
        margin: 0 0 15px 0;
    }
    div.zp-Zotpress div.zp-Entry.zp-Image div.csl-bib-body {
        margin: 0 0 15px 170px;
    }
    div.zp-Zotpress span.zp-Loading {
        border: 1px solid #ddd;
        border-radius: 5px;
        -moz-border-radius: 5px;
        background: #f3f3f3 url('<?php echo ZOTPRESS_PLUGIN_URL; ?>images/loading_list.gif') no-repeat top left;
        display: block;
        margin: auto;
        overflow: hidden;
        width: 33px;
        height: 32px;
    }
    div.zp-Zotpress span.zp-Loading span {
        visibility: hidden;
    }
    div.zp-Zotpress p.zp-NoCitations {
        margin: 0;
    }
-->
</style>

<script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>js/jquery.livequery.js"></script>
<script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>zotpress.shortcode.js"></script>

<script type="text/javascript">
    
    jQuery(document).ready(function()
    {
        // SET UP AJAX CALLS ARRAY
        window.ajax_calls = new Array();
        
        // SET UP ZOTPRESS PLUGIN URL VAR
        window.ZOTPRESS_PLUGIN_URL = "<?php echo ZOTPRESS_PLUGIN_URL; ?>";