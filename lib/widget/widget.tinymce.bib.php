<?php

    // Include WordPress
    require(dirname(dirname(dirname(dirname(dirname( dirname( __FILE__ )))))) .'/wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Include database
    global $wpdb;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Zotpress Bibliography</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel='stylesheet' href='<?php echo includes_url(); ?>/css/buttons.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/wp-admin.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/colors-fresh.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/zotpress.metabox.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/smoothness/jquery-ui-1.8.11.custom.css' type='text/css' media='all' />
		<style>
		html { background: none; }
		body.zp-Zotpress-TinyMCE-Popup { min-width: 0; margin: 0; height: auto; }
		.zp-Zotpress-TinyMCE-Popup h4 { color: #666666; padding: 0.5em 1em; margin: 0.25em 0; }
		.zp-Zotpress-TinyMCE-Popup div#zp-ZotpressMetaBox-Biblio-Options { margin: 0; }
		.zp-Zotpress-TinyMCE-Popup #zp-ZotpressMetaBox-Biblio-Options-Inner { display: none; }
		#zp-TinyMCESave { float: right; margin: 0.6em 0.5em 1em; padding: 0.25em 1em; }
		#zp-ZotpressMetaBox-Account-ID { display: none; }
		.zp-Zotpress-TinyMCE-Popup input, .zp-Zotpress-TinyMCE-Popup select { font-family: "Open Sans", sans-serif; }
		.zp-Zotpress-TinyMCE-Popup label { font-size: 0.9em; padding: 0 0.5em 0 1.25em; }
		div#zp-ZotpressMetaBox-Biblio-Options p.note, div#zp-ZotpressMetaBox-InTextCreator-Options p.note { margin: 0 1em 0.75em 1.5em; }
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
        
            // Assumes: summaries with multiple citations are separated with "; " (with the space)
			
            var shortcode = tinyMCEPopup.getWindowArg("shortcode");
            var summary = tinyMCEPopup.getWindowArg("summary").split("; ");
		    var zpBibDefaults = { "author": false, "year": false, "style": "apa", "sortby": "default", "sort": "ASC", "images": "no", "download": "no", "notes": "no", "abstract": "no", "cite": "no", "title": "no", "limit": false };
			
			if ( shortcode != "" ) // Set fields based on shortcode
			{
				var attributes = shortcode.match(/[\w-]+="[^"]*"/g);
				
				if ( attributes != null )
				{
					for (var i = 0; i < attributes.length; i++)
					{
						var attribute = attributes[i].split("=");
						if ( attribute[0].replace(/"/gi, '') == "item")
						{
							var scItems = attribute[1].replace(/"/gi, '').split(",");
							
							if (scItems.length > 1) // multiple items
							{
								for (var s = 0; s < scItems.length; s++)
								{
									jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List-Inner").append("<div class='item' rel='"+scItems[s]+"'><span class='label'>"+summary[s]+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><div class='id'>Key: "+scItems[s]+"</div></div></div>\n");
								}
							}
							else if (scItems.length == 1) // one item
							{
								jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List-Inner").append("<div class='item' rel='"+scItems[0]+"'><span class='label'>"+summary[0]+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><div class='id'>Key: "+scItems[0]+"</div></div></div>\n");
							}
						}
						//else if ( attribute[0].replace(/"/gi, '') == "format")
						else if ( attribute[0].replace(/"/gi, '').match("author|year|style|limit") )
						{
							jQuery("input[name]="+attribute[0].replace(/"/gi, '')+"]").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "sortby" )
						{
							jQuery("select[name=sortby]").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "userid")
						{
							jQuery("#zp-ZotpressMetaBox-Account-ID").text(attribute[1].replace(/"/gi, ''));
						}
						else
						{
							jQuery("input:radio[name="+attribute[0].replace(/"/gi, '')+"][value="+attribute[1].replace(/"/gi, '')+"]").click();
						}
					}
				}
			}
            
            jQuery("#zp-TinyMCESave").click(function()
			{
				var zpItemsExist = false;
				
				// Check items
				if ( jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List .item").length > 0 )
				{
					zpItemsExist = true;
					summary = "";
					var newShortcode = "[zotpress userid=\""+jQuery("#zp-ZotpressMetaBox-Account-ID").text()+"\"";
					var newShortcodeItems = "";
					
					jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List .item").each(function()
					{
						if ( newShortcodeItems.length == 0 ) newShortcodeItems = ' item="'; else newShortcodeItems += ",";
						newShortcodeItems += jQuery(this).attr("rel");
						
						if ( summary.length == 0 ) summary = jQuery(".label", this).text(); else summary += "; " + jQuery(".label", this).text();
					});
					newShortcode += newShortcodeItems + '"';
					
					// Check fields
					jQuery.each(zpBibDefaults, function(attr, value)
					{
						if ( attr == "author" || attr == "year" || attr == "style" || attr == "sortby" || attr == "limit" ) {
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
					
					newShortcode += ']';
				}
				
				// Save new shortcode
                if ( zpItemsExist === true )
				{
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, "<span class=\"zp-ZotpressShortcode bib\"><span class='summary'>"+summary+"</span><span class='shortcode'>"+newShortcode+"</span></span>");
				}
				else // Or remove shortcode
				{
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, "");
				}
                tinyMCEPopup.close();
            });
        
        });
        </script>
    </head>
	<body id="zp-Zotpress-TinyMCE-Cite" class="zp-Zotpress-TinyMCE-Popup wp-core-ui">
		
		
	   
		<!-- START OF ZOTPRESS BIBLIOGRAPHY ------------------------------------------------------------------>
		<!-- NEXT: datatype [items, tags, collections], SEARCH items, tags, collections LIMIT -------------- -->
		
		<div id="zp-ZotpressMetaBox-Bibliography">
			
			<?php if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress;") > 1) { ?>
			<!-- START OF ACCOUNT -->
			<div id="zp-ZotpressMetaBox-Biblio-Account">
				<?php
				
				// See if default exists
				$zp_default_account = false;
				if (get_option("Zotpress_DefaultAccount"))
					$zp_default_account = get_option("Zotpress_DefaultAccount");
				
				if ($zp_default_account !== false)
					$zp_account = $wpdb->get_results( $wpdb->prepare( "SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress WHERE api_user_id = '".$zp_default_account."';" ) );
				else
					$zp_account = $wpdb->get_results( $wpdb->prepare( "SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress LIMIT 1;" ) );
				?>
				<span id="zp-ZotpressMetaBox-Account-ID"><?php echo $zp_account[0]->api_user_id; ?></span>
				<?php
				if (is_null($zp_account[0]->nickname) === false && $zp_account[0]->nickname != "")
					$zp_default_account = $zp_account[0]->nickname . " (" . $zp_account[0]->api_user_id . ")";
				
				?>
				Searching <?php echo $zp_default_account; ?>. <a target="zotpress" href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>">Change account?</a>
			</div>
			<!-- END OF ACCOUNT -->
			<?php } ?>
			
			
			<!-- START OF SEARCH -->
			<div id="zp-ZotpressMetaBox-Biblio-Citations">
				<input id="zp-ZotpressMetaBox-Biblio-Citations-Search" class="help" type="text" value="Type to search" />
				<input type="hidden" id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
				
			</div><div id="zp-ZotpressMetaBox-Biblio-Citations-List"><div id="zp-ZotpressMetaBox-Biblio-Citations-List-Inner"></div><hr class="clear" /></div>
			<!-- END OF SEARCH -->
			
			
			<!-- START OF OPTIONS -->
			<div id="zp-ZotpressMetaBox-Biblio-Options">
				
				<h4>Options <span class="toggle"></span></h4>
				
				<div id="zp-ZotpressMetaBox-Biblio-Options-Inner">
					
					<label for="zp-ZotpressMetaBox-Biblio-Options-Author">Filter by Author:</label>
					<input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Author" name="author" value="" />
					
					<hr />
					
					<label for="zp-ZotpressMetaBox-Biblio-Options-Year">Filter by Year:</label>
					<input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Year" name="year" value="" />
					
					<hr />
					
					<label for="zp-ZotpressMetaBox-Biblio-Options-Style">Style:</label>
						<?php
						
						// See if default exists
						$zp_default_style = "apa";
						if (get_option("Zotpress_DefaultStyle")) $zp_default_style = get_option("Zotpress_DefaultStyle");
						
						?>
					<input id="zp-ZotpressMetaBox-Biblio-Options-Style" name="style" type="text" value="<?php echo $zp_default_style; ?>" />
					<p class="note">Styles listed <a title="Zotero-supported citation styles" rel="nofollow" href="http://www.zotero.org/styles">here</a>. Examples: apa, chicago-author-date, nature, modern-language-association.</p>
					
					<hr />
					
					<!--Sort by:-->
					<label for="zp-ZotpressMetaBox-Biblio-Options-SortBy">Sort by:</label>
					<select id="zp-ZotpressMetaBox-Biblio-Options-SortBy" name="sortby">
						<option id="zp-bib-default" value="default" rel="default" selected="selected">Default</option>
						<option id="zp-bib-author" value="author">Author</option>
						<option id="zp-bib-date" value="date">Date</option>
						<option id="zp-bib-title" value="title">Title</option>
					</select>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Sort order:
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC">Ascending</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Sort-DESC">Descending</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-No" name="sort" value="DESC" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Show images?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Image-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-Yes" name="images" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Image-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-No" name="images" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Show title by year?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Title-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-Yes" name="title" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Title-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-No" name="title" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Downloadable?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Download-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-Yes" name="download" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Download-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-No" name="download" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Abstract?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes" name="abstract" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-No" name="abstract" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Notes?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes" name="notes" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Notes-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-No" name="notes" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<div class="zp-ZotpressMetaBox-Field">
						Citable (in RIS format)?
						<div class="right">
						<label for="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes">Yes</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes" name="cite" value="yes" />
						
						<label for="zp-ZotpressMetaBox-Biblio-Options-Cite-No">No</label>
						<input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-No" name="cite" value="no" checked="checked" />
						</div>
					</div>
					
					<hr />
					
					<label for="zp-ZotpressMetaBox-Biblio-Options-Limit">Limit by:</label>
					<input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Limit" name="limit" value="" />
					
				</div>
			</div>
			<!-- END OF OPTIONS -->
			
		</div><!-- #zp-ZotpressMetaBox-Bibliography -->
		
		<!-- END OF ZOTPRESS BIBLIOGRAPHY --------------------------------------------------------------------->
		
		<input type="button" id="zp-TinyMCESave" class="button button-primary button-large" value="Save" />
	
	
	</body>
</html>