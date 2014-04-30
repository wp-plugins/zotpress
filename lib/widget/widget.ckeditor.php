<?php

    if (isset($_GET['iframe']) && trim($_GET['iframe']) == "true")
    {
        // Include WordPress
        require('../../../wp-load.php');
        
        global $wpdb;
        
        require_once( '../../../wp-admin/admin.php' );
        
        $title = __( 'Zotpress' );
        
        list( $display_version ) = explode( '-', $wp_version );
        
        include( '../../../wp-admin/admin-header.php' );
        
        if (!defined('WP_USE_THEMES'))
            define('WP_USE_THEMES', false);
            
    } // iframe
?>



<input type="hidden" id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />



<!-- START OF ZOTPRESS CKEDITOR DIALOG ------------------------------------------------------------------------------------------------------------------------------------>

<div id="zp-Zotpress-CkEditor"<?php if (!isset($_GET['bib'])) { echo ' class="citation"'; } ?>>

<?php if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>
    <div id="zp-Zotpress-CkEditor-Tabs" class="zp-ZotpressMetaBox-Tabs">
        
        <ul>
            <li><a href="#zp-Zotpress-CkEditor-0">Add/Edit Citations</a></li>
            <li><a href="#zp-Zotpress-CkEditor-1">Options</a></li>
        </ul>
    
<?php } ?>
    
        
        <!-- START OF ADD/EDIT CITATION ----------------------------------------------------------------------------------------------------------------------------------------- -->
        <div id="zp-Zotpress-CkEditor-0" class="zp-Tab">
            
            <!-- START OF ACCOUNT SELECTION -->
            <div id="zp-ZotpressMetaBox-Tabs" class="zp-ZotpressMetaBox-Tabs">
                
                <ul>
                    <li><a href="#zp-ZotpressMetaBox-Tabs-2">By Collection</a></li>
                    <li><a href="#zp-ZotpressMetaBox-Tabs-3">By Tag</a></li>
                </ul>
                
                <!-- START OF By Collection -->
                <div id="zp-ZotpressMetaBox-Tabs-2" class="zp-Tab">
                    
                    <!-- NEED TO AUTO-COMPLETE DEFAULT WITH    get_option("Zotpress_DefaultAccount")     -->
                    
                    <div id="zp-Zotpress-Collection-Account-Select">
                        <label for="zp-ZotpressMetaBox-Collection-Accounts">Account:</label>
                        <select id="zp-ZotpressMetaBox-Collection-Accounts">
                            <option id='default' class='default' value=''>Choose Account:</option>
                            <?php
                            
                                global $wpdb;
                                $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
                                
                                foreach ($accounts as $account)
                                    if (isset( $account->nickname ))
                                        echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                                    else
                                        echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
                            
                            ?>
                        </select>
                    </div>
                    
                </div>
                <!-- END OF By Collection -->
                
                <!-- START OF By Tags -->
                <div id="zp-ZotpressMetaBox-Tabs-3" class="zp-Tab">
                    
                    <label for="zp-ZotpressMetaBox-Tags-Accounts">Account:</label>
                    <select id="zp-ZotpressMetaBox-Tags-Accounts" multiple="yes">
                        <option id='default' class='default' value=''>Choose Account:</option>
                        <?php
                            foreach ($accounts as $account)
                                if (isset( $account->nickname ))
                                    echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                                else
                                    echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
                        ?>
                    </select>
                    
                </div>
                <!-- END OF By Tag -->
                
            </div>
            
        </div> <!-- #zp-Zotpress-CkEditor-0 -->
    
<?php if (!isset($_GET['bib'])) { ?>

        <!-- START OF PAGES -->
        <div id="zp-ZotpressMetaBox-Pages">
            <label for="zp-ZotpressMetaBox-Pages-Input">Page/s:</label>
            <input id="zp-ZotpressMetaBox-Pages-Input" type="text" size="10" />
            <input id="zp-ZotpressMetaBox-Pages-Button" class="button-secondary" type="button" value="Add" />
            <p class="zp-Note">Optional. Single number or a range, e.g. 3-10.</p>
        </div>
        <!-- START OF PAGES -->
    
    
    
<?php } if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>

        <!-- START OF OPTIONS ----------------------------------------------------------------------------------------------------------------------------------------------------- -->
        <div id="zp-Zotpress-CkEditor-1" class="zp-Tab">
            
            <!-- START OF TYPE: BIB/INTEXT -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-0" class="zp-Tab">
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-0-Type">Choose Type:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-0-Type">
                    <option id="intext" value="In-Text" selected="selected">In-Text</option>
                    <option id="bib" value="Bibliography">Bibliography</option>
                </select>
            </div>
            <!-- END OF TYPE: BIB/INTEXT -->
            
            <!-- START OF USERID/NICK -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-1" class="zp-Tab">
                <p class="note">*Only required if you have more than one account.</p>
                <div class="zp-ZotpressMetaBox-RadioButtons">
                    <label for="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-UserID">User ID:</label>
                    <input id="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-UserID" class="zp-ZotpressMetaBox-ShortcodeCreator-1-Type" type="radio" value="UserID" />
                    <label for="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-Nick">Nickname:</label>
                    <input id="zp-ZotpressMetaBox-ShortcodeCreator-1-Type-Nick" class="zp-ZotpressMetaBox-ShortcodeCreator-1-Type" type="radio" value="Nickname" />
                </div>
                <?php
                
                $zp_accounts = $wpdb->get_results("SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
                $zp_accounts_total = $wpdb->num_rows;
                
                if ($zp_accounts_total > 0)
                {
                    $zp_userids = "";
                    $zp_nicks = "";
                    foreach ($zp_accounts as $zp_account)
                    {
                        $zp_userids .= "<option id=\"".$zp_account->api_user_id."\" value=\"".$zp_account->api_user_id."\">".$zp_account->api_user_id."</option>\n";
                        $zp_nicks .= "<option id=\"".$zp_account->nickname."\" value=\"".$zp_account->nickname."\">".$zp_account->nickname."</option>\n";
                    }
                }
                
                ?>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-1-TypeText-UserID" class="zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText UserID">
                    <?php echo $zp_userids; ?>
                </select>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-1-TypeText-Nickname" class="zp-ZotpressMetaBox-ShortcodeCreator-1-UserIDText Nickname">
                    <?php echo $zp_nicks; ?>
                </select>
            </div>
            <!-- END OF USERID/NICK -->
            
            <!-- START OF AUTHOR/YEAR -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-2" class="zp-Tab">
                <p class="note">Optional. Be sure to replace spaces with a +.</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-2-Author">Author:</label>
                <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Author" type="text" size="20" value="" />
                <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Author-Button" class="button-secondary" type="button" value="Add" />
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-2-Year">Year:</label>
                <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Year" type="text" size="20" value="" />
                <input id="zp-ZotpressMetaBox-ShortcodeCreator-2-Year-Button" class="button-secondary" type="button" value="Add" />
            </div>
            <!-- END OF AUTHOR/YEAR -->
            
            <!-- START OF DATATYPE -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-3" class="zp-Tab">
                <p class="note">Optional. Default is "items."</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-3-Datatype">Choose Data Type:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-3-Datatype">
                    <option id="Items" value="Items" selected="selected">Items</option>
                    <option id="Tags" value="Tags">Tags</option>
                    <option id="Collections" value="Collections">Collections</option>
                </select>
            </div>
            <!-- END OF DATATYPE -->
            
            <!-- START OF DISPLAY -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-4" class="zp-Tab">
                <p class="note">Optional. Default is "bib."</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Content">Choose Content:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Content">
                    <option id="bib" value="bib" selected="selected">bib</option>
                    <option id="html" value="html">html</option>
                </select>
                <p class="note">Optional. Displays title by year.</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Title">Show Title?</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Title">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
                <p class="note">Optional. Displays image if exists.</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-4-Image">Show Image?</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-4-Image">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
            </div>
            <!-- END OF DISPLAY -->
            
            <!-- START OF STYLE -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-5" class="zp-Tab">
                <?php
                
                // Default style, per post or overall
                $zp_default_style = "apa";
                if (get_option("Zotpress_DefaultStyle_". get_the_ID()))
                    $zp_default_style = get_option("Zotpress_DefaultStyle_". get_the_ID());
                else
                    if (get_option("Zotpress_DefaultStyle"))
                        $zp_default_style = get_option("Zotpress_DefaultStyle");
                        
                ?>
                <p class="note">Optional. Default is "<?php echo $zp_default_style; ?>."</p>
                
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">Choose Style:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-5-Style">
                    <?php
                    
                    $zp_styles = "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver";
                    $zp_styles = explode(", ", $zp_styles);
                    
                    foreach($zp_styles as $zp_style)
                        if ($zp_style == $zp_default_style)
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" selected='selected'>".$zp_style."</option>\n";
                        else
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
                    
                    ?>
                </select>
                
                <script type="text/javascript" >
                jQuery(document).ready(function() {
                
                    jQuery("#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button").click(function()
                    {
                        // Plunk it together
                        var data = 'submit=true&style=' + jQuery('#zp-ZotpressMetaBox-ShortcodeCreator-5-Style').val() + '&forpost=true&post=<?php the_ID(); ?>';
                        
                        // Prep for validation
                        jQuery('input#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button').attr('disabled','true');
                        jQuery('.zp-Loading').show();
                        
                        // Set up uri
                        var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>/zotpress.widget.metabox.actions.php?'+data;
                        
                        // AJAX
                        jQuery.get(xmlUri, {}, function(xml)
                        {
                            var $result = jQuery('result', xml).attr('success');
                            
                            jQuery('.zp-Loading').hide();
                            jQuery('input#zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button').removeAttr('disabled');
                            
                            if ($result == "true")
                            {
                                jQuery('div.zp-Errors').hide();
                                jQuery('div.zp-Success').show();
                                
                                jQuery.doTimeout(1000,function() {
                                    jQuery('div.zp-Success').hide();
                                });
                            }
                            else // Show errors
                            {
                                jQuery('div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                jQuery('div.zp-Errors').show();
                            }
                        });
                        
                        // Cancel default behaviours
                        return false;
                        
                    });
                    
                });
                </script>
                
                <!--<form id="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Form" action="<?php //echo $PHP_SELF;?>" method="post">-->
                    <label for="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button">Set Style as Post Default:</label>
                    <input type="button" id="zp-ZotpressMetaBox-ShortcodeCreator-5-Default-Button" class="button-secondary" value="Set Default Style" />
                    <div class="zp-Loading">loading</div>
                    <div class="zp-Success">Success!</div>
                    <div class="zp-Errors">Errors!</div>
                <!--</form>-->
                
            </div>
            <!-- END OF STYLE -->
            
            <!-- START OF SORT -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-6" class="zp-Tab">
                <p class="note">Optional. Default is "latest."</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-6-SortBy">Sort By:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-6-SortBy">
                    <option id="latest" value="latest" selected="selected">latest</option>
                    <option id="author" value="author">author</option>
                    <option id="date" value="date">date</option>
                </select>
                <p class="note">Optional. Default is "desc."</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-6-Sort">Sort By:</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-6-Sort">
                    <option id="desc" value="desc" selected="selected">desc</option>
                    <option id="asc" value="asc">asc</option>
                </select>
            </div>
            <!-- END OF SORT -->
            
            <!-- START OF EXTRA -->
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-7" class="zp-Tab">
                <p class="note">Optional. Displays download link.</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-7-Download">Show Title?</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-7-Download">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
                <p class="note">Optional. Displays note/s if they exist.</p>
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-7-Notes">Show Image?</label>
                <select id="zp-ZotpressMetaBox-ShortcodeCreator-7-Notes">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
            </div>
            <!-- END OF DISPLAY -->
            
            <div id="zp-ZotpressMetaBox-ShortcodeCreator-Output">
                <label for="zp-ZotpressMetaBox-ShortcodeCreator-Text"><span class="inTextOnly">In-Text</span><span class="bibOnly">Bibliography</span> Shortcode:</span></label>
                <textarea id="zp-ZotpressMetaBox-ShortcodeCreator-Text">[zotpressInText]</textarea>
                <div id="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib-Container" class="inTextOnly">
                    <label for="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib">In-Text Bibliography Shortcode:</span></label>
                    <p class="note">Copy-n-paste at the end of your post.</p>
                    <input id="zp-ZotpressMetaBox-ShortcodeCreator-Text-InTextBib" type="text" value="[zotpressInTextBib]" />
                </div>
            </div>
            
        </div>
        
    </div>
    
<?php } // bib ?>



</div><!-- #zp-Zotpress-CkEditor -->

<!-- END OF ZOTPRESS CKEDITOR DIALOG -------------------------------------------------------------------------------------------------------------------------->



<?php if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>

<div id="zp-Zotpress-CKEditor-Output">
    <label for="zp-Zotpress-Output-Shortcode">Your shortcode:</label>
    <input id="zp-Zotpress-Output-Shortcode" type="text" size="28" />
</div>

<?php } else { ?>

<div id="zp-Zotpress-CKEditor-Output">
    <label for="zp-Zotpress-Output-Citation">Preview:</label>
    <input id="zp-Zotpress-Output-Citation" type="text" size="28" />
    <input id="zp-Zotpress-Output-Shortcode" type="text" size="28" />
</div>

<?php } ?>



<?php if (isset($_GET['iframe']) && trim($_GET['iframe']) == "true") { include( '../../../wp-admin/admin-footer.php' ); }  ?>