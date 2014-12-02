(function()
{
	// Popup window
	var zpTinyMCEWidget = function(ed, url, type, shortcode, summary)
	{
		shortcode = typeof shortcode !== 'undefined' ? shortcode : false;
		summary = typeof summary !== 'undefined' ? summary : false;
		
		var zpurl = url + "/../widget/widget.tinymce.bib.php";
		if (type == 'cite') zpurl = url + "/../widget/widget.tinymce.cite.php";
		else if (type == 'list') zpurl = url + "/../widget/widget.tinymce.list.php";
		
		var zptitle = 'Zotpress Bibliography';
		if (type == 'cite') zptitle = 'Zotpress In-Text Citation';
		else if (type == 'list') zptitle = 'Zotpress In-Text Bibliography';
		
		ed.windowManager.open(
		{
			title: zptitle,
			url : zpurl,
			inline: 1,
			scrollbars: 1,
			width : 320,
			height : 240
		},
		{
			shortcode : shortcode,
			summary: summary
		});
	};
	
	
    tinymce.create('tinymce.plugins.zotpress',
	{  
        init : function(ed, url)
		{
			// Styles
			ed.contentCSS.push( url + "/css/zotpress.tinymce.css?" + new Date().getTime() );
			
			// Buttons
			ed.addButton('zotpress-cite',
			{
				title : 'Add/Edit In-Text Citation',
				image : url+'/images/icon-cite.png',
				onclick : function() {					
					var scShortcode = jQuery(ed.selection.getContent({format : 'html'})).find('.shortcode').text();
					var scSummary = jQuery(ed.selection.getContent({format : 'html'})).find('.summary').text();
					zpTinyMCEWidget(ed, url, 'cite', scShortcode, scSummary);
				}
			});
			ed.addButton('zotpress-list',
			{
				title : 'Add/Edit In-Text Bibliography',
				image : url+'/images/icon-list.png',
				onclick : function() {
					zpTinyMCEWidget(ed, url, 'list', ed.selection.getContent({format : 'text'}));
				}
			});
			ed.addButton('zotpress-bib',
			{
				title : 'Add/Edit Bibliography',
				image : url+'/images/icon-bib.png',
				onclick : function() {
					var scShortcode = jQuery(ed.selection.getContent({format : 'html'})).find('.shortcode').text();
					var scSummary = jQuery(ed.selection.getContent({format : 'html'})).find('.summary').text();
					zpTinyMCEWidget(ed, url, 'bib', scShortcode, scSummary);
				}
			});
			
			// Click events
			ed.onClick.add(function(ed, e)
			{
				if ( jQuery(e.target).hasClass('zp-ZotpressShortcode') )
				{
					ed.selection.select(e.target);
				}
				else if ( jQuery(e.target).hasClass('summary') )
				{
					ed.selection.select(e.target.parentElement); // cross-browser?
				}
			});
			ed.onDblClick.add(function(ed, e)
			{
				var zpTarget = e.target;
				if ( jQuery(zpTarget).hasClass('zp-ZotpressShortcode') || jQuery(zpTarget).hasClass('summary') )
				{
					if ( jQuery(zpTarget).hasClass('summary') ) zpTarget = zpTarget.parentElement;
					
					var zpType = 'bib';
					if ( jQuery(zpTarget).hasClass('cite') ) zpType = 'cite';
					else if ( jQuery(zpTarget).hasClass('list') ) zpType = 'list';
					
					if ( zpType == 'cite' || zpType == 'bib' )
					{
						//var scText = jQuery(zpTarget).text().split('=zp=');
						//zpTinyMCEWidget(ed, zpType, scText[1], scText[0]);
						
						var scShortcode = jQuery(ed.selection.getContent({format : 'html'})).find('.shortcode').text();
						var scSummary = jQuery(ed.selection.getContent({format : 'html'})).find('.summary').text();
						zpTinyMCEWidget(ed, url, zpType, scShortcode, scSummary);
					}
					else
					{
						zpTinyMCEWidget(ed, url, zpType, jQuery(zpTarget).text());
					}
				}
			});
			
        },  
        createControl : function(n, cm) {  
            return null;  
        }
    });
	
    tinymce.PluginManager.add('zotpress', tinymce.plugins.zotpress);  
}
)();