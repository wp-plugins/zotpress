<?php global $wpdb; ?>
    

<!-- START OF ZOTPRESS METABOX -------------------------------------------------------------------------->

<div id="zp-ZotpressMetaBox">
    
    <ul>
        <li><a href="#zp-ZotpressMetaBox-Bibliography">Bibliography</a></li>
        <li><a href="#zp-ZotpressMetaBox-InTextCreator">In-Text</a></li>
    </ul>
    
    
    
   
    <!-- START OF ZOTPRESS BIBLIOGRAPHY ------------------------------------------------------------------>
    <!-- NEXT: datatype [items, tags, collections], SEARCH items, tags, collections LIMIT -------------- -->
    
    <div id="zp-ZotpressMetaBox-Bibliography">
        
        <?php
        
        if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress;") > 1)
        {
            // See if default exists
            $zp_default_account = false;
            if (get_option("Zotpress_DefaultAccount")) $zp_default_account = get_option("Zotpress_DefaultAccount");
            
            if ($zp_default_account !== false)
            {
                $zp_account = $wpdb->get_results(
                    $wpdb->prepare(
                        "
                        SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress
                        WHERE api_user_id = %s
                        ",
                        $zp_default_account
                    )
                );
            }
            else
            {
                $zp_account = $wpdb->get_results(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress LIMIT 1;
					"
				);
            }
            
            if (is_null($zp_account[0]->nickname) === false && $zp_account[0]->nickname != "")
                $zp_default_account = $zp_account[0]->nickname . " (" . $zp_account[0]->api_user_id . ")";
        ?>
        <!-- START OF ACCOUNT -->
        <div id="zp-ZotpressMetaBox-Biblio-Account" rel="<?php echo $zp_account[0]->api_user_id; ?>">
            Searching <?php echo $zp_default_account; ?>. <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>">Change account?</a>
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
            
            <h4>Options <span class='toggle'></span></h4>
            
            <div id="zp-ZotpressMetaBox-Biblio-Options-Inner">
                
                <label for="zp-ZotpressMetaBox-Biblio-Options-Author">Filter by Author:</label>
                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Author" value="" />
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-Biblio-Options-Year">Filter by Year:</label>
                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Year" value="" />
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-Biblio-Options-Style">Style:</label>
                <select id="zp-ZotpressMetaBox-Biblio-Options-Style">
                    <?php
                    
                    if (!get_option("Zotpress_StyleList"))
                        add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");
                    
                    $zp_styles = explode(", ", get_option("Zotpress_StyleList"));
                    sort($zp_styles);
                    
                    // See if default exists
                    $zp_default_style = "apa";
                    if (get_option("Zotpress_DefaultStyle"))
                        $zp_default_style = get_option("Zotpress_DefaultStyle");
                    
                    foreach($zp_styles as $zp_style)
                        if ($zp_style == $zp_default_style)
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" rel='default' selected='selected'>".$zp_style."</option>\n";
                        else
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
                    
                    ?>
                </select>
                <p class="note">Add more styles <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>">here</a>. Note: Requires re-import.</p>
                
                <hr />
                
                <!--Sort by:-->
                <label for="zp-ZotpressMetaBox-Biblio-Options-SortBy">Sort by:</label>
                <select id="zp-ZotpressMetaBox-Biblio-Options-SortBy">
                    <option id="zp-bib-default" value="default" rel="default" selected="selected">Default</option>
                    <option id="zp-bib-author" value="author">Author</option>
                    <option id="zp-bib-date" value="date">Date</option>
                    <option id="zp-bib-title" value="title">Title</option>
                </select>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Sort order:
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC">Ascending</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Sort-DESC">Descending</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-No" name="sort" value="DESC" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Show images?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Image-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-Yes" name="images" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Image-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-No" name="images" value="no" checked="checked" />
                        </div>
                </div>
                    
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Show title by year?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Title-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-Yes" name="title" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Title-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-No" name="title" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Downloadable?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Download-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-Yes" name="download" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Download-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-No" name="download" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Abstract?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes" name="abstract" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-No" name="abstract" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Notes?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes" name="notes" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Notes-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-No" name="notes" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Citable (in RIS format)?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes" name="cite" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-Biblio-Options-Cite-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-No" name="cite" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-Biblio-Options-Limit">Limit by:</label>
                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Limit" value="" />
                
            </div>
        </div>
        <!-- END OF OPTIONS -->
        
        <!-- START OF SHORTCODE -->
        <div id="zp-ZotpressMetaBox-Biblio-Shortcode">
            
            <a id="zp-ZotpressMetaBox-Biblio-Generate-Button" class="button-primary" href="javascript:void(0);">Generate Shortcode</a>
            <a id="zp-ZotpressMetaBox-Biblio-Clear-Button" class="button" href="javascript:void(0);">Clear</a>
            
            <hr class="clear" />
            
            <div id="zp-ZotpressMetaBox-Biblio-Shortcode-Inner">
                <label for="zp-ZotpressMetaBox-Biblio-Shortcode-Text">Shortcode:</span></label>
                <textarea id="zp-ZotpressMetaBox-Biblio-Shortcode-Text">[zotpress]</textarea>
            </div>
        </div>
        <!-- END OF SHORTCODE -->
        
    </div><!-- #zp-ZotpressMetaBox-Bibliography -->
    
    <!-- END OF ZOTPRESS BIBLIOGRAPHY --------------------------------------------------------------------->
    
    
    
    <!-- START OF ZOTPRESS IN-TEXT ------------------------------------------------------------------------->
    
    <div id="zp-ZotpressMetaBox-InTextCreator">
        
        <?php if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress;") > 1) { ?>
        <!-- START OF ACCOUNT -->
        <div id="zp-ZotpressMetaBox-Account">
            <?php
            
            // See if default exists
            $zp_default_account = false;
            if (get_option("Zotpress_DefaultAccount"))
                $zp_default_account = get_option("Zotpress_DefaultAccount");
            
            if ($zp_default_account !== false)
            {
                $zp_account = $wpdb->get_results(
                    $wpdb->prepare(
                        "
                        SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress
                        WHERE api_user_id = %s",
                        $zp_default_account
                    )
                );
            }
            else
            {
                $zp_account = $wpdb->get_results(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress LIMIT 1;
					"
				);
            }
            
            if (is_null($zp_account[0]->nickname) === false && $zp_account[0]->nickname != "")
                $zp_default_account = $zp_account[0]->nickname . " (" . $zp_account[0]->api_user_id . ")";
            
            ?>
            Searching <?php echo $zp_default_account; ?>. <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>">Change account?</a>
        </div>
        <!-- END OF ACCOUNT -->
        <?php } ?>
        
        <!-- START OF SEARCH -->
        <div id="zp-ZotpressMetaBox-Citations">
            <input id="zp-ZotpressMetaBox-Citations-Search" class="help" type="text" value="Type to search" />
            <input type="hidden" id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>" />
            
        </div><div id="zp-ZotpressMetaBox-Citations-List"><div id="zp-ZotpressMetaBox-Citations-List-Inner"></div><hr class="clear" /></div>
        <!-- END OF SEARCH -->
        
        <!-- START OF OPTIONS -->
        <div id="zp-ZotpressMetaBox-InTextCreator-Options">
            
            <h4>Options <span class='toggle'></span></h4>
            
            <div id="zp-ZotpressMetaBox-InTextCreator-Options-Inner">
                
                <h5 class="first">In-Text Options</h3>
                
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-Format">Format:</label>
                <input type="text" id="zp-ZotpressMetaBox-InTextCreator-Options-Format" value="(%a%, %d%, %p%)" />
                <p class="note">Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</p>
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-Etal">Et al:</label>
                <select id="zp-ZotpressMetaBox-InTextCreator-Options-Etal">
                    <option id="default" value="default" selected="selected">Default</option>
                    <option id="yes" value="yes">Yes</option>
                    <option id="no" value="no">No</option>
                </select>
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-Separator">Separator:</label>
                <select id="zp-ZotpressMetaBox-InTextCreator-Options-Separator">
                    <option id="semicolon" value="default" selected="selected">Semicolon</option>
                    <option id="default" value="comma">Comma</option>
                </select>
                
                <hr />
                
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-And">And:</label>
                <select id="zp-ZotpressMetaBox-InTextCreator-Options-And">
                    <option id="default" value="default" selected="selected">No</option>
                    <option id="and" value="and">and</option>
                    <option id="comma-and" value="comma-and">, and</option>
                </select>
                
                <h5>Bibliography Options</h3>
                
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-Style">Style:</label>
                <select id="zp-ZotpressMetaBox-InTextCreator-Options-Style">
                    <?php
                    
                    if (!get_option("Zotpress_StyleList"))
                        add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver");
                    
                    $zp_styles = explode(", ", get_option("Zotpress_StyleList"));
                    sort($zp_styles);
                    
                    // See if default exists
                    $zp_default_style = "apa";
                    if (get_option("Zotpress_DefaultStyle")) $zp_default_style = get_option("Zotpress_DefaultStyle");
                    
                    foreach($zp_styles as $zp_style)
                        if ($zp_style == $zp_default_style)
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" rel='default' selected='selected'>".$zp_style."</option>\n";
                        else
                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";
                    
                    ?>
                </select>
                <p class="note">Add more styles <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>">here</a>. Note: Requires re-import.</p>
                
                <hr />
                
                <!--Sort by:-->
                <label for="zp-ZotpressMetaBox-InTextCreator-Options-SortBy">Sort by:</label>
                <select id="zp-ZotpressMetaBox-InTextCreator-Options-SortBy">
                    <option id="default" value="default" rel="default" selected="selected">Default</option>
                    <option id="author" value="author">Author</option>
                    <option id="date" value="date">Date</option>
                    <option id="title" value="title">Title</option>
                </select>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Sort order:
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC">Ascending</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Sort-DESC">Descending</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Sort-No" name="sort" value="DESC" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Show images?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Image-Yes" name="images" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Image-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Image-No" name="images" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Show title by year?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Title-Yes" name="title" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Title-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Title-No" name="title" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Downloadable?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Download-Yes" name="download" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Download-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Download-No" name="download" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Abstract?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-Yes" name="abstract" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Abstract-No" name="abstract" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Notes?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Notes-Yes" name="notes" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Notes-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Notes-No" name="notes" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="zp-ZotpressMetaBox-Field">
                    Citable (in RIS format)?
                    <div class="zp-ZotpressMetaBox-Field-Radio">
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes">Yes</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Cite-Yes" name="cite" value="yes" />
                        
                        <label for="zp-ZotpressMetaBox-InTextCreator-Options-Cite-No">No</label>
                        <input type="radio" id="zp-ZotpressMetaBox-InTextCreator-Options-Cite-No" name="cite" value="no" checked="checked" />
                    </div>
                </div>
                
            </div>
        </div>
        <!-- END OF OPTIONS -->
        
        <!-- START OF SHORTCODE -->
        <div id="zp-ZotpressMetaBox-InTextCreator-Shortcode">
            
            <a id="zp-ZotpressMetaBox-InTextCreator-Generate-Button" class="button-primary" href="javascript:void(0);">Generate Shortcode</a>
            <a id="zp-ZotpressMetaBox-InTextCreator-Clear-Button" class="button" href="javascript:void(0);">Clear</a>
            
            <hr class="clear" />
            
            <div id="zp-ZotpressMetaBox-InTextCreator-Shortcode-Inner">
                <label for="zp-ZotpressMetaBox-InTextCreator-InText">Shortcode:</span></label>
                <textarea id="zp-ZotpressMetaBox-InTextCreator-InText">[zotpressInText]</textarea>
                
                <div id="zp-ZotpressMetaBox-InTextCreator-Text-Bib-Container" class="inTextOnly">
                    <label for="zp-ZotpressMetaBox-InTextCreator-Text-Bib">Bibliography: <span>(Paste somewhere in the post)</span></label>
                    <input id="zp-ZotpressMetaBox-InTextCreator-Text-Bib" type="text" value="[zotpressInTextBib]" />
                </div>
            </div>
        </div>
        <!-- END OF SHORTCODE -->
        
    </div><!-- #zp-ZotpressMetaBox-InTextCreator -->
    
    <!-- END OF ZOTPRESS IN-TEXT ---------------------------------------------------------------------------->
    

    
</div><!-- #zp-ZotpressMetaBox -->
    
<!-- END OF ZOTPRESS METABOX ------------------------------------------------------------------------------->


