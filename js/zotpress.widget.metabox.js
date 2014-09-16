jQuery(document).ready(function()
{
    
    
    /****************************************************************************************
     *
     *     ZOTPRESS METABOX
     *
     ****************************************************************************************/
    
    if ( jQuery("#zp-ZotpressMetaBox").length > 0 ) jQuery("#zp-ZotpressMetaBox").tabs();
    
    
    
    /****************************************************************************************
     *
     *     ZOTPRESS BIBLIO CREATOR
     *
     ****************************************************************************************/
    
    var zpBiblio = {
		"author": false, "year": false, "style": false, "sortby": false, "sort": false, "image": false,
		"download": false, "notes": false, "zpabstract": false, "cite": false, "title": false, "limit": false,
		"items": []
		};
	
	var zpAutoCompleteURL = "../wp-content/plugins/zotpress/lib/widget/widget.metabox.search.php";
	if ( jQuery(".zp-Zotpress-TinyMCE-Popup").length > 0 ) zpAutoCompleteURL = "widget.metabox.search.php";
    
    jQuery("input#zp-ZotpressMetaBox-Biblio-Citations-Search")
        .bind( "keydown", function( event ) {
            // Don't navigate away from the field on tab when selecting an item
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }
            // Don't submit the form when pressing enter
            if ( event.keyCode === 13 ) {
                event.preventDefault();
            }
        })
        .bind( "focus", function( event ) {
            // Remove help text on focus
            if (jQuery(this).val() == "Type to search") {
                jQuery(this).val("");
                jQuery(this).removeClass("help");
            }
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").hide('fast');
        })
        .bind( "blur", function( event ) {
            // Add help text on blur, if nothing there
            if (jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val("Type to search");
                jQuery(this).addClass("help");
            }
        })
        .autocomplete({
            source: zpAutoCompleteURL,
            minLength: 3,
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            open: function () {
				var widget = jQuery(this).data('ui-autocomplete'),
						menu = widget.menu,
						$ul = menu.element;
				menu.element.addClass("zp-autocomplete");
                //jQuery(this).data("autocomplete").menu.element.addClass("zp-autocomplete");
                jQuery(".zp-autocomplete .ui-menu-item:first").addClass("first");
                
                // Change width of autocomplete dropdown based on input size
                if ( jQuery("#ZotpressMetaBox").parent().attr("id") == "normal-sortables" )
                    menu.element.addClass("zp-autocomplete-wide");
                else
                    menu.element.removeClass("zp-autocomplete-wide");
            },
            select: function( event, ui )
            {
                // Check if item is already in the list
                var check = false;
                jQuery.each(zpBiblio.items, function(index, item) {
                    if (item.itemkey == ui.item.value)
                        check = true;
                });
				
				// Check for duplicates in popup
				if ( jQuery(".zp-Zotpress-TinyMCE-Popup").length > 0 )
				{
					if ( jQuery(".item[rel="+ui.item.value+"]").length > 0 )
						check = true;
				}
				
                if (check === false)
				{
                    // Add to list, if not already there
                    zpBiblio.items.push(ui.item.value);
                    // Add visual indicator
                    var uilabel = (ui.item.label).split(")",1) + ")";
                    jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List-Inner").append("<div class='item' rel='"+ui.item.value+"'><span class='label'>"+uilabel+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><div class='id'>Key: "+ui.item.value+"</div></div></div>\n");
                    // Remove text from input
                    jQuery("input#zp-ZotpressMetaBox-Biblio-Citations-Search").val("").focus();
                }
                return false;
            }
        });
    
    
    // ITEM
    jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List div.item")
        .livequery('click', function(event)
        {
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").hide('fast');
        });

    
    // ITEM TOGGLE BUTTON
    jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List div.item .toggle")
        .livequery('click', function(event)
        {
            var $parent = jQuery(this).parent();
            
            // Toggle action
            jQuery(this).toggleClass("active");
            jQuery(".options", $parent).slideToggle('fast');
        });
    
    
    // ITEM CLOSE BUTTON
    jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List div.item .delete")
        .livequery('click', function(event)
        {
            var $parent = jQuery(this).parent();
            
            // Make sure toggle is closed
            if (jQuery(".toggle", $parent).hasClass("active")) {
                jQuery(this).toggleClass("active");
                jQuery(".options", $parent).slideToggle('fast');
            }
            
            // Remove item from JSON
            jQuery.each(zpBiblio.items, function(index, item) {
                if (item.itemkey == $parent.attr("rel"))
                    zpBiblio.items.splice(index, 1);
            });
            
            // Remove visual indicator
            $parent.remove();
            
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").hide('fast');
            //alert(JSON.stringify(zpBiblio));
        });
    
    
    // OPTIONS PANEL
    jQuery("#zp-ZotpressMetaBox-Biblio-Options")
        .click(function(event)
        {
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").hide('fast');
        });
    
    
    // OPTIONS BUTTON
    jQuery("#zp-ZotpressMetaBox-Biblio-Options h4 .toggle")
        .click(function(event)
        {
            jQuery(this).toggleClass("active");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Inner").slideToggle('fast');
        });
    
    
    // GENERATE SHORTCODE BUTTON
    jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Button")
        .click(function(event)
        {
            // Update page parameters for all citations - N/A because regular bibs don't have page numbers?
            //jQuery("#zp-ZotpressMetaBox-Biblio-Citations-List .item").each(function(vindex, vitem) {
            //    if (jQuery.trim(jQuery("input", vitem).val()).length > 0)
            //    {
            //        jQuery.each(zpBiblio.items, function(index, item) {
            //            if (item.itemkey == jQuery(vitem).attr("rel"))
            //                item.pages = jQuery.trim(jQuery("input", vitem).val());
            //        });
            //    }
            //});
            
            // Grab the author, year, style, sortby options
            zpBiblio.author = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Author").val());
            zpBiblio.year = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Year").val());
            zpBiblio.style = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style").val());
            zpBiblio.sortby = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-SortBy").val());
            zpBiblio.limit = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Limit").val());
            
            // Grab the sort order option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Sort-ASC").is(':checked') === true)
                zpBiblio.sort = "ASC";
            else
                zpBiblio.sort = "DESC";
            
            // Grab the image option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Image-Yes").is(':checked') === true)
                zpBiblio.image = "yes";
            else
                zpBiblio.image = "";
            
            // Grab the title option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Title-Yes").is(':checked') === true)
                zpBiblio.title = "yes";
            else
                zpBiblio.title = "";
            
            // Grab the download option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Download-Yes").is(':checked') === true)
                zpBiblio.download = "yes";
            else
                zpBiblio.download = "";
            
            // Grab the abstract option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes").is(':checked') === true)
                zpBiblio.zpabstract = "yes";
            else
                zpBiblio.zpabstract = "";
            
            // Grab the notes option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Notes-Yes").is(':checked') === true)
                zpBiblio.notes = "yes";
            else
                zpBiblio.notes = "";
            
            // Grab the cite option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Cite-Yes").is(':checked') === true)
                zpBiblio.cite = "yes";
            else
                zpBiblio.cite = "";
            
            // Generate bibliography shortcode
            var zpBiblioShortcode = "[zotpress";
            
            if (jQuery("#zp-ZotpressMetaBox-Biblio-Account").length > 0) zpBiblioShortcode += " userid=\"" + jQuery("#zp-ZotpressMetaBox-Biblio-Account").attr("rel") + "\"";
            if (zpBiblio.items.length > 0) zpBiblioShortcode += " items=\"" + zpBiblio.items + "\"";
            if (zpBiblio.author != "") zpBiblioShortcode += " author=\"" + zpBiblio.author + "\"";
            if (zpBiblio.year != "") zpBiblioShortcode += " year=\"" + zpBiblio.year + "\"";
            if (zpBiblio.style != "") zpBiblioShortcode += " style=\"" + zpBiblio.style + "\"";
            if (zpBiblio.sortby != "" && zpBiblio.sortby != "default") zpBiblioShortcode += " sortby=\"" + zpBiblio.sortby + "\"";
            if (zpBiblio.sort != "") zpBiblioShortcode += " sort=\"" + zpBiblio.sort + "\"";
            if (zpBiblio.image != "") zpBiblioShortcode += " showimage=\"" + zpBiblio.image + "\"";
            if (zpBiblio.download != "") zpBiblioShortcode += " download=\"" + zpBiblio.download + "\"";
            if (zpBiblio.zpabstract != "") zpBiblioShortcode += " abstract=\"" + zpBiblio.zpabstract + "\"";
            if (zpBiblio.notes != "") zpBiblioShortcode += " notes=\"" + zpBiblio.notes + "\"";
            if (zpBiblio.cite != "") zpBiblioShortcode += " cite=\"" + zpBiblio.cite + "\"";
            if (zpBiblio.title != "") zpBiblioShortcode += " title=\"" + zpBiblio.title + "\"";
            if (zpBiblio.limit != "") zpBiblioShortcode += " limit=\"" + zpBiblio.limit + "\"";
            
            zpBiblioShortcode += "]";
            
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Text").text(zpBiblioShortcode);
            
            // Reveal shortcode
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").slideDown('fast');
            
            //alert(JSON.stringify(zpBiblio));
        });
    
    
    // CLEAR SHORTCODE BUTTON
    jQuery("#zp-ZotpressMetaBox-Biblio-Clear-Button")
        .click(function(event)
        {
            // Clear zpBiblio
            zpBiblio.author = false;
            zpBiblio.year = false;
            zpBiblio.style = false;
            zpBiblio.sortby = false;
            zpBiblio.sort = false;
            zpBiblio.image = false;
            zpBiblio.download = false;
            zpBiblio.notes = false;
            zpBiblio.zpabstract = false;
            zpBiblio.cite = false;
            zpBiblio.title = false;
            zpBiblio.limit = false;
            jQuery.each(zpBiblio.items, function(index, item) {
                zpBiblio.items.splice(index, 1);
            });
            
            // Hide options and shortcode
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Inner").slideUp('fast');
            jQuery("#zp-ZotpressMetaBox-Biblio-Options h4 .toggle").removeClass("active");
            jQuery("#zp-ZotpressMetaBox-Biblio-Shortcode-Inner").slideUp('fast');
            
            // Reset form inputs
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Author").val("");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Year").val("");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Limit").val("");
            
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style").val(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style option[rel='default']").val());
            
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-SortBy option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-SortBy").val(jQuery("#zp-ZotpressMetaBox-Biblio-Options-SortBy option[rel='default']").val());
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Sort-DESC").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Sort-ASC").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Image-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Image-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Title-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Title-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Download-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Download-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Abstract-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Notes-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Notes-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Cite-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Cite-No").attr('checked', 'checked');
            
            // Remove visual indicators
            jQuery("div#zp-ZotpressMetaBox-Biblio-Citations-List div.item").remove();
            
            //alert(JSON.stringify(zpBiblio));
        });
    
    
    
    /****************************************************************************************
     *
     *     ZOTPRESS IN-TEXT CREATOR
     *
     ****************************************************************************************/
    
    var zpInText = { "format": false, "etal": false, "and": false, "separator": false, "style": false, "sortby": false, "sort": false, "image": false, "download": false, "notes": false, "zpabstract": false, "cite": false, "title": false, "items": [] };
	var zpAutoCompleteURL = "../wp-content/plugins/zotpress/lib/widget/widget.metabox.search.php";
	if ( jQuery(".zp-Zotpress-TinyMCE-Popup").length > 0 ) zpAutoCompleteURL = "widget.metabox.search.php";
    
    jQuery("input#zp-ZotpressMetaBox-Citations-Search")
        .bind( "keydown", function( event ) {
            // Don't navigate away from the field on tab when selecting an item
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }
            // Don't submit the form when pressing enter
            if ( event.keyCode === 13 ) {
                event.preventDefault();
            }
        })
        .bind( "focus", function( event ) {
            // Remove help text on focus
            if (jQuery(this).val() == "Type to search") {
                jQuery(this).val("");
                jQuery(this).removeClass("help");
            }
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").hide('fast');
        })
        .bind( "blur", function( event ) {
            // Add help text on blur, if nothing there
            if (jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val("Type to search");
                jQuery(this).addClass("help");
            }
        })
        .autocomplete({
            source: zpAutoCompleteURL,
            minLength: 3,
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            open: function () {
				var widget = jQuery(this).data('ui-autocomplete'),
						menu   = widget.menu,
						$ul    = menu.element;
				menu.element.addClass("zp-autocomplete");
                //jQuery(this).data("autocomplete").menu.element.addClass("zp-autocomplete");
                jQuery(".zp-autocomplete .ui-menu-item:first").addClass("first");
                
                // Change width of autocomplete dropdown based on input size
                if ( jQuery("#ZotpressMetaBox").parent().attr("id") == "normal-sortables" )
                    menu.element.addClass("zp-autocomplete-wide");
                else
                    menu.element.removeClass("zp-autocomplete-wide");
            },
            select: function( event, ui )
            {
                // Check if item is already in the list
                var check = false;
                jQuery.each(zpInText.items, function(index, item) {
                    if (item.itemkey == ui.item.value)
                        check = true;
                });
				// Check for duplicates in popup
				if ( jQuery(".zp-Zotpress-TinyMCE-Popup").length > 0 )
				{
					if ( jQuery(".item[rel="+ui.item.value+"]").length > 0 ) check = true;
				}
                
                if (check === false) {
                    // Add to list, if not already there
                    zpInText.items.push({ "itemkey": ui.item.value, "pages": false});
                    // Add visual indicator
                    var uilabel = (ui.item.label).split(")",1) + ")";
                    jQuery("#zp-ZotpressMetaBox-Citations-List-Inner").append("<div class='item' rel='"+ui.item.value+"'><span class='label'>"+uilabel+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><label for='zp-Item-"+ui.item.value+"'>Page(s):</label><input id='zp-Item-"+ui.item.value+"' type='text' /><div class='id'>Key: "+ui.item.value+"</div></div></div>\n");
                    // Remove text from input
                    jQuery("input#zp-ZotpressMetaBox-Citations-Search").val("").focus();
                }
                //alert(JSON.stringify(zpInText));
                return false;
            }
        });
    
    
    //// ACCOUNTS
    //jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Account option")
    //    .click(function()
    //    {
    //        jQuery("#zp-DefaultAccount").text(jQuery(this).val());
    //    });
    
    
    // ITEM
    jQuery("#zp-ZotpressMetaBox-Citations-List div.item")
        .livequery('click', function(event)
        {
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").hide('fast');
        });

    
    // ITEM TOGGLE BUTTON
    jQuery("#zp-ZotpressMetaBox-Citations-List div.item .toggle")
        .livequery('click', function(event)
        {
            var $parent = jQuery(this).parent();
            
            // Toggle action
            jQuery(this).toggleClass("active");
            jQuery(".options", $parent).slideToggle('fast');
        });
    
    
    // ITEM CLOSE BUTTON
    jQuery("#zp-ZotpressMetaBox-Citations-List div.item .delete")
        .livequery('click', function(event)
        {
            var $parent = jQuery(this).parent();
            
            // Make sure toggle is closed
            if (jQuery(".toggle", $parent).hasClass("active")) {
                jQuery(this).toggleClass("active");
                jQuery(".options", $parent).slideToggle('fast');
            }
            
            // Remove item from JSON
            jQuery.each(zpInText.items, function(index, item) {
                if (item.itemkey == $parent.attr("rel"))
                    zpInText.items.splice(index, 1);
            });
            
            // Remove visual indicator
            $parent.remove();
            
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").hide('fast');
            //alert(JSON.stringify(zpInText));
        });
    
    
    // OPTIONS PANEL
    jQuery("#zp-ZotpressMetaBox-InTextCreator-Options")
        .click(function(event)
        {
            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").hide('fast');
        });
    
    
    // OPTIONS BUTTON
    jQuery("#zp-ZotpressMetaBox-InTextCreator-Options h4 .toggle")
        .click(function(event)
        {
            jQuery(this).toggleClass("active");
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Inner").slideToggle('fast');
        });
    
    
    // GENERATE SHORTCODE BUTTON
    jQuery("#zp-ZotpressMetaBox-InTextCreator-Generate-Button")
        .click(function(event)
        {
            // Update page parameters for all citations
            jQuery("#zp-ZotpressMetaBox-Citations-List .item").each(function(vindex, vitem) {
                if (jQuery.trim(jQuery("input", vitem).val()).length > 0)
                {
                    jQuery.each(zpInText.items, function(index, item) {
                        if (item.itemkey == jQuery(vitem).attr("rel"))
                            item.pages = jQuery.trim(jQuery("input", vitem).val());
                    });
                }
            });
            
            // Grab the format option
            zpInText.format = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Format").val());
            
            // Grab the et al option
            zpInText.etal = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Etal").val());
            
            // Grab the and option
            zpInText.and = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-And").val());
            
            // Grab the separator option
            zpInText.separator = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Separator").val());
            
            // Grab the style option
            zpInText.style = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Style").val());
            
            // Grab the sortby option
            zpInText.sortby = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-SortBy").val());
            
            // Grab the sort order option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC").is(':checked') === true)
                zpInText.sort = "ASC";
            else
                zpInText.sort = "DESC";
            
            // Grab the image option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes").is(':checked') === true)
                zpInText.image = "yes";
            else
                zpInText.image = "";
            
            // Grab the title option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes").is(':checked') === true)
                zpInText.title = "yes";
            else
                zpInText.title = "";
            
            // Grab the download option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes").is(':checked') === true)
                zpInText.download = "yes";
            else
                zpInText.download = "";
            
            // Grab the abstract option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes").is(':checked') === true)
                zpInText.zpabstract = "yes";
            else
                zpInText.zpabstract = "";
            
            // Grab the notes option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes").is(':checked') === true)
                zpInText.notes = "yes";
            else
                zpInText.notes = "";
            
            // Grab the cite option
            if (jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes").is(':checked') === true)
                zpInText.cite = "yes";
            else
                zpInText.cite = "";
            
            // Generate in-text shortcode
            var zpIntTextVal = "[zotpressInText item=\"";
            jQuery.each(zpInText.items, function(index, item) {
                zpIntTextVal += "{" + item.itemkey;
                if (item.pages !== false) zpIntTextVal += "," + item.pages;
                zpIntTextVal += "},";
            });
            zpIntTextVal = zpIntTextVal.substring(0, zpIntTextVal.length - 1) + "\""; // get rid of last comma
            
            if (jQuery("#zp-ZotpressMetaBox-Biblio-Account").length > 0)
				zpIntTextVal += " userid=\"" + jQuery("#zp-ZotpressMetaBox-Biblio-Account").attr("rel") + "\"";
			
            if (zpInText.format != "" && zpInText.format != "(%a%, %d%, %p%)")
                zpIntTextVal += " format=\"" + zpInText.format + "\"";
			
			if (zpInText.etal != "" && zpInText.etal != "default")
				zpIntTextVal += " etal=\"" + zpInText.etal + "\"";
			
			if (zpInText.and != "" && zpInText.and != "default")
				zpIntTextVal += " and=\"" + zpInText.and + "\"";
			
			if (zpInText.separator != "" && zpInText.separator != "default")
				zpIntTextVal += " separator=\"" + zpInText.separator + "\"";
            
            zpIntTextVal += "]";
            jQuery("#zp-ZotpressMetaBox-InTextCreator-InText").val(zpIntTextVal);
            
            // Generate in-text bibliography shortcode
            var zpInTextShortcode = "[zotpressInTextBib";
            
            if (zpInText.style != "") zpInTextShortcode += " style=\"" + zpInText.style + "\"";
            if (zpInText.sortby != "" && zpInText.sortby != "default") zpInTextShortcode += " sortby=\"" + zpInText.sortby + "\"";
            if (zpInText.sort != "") zpInTextShortcode += " sort=\"" + zpInText.sort + "\"";
            if (zpInText.image != "") zpInTextShortcode += " showimage=\"" + zpInText.image + "\"";
            if (zpInText.download != "") zpInTextShortcode += " download=\"" + zpInText.download + "\"";
            if (zpInText.zpabstract != "") zpInTextShortcode += " abstract=\"" + zpInText.zpabstract + "\"";
            if (zpInText.notes != "") zpInTextShortcode += " notes=\"" + zpInText.notes + "\"";
            if (zpInText.cite != "") zpInTextShortcode += " cite=\"" + zpInText.cite + "\"";
            if (zpInText.title != "") zpInTextShortcode += " title=\"" + zpInText.title + "\"";
            
            zpInTextShortcode += "]";
            
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Text-Bib").val(zpInTextShortcode);
            
            // Reveal shortcode
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").slideDown('fast');
            
            //alert(JSON.stringify(zpInText));
        });
    
    
    // CLEAR SHORTCODE BUTTON
    jQuery("#zp-ZotpressMetaBox-InTextCreator-Clear-Button")
        .click(function(event)
        {
            // Clear zpInText
            zpInText.format = false;
            zpInText.etal = false;
            zpInText.and = false;
            zpInText.separator = false;
            zpInText.style = false;
            zpInText.sortby = false;
            zpInText.sort = false;
            zpInText.image = false;
            zpInText.download = false;
            zpInText.zpabstract = false;
            zpInText.notes = false;
            zpInText.cite = false;
            zpInText.title = false;
            jQuery.each(zpInText.items, function(index, item) {
                zpInText.items.splice(index, 1);
            });
            
            // Hide options and shortcode
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Inner").slideUp('fast');
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options h4 .toggle").removeClass("active");
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner").slideUp('fast');
            
            // Reset form inputs
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Format").val("(%a%, %d%, %p%)");
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Etal").val("default");
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-And").val("default");
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Separator").val("default");
            
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Style option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Style").val(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-Style option[rel='default']").val());
            
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-SortBy option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-SortBy").val(jQuery("#zp-ZotpressMetaBox-InTextCreator-Options-SortBy option[rel='default']").val());
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Sort-DESC").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Image-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Title-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Download-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Abstract-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Notes-No").attr('checked', 'checked');
            
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes").removeAttr('checked');
            jQuery("input#zp-ZotpressMetaBox-InTextCreator-Options-Cite-No").attr('checked', 'checked');
            
            // Remove visual indicators
            jQuery("div#zp-ZotpressMetaBox-Citations-List div.item").remove();
            
            //alert(JSON.stringify(zpInText));
        });
    
    
});