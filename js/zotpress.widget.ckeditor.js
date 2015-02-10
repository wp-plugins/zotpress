jQuery(document).ready(function() {


    /*
    
        CKEDITOR TABS
    
    */
    
    jQuery("div#zp-Zotpress-CkEditor").tabs();
    
    
    
    /*
    
        CKEDITOR CONTEXT MENU
    
    */
    
    if (jQuery("#wp-content-editor-container").length > 0)
    {
        
        var iframeWindow = null;
        
        CKEDITOR.plugins.add('zotpress',
        {
            requires: [ 'iframedialog' ],
            
            init: function(editor)
            {
                var pluginName = 'zotpress';
                var zotpressPath = this.path + '../../../../zotpress';
                var zotpressCurrentShortcode = "";
                var zotpressCurrentCitation = "";
                var zotpressCurrentPages = "";
                var zotpressCurrentPlaceholder = "";
                var zotpressTotalShortcodes = 0;
                
                
                // Add Zotpress CKEDITOR CSS
                CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss, zotpressPath + '/zotpress.metabox.css'];
                
                // Set up Zotpress commands
                editor.addCommand( 'zotpress_AddCitation', new CKEDITOR.dialogCommand('zotpress_AddCitation_Dialog') );
                editor.addCommand( 'zotpress_AddBibliography', new CKEDITOR.dialogCommand('zotpress_AddBibliography_Dialog') );
                
                
                // Zotpress GUI menu item -- NOT WORKING?! Can't overwrite
                //editor.ui.addButton(pluginName, {
                //    label: 'Add Citation',
                //    group: 'zotpressgroup',
                //    icon: zotpressPath + '/zotpress/images/icon.png',
                //    command: 'zotpresscommand'
                //});
                
                
                // Zotpress context menu items
                if (editor.addMenuItem)
                {
                    editor.addMenuGroup('zotpress_MenuGroup');
                    
                    // Add or Edit Citation
                    editor.addMenuItem('zotpress_AddCiteItem', {
                        label: 'Add Citation',
                        group: 'zotpress_MenuGroup',
                        icon: zotpressPath + '/images/icon-add.png',
                        command: 'zotpress_AddCitation'
                    });
                    editor.addMenuItem('zotpress_EditCiteItem', {
                        label: 'Edit Citation',
                        group: 'zotpress_MenuGroup',
                        icon: zotpressPath + '/images/icon-edit.png',
                        command: 'zotpress_AddCitation'
                    });
                    
                    // Add or Edit Bibliography
                    editor.addMenuItem('zotpress_AddBibItem', {
                        label: 'Add Bibliography',
                        group: 'zotpress_MenuGroup',
                        icon: zotpressPath + '/images/icon-add.png',
                        command: 'zotpress_AddBibliography'
                    });
                    editor.addMenuItem('zotpress_EditBibItem', {
                        label: 'Edit Bibliography',
                        group: 'zotpress_MenuGroup',
                        icon: zotpressPath + '/images/icon-edit.png',
                        command: 'zotpress_AddBibliography'
                    });
                    
                } // editor.addMenuItem
                
                
                // Context menu
                if (editor.contextMenu)
                {
                    editor.contextMenu.addListener(function(element)
                    {
                        var zp_parents = element.getParents("span");
                        
                        if (zp_parents[1].getName() == "span")
                        {
                            if (zp_parents[1].getAttribute("class") == "zp-Zotpress-Citation")
                            {
                                // Set current vars
                                zotpressCurrentPlaceholder = zp_parents[1].getAttribute("id");
                                zotpressCurrentShortcode = jQuery.trim(zp_parents[1].getChild(0).getText());
                                zotpressCurrentCitation = jQuery.trim(zp_parents[1].getChild(1).getText());
                                if (jQuery.trim(zp_parents[1].getChild(0).getAttribute("rel")).length > 0)
                                    zotpressCurrentPages = jQuery.trim(zp_parents[1].getChild(0).getAttribute("rel"));
                                
                                return { zotpress_EditCiteItem: CKEDITOR.TRISTATE_ON };
                            }
                            else if (parents[1].getAttribute("class") == "zp-Zotpress-Bibliography")
                            {
                                // Set current vars
                                zotpressCurrentPlaceholder = zp_parents[1].getAttribute("id");
                                zotpressCurrentShortcode = jQuery.trim(zp_parents[1].getChild(0).getText());
                                
                                return { zotpress_EditBibItem: CKEDITOR.TRISTATE_ON };
                            }
                        }
                        
                        return { zotpress_AddCiteItem: CKEDITOR.TRISTATE_ON, zotpress_AddBibItem: CKEDITOR.TRISTATE_ON };
                    }); 
                } // editor.contextMenu
                
                
                // Grab Account data from select
                var zp_accounts = new Array();
                zp_accounts[0] = [ "Select an account", "" ];
                jQuery("select#zp-ZotpressMetaBox-Collection-Accounts option").each(function(e) {
                    zp_accounts[e+1] = [ jQuery(this).text() + " (" + jQuery(this).attr("class") + ")", jQuery(this).val()];
                });
                
                
                // Zotpress Citation dialog
                CKEDITOR.dialog.add( 'zotpress_AddCitation_Dialog', function( api )
                {
                    return {
                        title : 'Zotpress Citation',
                        minWidth : 600,
                        minHeight : 400,
                        contents :
                        [
                           {
                                id : 'iframe',
                                label : 'Zotpress Citation',
                                expand : true,
                                elements :
                                [
                                   {
                                        type : 'iframe',
                                        src : zotpressPath + '/zotpress.widget.ckeditor.php?iframe=true',
                                        width : '100%',
                                        height : '100%',
                                        onContentLoad : function()
                                        {
                                            var iframe = document.getElementById( this._.frameId );
                                            iframeWindow = iframe.contentWindow;
                                        }
                                   }
                                ]
                           }
                        ],
                        onShow : function()
                        {
                            // Remove placeholder temporarily
                            if (zotpressCurrentPlaceholder.length > 0)
                            {
                                jQuery(".cke_editor_content iframe").contents().find("#"+zotpressCurrentPlaceholder).remove();
                                zotpressCurrentPlaceholder = "";
                            }
                            
                            function zotpressSetContentOnEdit()
                            {
                                if (jQuery("iframe.cke_dialog_ui_iframe").length > 0
                                        && jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-Zotpress-Output-Citation").length > 0)
                                {
                                    // For editing citation ... move from spans back to dialog ... check if spans exist!
                                    if (zotpressCurrentShortcode.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-Zotpress-Output-Shortcode").val(zotpressCurrentShortcode);
                                    
                                    if (zotpressCurrentCitation.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-Zotpress-Output-Citation").val(zotpressCurrentCitation);
                                    
                                    if (zotpressCurrentPages.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-ZotpressMetaBox-Pages-Input").val(zotpressCurrentPages);
                                    
                                    clearInterval(zotpressIframeCheck);
                                }
                            }
                            
                            var zotpressIframeCheck = setInterval(zotpressSetContentOnEdit, 500);
                        },
                        onOk : function(event)
                        {
                            // Grab from [hidden] inputs
                            
                            var zotpressPagesOutput = "";
                            zotpressTotalShortcodes++;
                            
                            var zotpressShortcodeOutput = '<span id="zp-Shortcode-' + zotpressTotalShortcodes + '" class="zp-Zotpress-Citation"><span class="zp-Zotpress-Citation-Shortcode"'
                            
                            if (jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-ZotpressMetaBox-Pages-Input").val().length > 0)
                                zotpressPagesOutput = jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-ZotpressMetaBox-Pages-Input").val();
                            
                            if (zotpressPagesOutput.length > 0)
                                zotpressShortcodeOutput += ' rel="' + zotpressPagesOutput + '"';
                            
                            zotpressShortcodeOutput += '>[zotpressInText item="' + jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-Zotpress-Output-Shortcode").val() + '"';
                            
                            if (zotpressPagesOutput.length > 0)
                                zotpressShortcodeOutput += ' pages="' + zotpressPagesOutput + '"';
                            
                            var zotpressCitationOutput = jQuery("iframe.cke_dialog_ui_iframe").contents().find("#zp-Zotpress-Output-Citation").val();
                            
                            if (zotpressPagesOutput.length > 0)   // There's page(s) to add/update
                            {
                                if (zotpressCitationOutput.indexOf("p. ") > 0)   // Already in citation
                                {
                                    zotpressCitationOutput = zotpressCitationOutput.substring(0, zotpressCitationOutput.indexOf("p. ")) + "p. " + zotpressPagesOutput  + ")";
                                }
                                else // Not in citation yet
                                {
                                    if (zotpressPagesOutput.indexOf("-") > 0)   // Multiple
                                        zotpressCitationOutput = zotpressCitationOutput.replace(")", ", pp. " + zotpressPagesOutput + ")");
                                    else   // Single
                                        zotpressCitationOutput = zotpressCitationOutput.replace(")", ", p. " + zotpressPagesOutput + ")");
                                }
                            }
                            
                            zotpressShortcodeOutput += ']</span><span class="zp-Zotpress-Citation-Info">' + zotpressCitationOutput + '</span></span>';
                            
                            // HAVE TO CHANGE so when editing, don't ADD but REPLACE
                            CKEDITOR.instances.content.insertHtml( zotpressShortcodeOutput );
                        }
                    };
                } ); // zotpress_AddCitation_Dialog
                
                
                
                // Add Bibliography dialog
               CKEDITOR.dialog.add( 'zotpress_AddBibliography_Dialog', function ()
               {
                    return {
                        title : 'Zotpress Bibliography',
                        minWidth : 600,
                        minHeight : 400,
                        contents :
                        [
                           {
                                id : 'iframe',
                                label : 'Zotpress Bibliography',
                                expand : true,
                                elements :
                                [
                                   {
                                        type : 'iframe',
                                        src : zotpressPath + '/zotpress.widget.ckeditor.php?iframe=true&bib=true',
                                        width : '100%',
                                        height : '100%',
                                        onContentLoad : function() {
                                            var iframe = document.getElementById( this._.frameId );
                                            iframeWindow = iframe.contentWindow;
                                        }
                                   }
                                ]
                           }
                        ],
                        onOk : function()
                        {
                            //this._.editor.insertHtml(iframeWindow.getElementById('zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib').value);
                            CKEDITOR.instances.content.insertHtml( '<span class="zp-Zotpress-Citation"><span class="zp-Zotpress-Citation-Shortcode">' + iframeWindow.getElementById('zp-ZotpressMetaBox-Output-Shortcode').value + '</span></span>' );
                        }
                    };
                } ); // zotpress_AddBibliography_Dialog
                
            }
        });
        
        
        // Add the Zotpress plugin to the CKEditor extra plugin list
        CKEDITOR.config.extraPlugins = 'zotpress';
        
    }


});