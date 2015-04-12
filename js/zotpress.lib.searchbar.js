jQuery(document).ready(function()
{
    
    
    /****************************************************************************************
     *
     *     ZOTPRESS LIB SEARCHBAR
     *
     ****************************************************************************************/
	
	// Generate autocomplete query URL
	var zpAutoCompleteURL = jQuery("#ZOTPRESS_PLUGIN_URL").val()+"/lib/shortcode/shortcode.lib.search.php?user="
			+jQuery("#ZOTPRESS_USER").val();
	
	// Deal with possible max results
	if ( jQuery("#ZOTPRESS_AC_MAXRESULTS").val().length > 0 )
		zpAutoCompleteURL += "&maxresults=" + jQuery("#ZOTPRESS_AC_MAXRESULTS").val();
	
	// Set max per page (pagination)
	window.zpPage = 1;
	window.zpMaxPerPage = 10;
	if ( jQuery("#ZOTPRESS_AC_MAXPERPAGE").val().length > 0 )
		window.zpMaxPerPage = jQuery("#ZOTPRESS_AC_MAXPERPAGE").val();
	
	// Deal with change in filter
	if ( jQuery("input[name=zpSearchFilters]").length > 0 )
			zpAutoCompleteURL = zpAutoCompleteURL+"&filter="+jQuery("input[name=zpSearchFilters]:checked").val();
	
	jQuery("input[name='zpSearchFilters']").click(function()
	{
		// Generate autocomplete query URL
		zpAutoCompleteURL = jQuery("#ZOTPRESS_PLUGIN_URL").val()+"/lib/shortcode/shortcode.lib.search.php?user="
				+jQuery("#ZOTPRESS_USER").val();
		
		// Deal with change in filter
		if ( jQuery("input[name=zpSearchFilters]").length > 0 )
				zpAutoCompleteURL = zpAutoCompleteURL+"&filter="+jQuery(this).val();
		
		// Update autocomplete URL
		jQuery("input#zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpAutoCompleteURL );
		
		// If there's already text, search again
		if ( jQuery("input#zp-Zotpress-SearchBox-Input").val().length > 0 )
			jQuery("input#zp-Zotpress-SearchBox-Input").autocomplete("search");
	});
    
	
	// Set up autocomplete
    jQuery("input#zp-Zotpress-SearchBox-Input")
        .bind( "keydown", function( event )
		{
            // Don't navigate away from the input on tab when selecting an item
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }
            // Don't submit the form when pressing enter
            if ( event.keyCode === 13 ) {
                event.preventDefault();
            }
        })
        .bind( "focus", function( event )
		{
            // Remove help text on focus
            if (jQuery(this).val() == "Type to search") {
                jQuery(this).val("");
                jQuery(this).removeClass("help");
            }
        })
        .bind( "blur", function( event )
		{
            // Add help text on blur, if nothing there
            if (jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val("Type to search");
                jQuery(this).addClass("help");
            }
        })
        .autocomplete({
            source: zpAutoCompleteURL,
            minLength: jQuery("#ZOTPRESS_AC_MINLENGTH").val(),
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
			search: function( event, ui )
			{
				// Show loading icon
				jQuery("#zp-List .zpSearchLoading").addClass("show");
				
				// Remove old results
				jQuery("#zpSearchResultsContainer").empty();
			},
			response: function( event, ui )
			{
				// Remove loading icon
				jQuery("#zp-List .zpSearchLoading").removeClass("show");
				
				// Display list of search results
				jQuery.each(ui.content, function( index, value )
				{
					var temp = "<div class='zpSearchResultsItem hidden'>"+value.item;
					
					if ( jQuery("input[name=zpSearchFilters]:checked").length > 0 )
					{
						temp += "<span class='item_key'>";
						
						//if ( jQuery("input[name=zpSearchFilters]:checked").length == 0 ) temp += "Item Key: ";
						if ( jQuery("input[name=zpSearchFilters]:checked").val() == "tags" ) temp += "Tag: ";
						if ( jQuery("input[name=zpSearchFilters]:checked").val() == "collections" ) temp += "Collection: ";
						
						temp += value.item_key;
					}
					jQuery("#zpSearchResultsContainer").append(temp+"</div>\n");
				});
				
				// Update pagination
				zpACPagination(true);
			},
            open: function ()
			{
				// Don't show the dropdown
				jQuery(".ui-autocomplete").hide();
            }
        });
    
	
	// Set up pagination
	function zpACPagination($isNewQuery)
	{
		// e.g.
		// window.zpMaxPerPage = 10
		// window.zpPage = 3
		// 0-9, 10-19, 20-29 ...
		
		if ( $isNewQuery == true ) window.zpPage = 1;
		
		// Show the results given the current pagination page
		jQuery("#zpSearchResultsContainer")
			.children()
			.addClass("hidden")
			.slice( (window.zpPage-1)*window.zpMaxPerPage, (window.zpPage*window.zpMaxPerPage) )
			.removeClass("hidden");
		
		// Generate paging menu
		if ( $isNewQuery == true || jQuery("#zpSearchResultsPaging").children().length == 0 )
		{
			jQuery("#zpSearchResultsPaging").empty();
			
			for (i = 1; i < Math.ceil(jQuery("#zpSearchResultsContainer").children().length/window.zpMaxPerPage); i++)
			{
				if ( i == 1 )
				{
					jQuery("#zpSearchResultsPaging").append("<span class='title'>Page</span>");
					jQuery("#zpSearchResultsPaging").append("<a class='selected' href='javascript:void(0)'>"+i+"</a>");
				}
				else
				{
					jQuery("#zpSearchResultsPaging").append("<a href='javascript:void(0)'>"+i+"</a>");
				}
			}
		}
	};
	
	jQuery('body').on("click", "#zpSearchResultsPaging a", function()
	{
		// Highlight this link
		jQuery("#zpSearchResultsPaging a").removeClass("selected");
		jQuery(this).addClass("selected");
		
		// Update pagination page
		window.zpPage = jQuery(this).text();
		
		// Update
		zpACPagination(false);
	});
	
});