<?php

    // Include WordPress
    require(dirname(dirname(dirname(dirname(dirname( dirname( __FILE__ )))))) .'/wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Include database
    global $wpdb;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Zotpress In-Text Bibliography</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel='stylesheet' href='<?php echo includes_url(); ?>/css/buttons.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/wp-admin.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/colors-fresh.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/zotpress.metabox.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/smoothness/jquery-ui-1.8.11.custom.css' type='text/css' media='all' />
		<style>
		html { background: none; }
		body.zp-Zotpress-TinyMCE-Popup { min-width: 0; margin: 0; height: auto; }
		.zp-Zotpress-TinyMCE-Popup h4 { color: #666666; margin: 0.25em 0; padding: 0.8em 1em; }
		.zp-Zotpress-TinyMCE-Popup div#zp-ZotpressMetaBox-InTextCreator-Options { margin: 0; }
		.zp-Zotpress-TinyMCE-Popup #zp-ZotpressMetaBox-InTextCreator-Options-Inner { display: block; }
		.zp-Zotpress-TinyMCE-Popup .zp-TinyMCESave { float: right; margin: 0.6em 0.5em 0.7em; padding: 0.25em 1em !important; }
		.zp-Zotpress-TinyMCE-Popup .zp-TinyMCESave.top { position: absolute; float: none; top: 0; right: 0; }
		div#zp-ZotpressMetaBox-Biblio-Options-Inner input, div#zp-ZotpressMetaBox-Biblio-Options-Inner select, div#zp-ZotpressMetaBox-InTextCreator-Options-Inner input, div#zp-ZotpressMetaBox-InTextCreator-Options-Inner select { margin-right: 0; }
		.zp-Zotpress-TinyMCE-Popup input, .zp-Zotpress-TinyMCE-Popup select { font-family: "Open Sans", sans-serif; }
		.zp-Zotpress-TinyMCE-Popup label { font-size: 0.9em; padding: 0 0.5em 0 1.25em; }
		div#zp-ZotpressMetaBox-Biblio-Options p.note, div#zp-ZotpressMetaBox-InTextCreator-Options p.note { margin: 0 1em 0.75em 1.5em; }
		div#zp-ZotpressMetaBox-Biblio-Options div.right, div#zp-ZotpressMetaBox-InTextCreator-Options div.right { margin-top: -0.15em; }
		</style>
		
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.core.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.widget.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.position.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.menu.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.autocomplete.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.livequery.min.js"></script>
        <script type="text/javascript" src="../../js/zotpress.widget.metabox.js"></script>
        <script>
        
        jQuery(document).ready(function() {
        
            var shortcode = tinyMCEPopup.getWindowArg("shortcode");
		    var zpInTextBibDefaults = { "style": "apa", "sortby": "default", "sort": "ASC", "images": "no", "download": "no", "notes": "no", "abstract": "no", "cite": "no", "title": "no" };
			
			if ( shortcode != "" ) // Set fields based on shortcode
			{
				var attributes = shortcode.match(/[\w-]+="[^"]*"/g);
				
				if ( attributes != null )
				{
					for (var i = 0; i < attributes.length; i++)
					{
						var attribute = attributes[i].split("=");
						
						if ( attribute[0].replace(/"/gi, '') == "style")
							jQuery("input[name=style]").val(attribute[1].replace(/"/gi, ''));
						else if (attribute[0].replace(/"/gi, '') == "sortby")
							jQuery("select[name=sortby]").val(attribute[1].replace(/"/gi, ''));
						else
							jQuery("input:radio[name="+attribute[0].replace(/"/gi, '')+"][value="+attribute[1].replace(/"/gi, '')+"]").click();
				   }
				}
			}
            
            jQuery(".zp-TinyMCESave").click(function()
			{
				var newShortcode = "[zotpressInTextBib";
				
				// Check fields
				jQuery.each(zpInTextBibDefaults, function(attr, value)
				{
					if ( attr == "style" || attr == "sortby" ) {
						if ( jQuery("[name="+attr+"]").val() != value ) {
							if ( jQuery.trim(jQuery("[name="+attr+"]").val()).length > 0 ) {
								newShortcode += ' '+attr+'="'+jQuery("[name="+attr+"]").val()+'"';
							}
						}
					}
					else {
						if ( jQuery("[name="+attr+"]:checked").val() != value ) {
							newShortcode += ' '+attr+'="'+jQuery("[name="+attr+"]:checked").val()+'"';
						}
					}
				});
				newShortcode += "]";
				
				// Save new shortcode
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, "<span class=\"zp-ZotpressShortcode list\">"+newShortcode+"</span>");
                tinyMCEPopup.close();
            });
        
        });
        </script>
    </head>
	
	<body id="zp-Zotpress-TinyMCE-Cite" class="zp-Zotpress-TinyMCE-Popup wp-core-ui">
		
		<!-- START OF OPTIONS -->
		<div id="zp-ZotpressMetaBox-InTextCreator-Options">
			
			<h4>Options</h4>
			
			<input type="button" class="zp-TinyMCESave button button-primary top" value="Save" />
			
			<div id="zp-ZotpressMetaBox-InTextCreator-Options-Inner">
				
				<label for="zp-ZotpressMetaBox-InTextCreator-Options-Style">Style:</label>
					<?php
					
					// See if default exists
					$zp_default_style = "apa";
					if (get_option("Zotpress_DefaultStyle")) $zp_default_style = get_option("Zotpress_DefaultStyle");
					
					?>
				<input id="zp-ZotpressMetaBox-InTextCreator-Options-Style" name="style" type="text" value="<?php echo $zp_default_style; ?>" />
				<p class="note">Styles listed <a title="Zotero-supported citation styles" rel="nofollow" href="http://www.zotero.org/styles">here</a>. Examples: apa, chicago-author-date, nature, modern-language-association.</p>
				
				<hr />
				
				<!--Sort by:-->
				<label for="zp-ZotpressMetaBox-InTextCreator-Options-SortBy">Sort by:</label>
				<select id="zp-ZotpressMetaBox-InTextCreator-Options-SortBy" name="sortby">
					<option id="default" value="default" rel="default" selected="selected">Latest Added</option>
					<option id="author" value="author">Author</option>
					<option id="date" value="date">Date</option>
					<option id="title" value="title">Title</option>
				</select>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Sort order:
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC">Ascending</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Sort-DESC">Descending</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Sort-DESC" name="sort" value="DESC" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Show images?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes" name="images" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Image-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Image-No" name="images" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Show title by year?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes" name="title" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Title-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Title-No" name="title" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Downloadable?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes" name="download" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Download-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Download-No" name="download" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Abstract?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes" name="abstract" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-No" name="abstract" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Notes?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes" name="notes" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Notes-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Notes-No" name="notes" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="zp-ZotpressMetaBox-Field">
					Citable (in RIS format)?
					<div class="right">
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes" name="cite" value="yes" />
						
						<label for="zp-ZotpressMetaBox-InTextCreator-Options-Cite-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Cite-No" name="cite" value="no" checked="checked" />
					</div>
				</div>
				
			</div>
		</div>
		<!-- END OF OPTIONS -->
		
		<input type="button" class="zp-TinyMCESave button button-primary" value="Save" />
	
	</body>
</html>