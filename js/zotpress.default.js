jQuery(document).ready(function() {


	/*
		
		NAVIGATION STYLES
		
	*/
	
    jQuery("div#zp-Zotpress div#zp-Zotpress-Navigation a.nav-item").click( function() {
        jQuery(this).addClass("active");
    });
	jQuery(".zp-List-Subcollection").focus(function() {
		jQuery(this).addClass("down");
	});
	
	
	
	/*
		
		COPYING ITEM KEYS ON CLICK
		
	*/
	
	jQuery('.zp-Entry-ID-Text span').click( function() {
		jQuery(this).parent().find('input').show().select();
		jQuery(this).hide();
	});
	jQuery('.zp-Entry-ID-Text input').blur( function() {
		jQuery(this).hide();
		jQuery(this).parent().find('span').show();
	});
	
	jQuery('.zp-Collection-Title .item_key_inner span').click( function() {
		jQuery(this).parent().find('input').show().select();
		jQuery(this).hide();
	});
	jQuery('.zp-Collection-Title .item_key_inner input').blur( function() {
		jQuery(this).hide();
		jQuery(this).parent().find('span').show();
	});
	
	
	
	/*
		
		FILTER CITATIONS
		
	*/
	
	// FILTER BY ACCOUNT
	
	jQuery('div#zp-Browse-Accounts').delegate("select#zp-FilterByAccount", "change", function()
	{
		var id = jQuery(this).val();
		window.location = "admin.php?page=Zotpress&account_id="+id;
	});
	
	
	// FILTER BY TAG
	
	jQuery('div#zp-Browse-Bar').delegate("select#zp-List-Tags", "change", function()
	{
		if ( jQuery(this).val() != "No tag selected" ) window.location = "admin.php?page=Zotpress&account_id="+jQuery('select#zp-FilterByAccount option:selected').val()+"&tag_id="+jQuery("option:selected", this).attr("rel");
	});
	
	
	
	/*
		
		CITATION IMAGE HOVER
		
	*/
	
	jQuery('div#zp-List').delegate("div.zp-Entry-Image", "hover", function () {
		jQuery(this).toggleClass("hover");
	});
	
	
	
	/*
		
		SET IMAGE FOR ENTRIES
		Thanks to http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
		
	*/
	
	var zp_uploader;
	
	jQuery('.zp-Entry-Image a.upload').click(function(e)
	{
        e.preventDefault();
		$this = jQuery(this);
		
        if (zp_uploader)
		{
            zp_uploader.open();
            return;
        }
		
        zp_uploader = wp.media.frames.file_frame = wp.media(
		{
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});
		
        zp_uploader.on( 'select', function()
		{
            attachment = zp_uploader.state().get('selection').first().toJSON();
			var zp_xml_url = jQuery('#ZOTPRESS_PLUGIN_URL').text()
					+ 'lib/actions/actions.php?image=true&api_user_id='+jQuery("#zp-FilterByAccount").find(":selected").attr("rel")+'&entry_id='+$this.attr('rel')+'&image_id='+attachment.id;
			
			// Save as featured image
			jQuery.get( zp_xml_url, {}, function(xml)
			{
				var $result = jQuery('result', xml).attr('success');
				
				if ( $result == "true" )
				{
					if ( $this.parent().find(".thumb").length > 0 ) {
						$this.parent().find(".thumb").attr("src", attachment.url);
					}
					else {
						$this.parent().addClass("hasimage");
						$this.parent().prepend("<img class='thumb' src='"+attachment.url+"' alt='image' />");
					}
				}
				else // Show errors
				{
					alert ("Sorry, featured image couldn't be set.");
				}
			});
        });
		
        zp_uploader.open();
		
    });
	
	
	// REMOVE FEATURED IMAGE
	
	jQuery(".zp-Entry-Image a.delete").click( function(e)
	{
        e.preventDefault();
		$this = jQuery(this);
		
		var zp_xml_url = jQuery('#ZOTPRESS_PLUGIN_URL').text() + 'lib/actions/actions.php?remove=image&entry_id='+$this.attr('rel');
		
		// Save as featured image
		jQuery.get( zp_xml_url, {}, function(xml)
		{
			var $result = jQuery('result', xml).attr('success');
			
			if ( $result == "true" )
			{
				$this.parent().removeClass("hasimage");
				$this.parent().find(".thumb").remove();
			}
			else // Show errors
			{
				alert ("Sorry, featured image couldn't be set.");
			}
		});
	});
    
    
});