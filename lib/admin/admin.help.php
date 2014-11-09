
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('admin.display.tabs.php'); ?>
            
            <h3>What is Zotpress?</h3>
            
            <div class="zp-Message">
                <h3>About Zotpress</h3>
                <p class="version">
                    <strong>Version:</strong> You're using Zotpress <?php echo ZOTPRESS_VERSION; ?><br />
                    <strong>Website:</strong> <a title="Zotpress on WordPress" rel="external" href="http://wordpress.org/plugins/zotpress/">Zotpress on WordPress.org</a><br />
                    <strong>Support:</strong> <a title="Donations always appreciated! Accepted through PayPal" rel="external" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5HQ8FXAXS9MUQ">Donate through PayPal</a>
                </p>
                <p class="rate">
                    If you like Zotpress, let the world know with a
                    <a class="zp-FiveStar" title="Rate Zotpress" rel="external" href="http://wordpress.org/plugins/zotpress/">rating</a>
                    on WordPress.org!
                </p>
            </div>
            
            <p>
                <a title="More of my plugins" href="http://katieseaborn.com/plugins/">Zotpress</a> bridges <a title="Zotero" href="https://www.zotero.org/settings/keys">Zotero</a>
                and WordPress by allowing you to display items from your Zotero library through shortcodes and widgets.
                It also extends the basic meta functionality offered by Zotero by allowing you to add images to and provide downloadable files associated with your citations.
            </p>
            
            <p>There's a few ways to use Zotpress:</p>
            
            <ol class="zp-WaysToUseZotpress">
                <li>
                    <p><strong><span class="number">1</span> The Zotpress Shortcode</strong></p>
                    <p class="indent">Generate a bibliography wherever you can call shortcodes. <a title="Learn more" href="#zotpress">Learn more &raquo;</a></p>
                </li>
                <li>
                    <p><strong><span class="number">2</span>The Zotpress In-Text Shortcodes</strong></p>
                    <p class="indent">Create in-text citations and an auto-generated bibliography. <a title="Learn more" href="#intext">Learn more &raquo;</a></p>
                </li>
                <li>
                    <p><strong><span class="number">3</span>The Zotpress Sidebar Widget</strong></p>
                    <p class="indent">Use this widget in a sidebar to generate a bibliography. <a title="Learn more" href="#widget">Learn more &raquo;</a></p>
                </li>
            </ol>
            
            <p>
                You can build shortcodes and search for item keys using the <a title="Zotpress Reference widget" href="#ref">Zotpresss Reference widget</a>
                on the post and page write/edit screens.
            </p>
            
            <p>
                Have questions? First, check out the <a title="F.A.Q." href="#faq">F.A.Q.</a> Then search the
                <a title="Zotpress Forums" href="http://wordpress.org/support/plugin/zotpress">Zotpress Support Forums</a>. If you can't find an answer,
                feel free to post your question there.
            </p>
            
            
            
            <hr />
            
            <a name="zotpress"></a>
            <h3>Displaying Citations Using the Zotpress Shortcode</h3>
            
            <p>
                To display a complete list of citations for an account in the default bibliography style (APA), simply use this shortcode:
            </p>
            
            <code>
                [zotpress userid="00000"]
            </code>
            
            <p>
                An example of how to use shortcode attributes is:
            </p>
            
            <code>
                [zotpress collection="ZKDTKM3X" limit="5"]
            </code>
            
            <p>
                This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X". (<strong>Hint:</strong> Collection keys are listed beside each item on the <a title="Browse" href="admin.php?page=Zotpress">Browse page</a>.)
            </p>
            
            <h4 class="short">Shortcode Attributes</h4>
            
            <table class="zp-Documentation" border="0" cellpadding="0" cellspacing="0">
                
                <tr class="main">
                    <th>Category</th>
                    <th>Type</th>
                    <th>Attribute</th>
                    <th>Description</th>
                    <th>Example</th>
                </tr>
                
                <tr>
                    <td rowspan="13" class="category">Filtering</td>
                    
                    <td rowspan="2" class="type">Account</td>
                    
                    <td class="code">userid</td>
                    <td><p>Display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</p></td>
                    <td class="example"><p><code>[zotpress userid="000000"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">nickname</td>
                    <td><p>Display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</p></td>
                    <td class="example"><p><code>[zotpress nickname="Katie"]</code></p></td>
                </tr>
                
                <tr class="zebra">
                    <td rowspan="2" class="type">Data</td>
                    
                    <td class="code">authors</td>
                    <td><p>Alternative: <code>author</code>. Display a list of citations from a particular author or authors. <strong>Note:</strong> "Carl Sagan","C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Sagan".</p></td>
                    <td class="example"><p><code>[zotpress author="Carl Sagan"]</code></p><p><code>[zotpress authors="Carl Sagan,Stephen Hawking"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td class="code">years</td>
                    <td><p>Alternative: <code>year</code>. Display a list of citations from a particular year or years. <strong>Note:</strong> You <em>can</em> display by Author and Year together.</p></td>
                    <td class="example"><p><code>[zotpress year="1990"]</code></p><p><code>[zotpress years="1990,1998,2013"]</code></p></td>
                </tr>
                
                <tr>
                    <td rowspan="4" class="type">Type</td>
                    
                    <td class="code">datatype</td>
                    <td><p>Display a list of a particular data type. <strong>Options: items [default], tags, collections.</strong></p></td>
                    <td class="example"><p><code>[zotpress datatype="tags"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">items</td>
                    <td><p>Alternative: <code>item</code>. Display an item or list of items using particular item keys.</p></td>
                    <td class="example"><p><code>[zotpress item="GMGCJU34"]</code></p><p><code>[zotpress items="GMGCJU34,U9Z5JTKC"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">collections</td>
                    <td><p>Alternative: <code>collection</code>. Display items from a collection or list of collections using particular collection keys.</p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34"]</code></p><p><code>[zotpress collections="GMGCJU34,U9Z5JTKC"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">tags</td>
                    <td><p>Alternative: <code>tag</code>. Display items associated with one or more tags. <strong>Warning:</strong> Will break if the tag has a comma.</p></td>
                    <td class="example"><p><code>[zotpress tag="zotero"]</code></p><p><code>[zotpress tags="zotero,scholarly blogging"]</code></p></td>
                </tr>
                
                <tr class="zebra">
                    <td rowspan="5" class="type">Settings</td>
                    
                    <td class="code">inclusive</td>
                    <td><p>When filtering, include all items that match ANY criteria or exclude all items except those that match the criteria exactly. Works with collection/s, tag/s, author, year. <strong>Options: yes [default], no.</strong></p></td>
                    <td class="example"><p><code>[zotpress author="Carl Sagan" inclusive="yes"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td class="code">sortby</td>
                    <td><p>Sort multiple citations using meta data as attributes. <strong>Options: title, author, date, default (latest added) [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress author="Carl Sagan" sortby="date"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td class="code">order</td>
                    <td><p>Alternative: <code>sort</code>. Order of the sortby attribute. <strong>Options: asc [default], desc.</strong></p></td>
                    <td class="example"><p><code>[zotpress author="Carl Sagan" sortby="date" order="desc"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td class="code">title</td>
                    <td><p>Dispay a title by year. <strong>Options: yes, no [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress author="Carl Sagan" sortby="date" title="yes"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td class="code">limit</td>
                    <td><p>Limit the item list to by a given number. <strong>Optional.</strong> Options: Any number between 1 and infinity.</p></td>
                    <td class="example"><p><code>[zotpress limit="5"]</code></p></td>
                </tr>
                
                <tr>
                    <td rowspan="10" class="category last">Display</td>
                    
                    <td rowspan="10" class="type last">Settings</td>
                    
                    <td class="code">style</td>
                    <td><p>Citation style. <strong>Options: apsa, apa [default], asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver.</strong> Note: Support for more styles is coming; see <a title="Zotero Style Repository" href="http://www.zotero.org/styles">Zotero Style Repository</a> for details.</p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" style="apa"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">showimage</td>
                    <td><p>Whether or not to display the citation's image, if one exists. <strong>Options: yes, no [default]</strong></p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" showimage="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">showtags</td>
                    <td><p>Whether or not to display the citation's tags, if one or more exists. <strong>Options: yes, no [default]</strong></p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" showtags="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">download</td>
                    <td><p>Alternative: <code>downloadable</code> Whether or not to display the citation's download URL, if one exists. <strong>Enable this option only if you are legally able to provide your files for download.</strong> Options: yes, no [default].</p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" download="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">abstract</td>
                    <td><p>Alternative: <code>abstracts</code> Whether or not to display the citation's abstract, if one exists. Options: yes, no [default].</p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" abstracts="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">notes</td>
                    <td><p>Alternative: <code>note</code> Whether or not to display the citation's notes, if one exists. <strong>Must have notes made public via the private key settings on Zotero.</strong> Options: yes, no [default].</p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" notes="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">cite</td>
                    <td><p>Alternative: <code>citeable</code> Make the displayed citations citable by generating RIS links. <strong>Options: yes, no [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" cite="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">linkedlist</td>
                    <td><p><strong>Requires:</strong> <code>datatype="collections"</code> or <code>datatype="tags"</code> Alternative: <code>link</code> List all tags or all collections and link each to a page that displays their associated citation items. <strong>Options: yes, no [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress datatype="tags" linkedlist="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">forcenumber</td>
                    <td><p>Numbers bibliography items, even when the citation style, e.g. APA, doesn't normally.<strong>Options: true, false [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" forcenumber="true"]</code></p></td>
                </tr>
                <tr class="last">
                    <td class="code last">target</td>
                    <td><p>Links open up in a new window or tab. Applies to citation links, e.g. "retrieved from." Compliant with HTML5 but not XHTML Strict. <strong>Options: new, no [default].</strong></p></td>
                    <td class="example"><p><code>[zotpress collection="GMGCJU34" target="new"]</code></p></td>
                </tr>
                
            </table>
            
            
            
            
            <hr />
            
            <a name="intext"></a>
            <h3>Displaying Citations Using the Zotpress In-Text Shortcodes</h3>
            
            <div id="zp-Intext-Example">
                <span class="title">Zotpress In-Text Example</span>
                
                <p>This is an example of a Zotpress in-text citation as it would appear in your rich text editor [zotpressInText item="{NCXAA92F,36}"]. Let's wrap up this short note with a bibliography.</p>
                
                <span class="title">Bibliography:</span>
                <p>[zotpressInTextBib]</p>
            </div>
            
            <p>
                Use one or more <code>[zotpressInText]</code> shortcodes in your blog entry to create in-text citations.
            </p>
            <p>
                Here's what an in-text citation might look like in your rich text editor:
            </p>
            
            <p class="example">
                Katie said, "Zotpress is cooler than your shoes" <code>[zotpressInText item="{NCXAA92F,36}"]</code>.
            </p>
            
            <p>And this is what it might look like on your blog:</p>
            
            <p class="example">
                Katie said, "Zotpress is cooler than your shoes" (Seaborn, 2012, p. 36).
            </p>
            
            <p>
                To display the auto-generated bibliography, place the <code>[zotpressInTextBib]</code> shortcode somewhere in your entry after the in-text citation shortcodes.
            </p>
            
            <p>
                <strong>Note:</strong> In-text citations, unlike the bibliography, are not automatically styled. Use the "format" attribute to manually style in-text citations. Support for automatically styled in-text citations is in the works.
            </p>
            
            <h4 style="clear:both;">In-Text Shortcode Attributes</h4>
            
            <table class="zp-Documentation" border="0" cellpadding="0" cellspacing="0">
                
                <tr class="main">
                    <th>Category</th>
                    <th>Type</th>
                    <th>Attribute</th>
                    <th>Description</th>
                    <th>Example</th>
                </tr>
                
                <tr>
                    <td rowspan="3" class="category">Filtering</td>
                    
                    <td rowspan="2" class="type">Account</td>
                    
                    <td class="code">userid</td>
                    <td><p>Display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</p></td>
                    <td class="example"><p><code>[zotpressInText userid="000000"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">nickname</td>
                    <td><p>Display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</p></td>
                    <td class="example"><p><code>[zotpressInText nickname="Katie"]</code></p></td>
                </tr>
                <tr class="zebra">
                    <td rowspan="1" class="type">Data</td>
                    
                    <td class="code">items</td>
                    <td><p>Alternative: <code>item</code> Item keys and page number pairs formatted like so: <code>ITEMKEY</code> or <code>{ITEMKEY,PAGES}</code> or <code>{ITEMKEY1,PAGES},{ITEMKEY2,PAGES},...</code>.</p></td>
                    <td class="example"><p><code>[zotpressInText item="NCXAA92F"]</code></p><p><code>[zotpressInText item="{NCXAA92F,10-15}"]</code></p><p><code>[zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]</code></p></td>
                </tr>
                
                <tr class="last">
                    <td rowspan="4" class="category last">Display</td>
                    
                    <td rowspan="4" class="type last">Settings</td>
                    
                    <td class="code">format</td>
                    <td>
                        <p>How the in-text citation should be presented. Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</p>
                        <p class="break"><strong>Hint:</strong> In WordPress shortcodes, the bracket characters <strong>[</strong> and <strong>]</strong> are special characters. To use in-text brackets, see the example on the right.</p>
                    </td>
                    <td class="example">
                        <p><code>[zotpressInText item="NCXAA92F" format="%a% (%d%, %p%)"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">author (date, pages)</span></p>
                        <p class="break"><code>[zotpressInText item="NCXAA92F" format="&amp;#91;%num%&amp;#93;"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">[1]</span></p>
                    </td>
                </tr>
                <tr>
                    <td class="code">etal</td>
                    <td><p>How "et al." is applied to multiple instances of a citation if it has three or more authors. Default is full author list for first instance and "et al." for every other instance. <strong>Options:</strong> yes, no, default [default]</p></td>
                    <td class="example"><p><code>[zotpressInText item="NCXAA92F" etal="yes"]</code></p></td>
                </tr>
                <tr>
                    <td class="code">separator</td>
                    <td><p>How a list of two or more citations is delineated. Default is with a comma. <strong>Options:</strong> comma, semicolon [default]</p></td>
                    <td class="example"><p><code>[zotpressInText item="NCXAA92F" separator="semicolon"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">(Sagan 2013; Hawkings 2014)</span></p></td>
                </tr>
                <tr>
                    <td class="code last">and</td>
                    <td><p>Whether some form of "and" is applied to citations with two or more authors. Default is "and". <strong>Options:</strong> and, comma-and, comma [default]</p></td>
                    <td class="example"><p><code>[zotpressInText item="NCXAA92F" and="comma-and"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">(Sagan, and Hawkings 2014)</span></p></td>
                </tr>
                
            </table>
            
            <p>The <code>[zotpressInTextBib]</code> shortcode takes the same attributes as the <code>[zotpress]</code> shortcode, minus the "userid," "nickname," and "limit" attributes.</p>
            
            
            <hr />
            
            <a name="widget"></a>
            <h3>Displaying Citations Using the Zotpress Sidebar Widget</h3>
            
            <p>You can drag-n-drop a Zotpress sidebar widget on your <a title="Widgets" href="widgets.php">Widgets</a> page. Fill out the form, save, and you're done.</p>
            
            
            
            <hr />
            
            <a name="ref"></a>
            <h3>Zotpress Reference</h3>
            
            <p>
                Zotpress Reference is a metabox widget that shows up on your writing pages. It lets you quickly retrieve item keys for collections, tags, and citations.
                You can <strong>hide or show</strong> the widget using the "Screen Options" tab found at the upper-right corner of the screen when adding or
                editing posts.
            </p>
            
            
            
            <hr />
            
            <a name="faq"></a>
            <h3>F.A.Q.</h3>
            
            <p>
                Check out the answered questions below. If you can't find what you're looking for, feel free to post your question at the
                <a title="Zotpress Forums" href="http://wordpress.org/support/plugin/zotpress">Zotpress Support Forums</a>.
            </p>
            
            <h4>How can I sync or re-import my local library in Zotpress?</h4>
            
            <p>
                You can use the buttons found on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts</a>
                page next to the account for which you'd like to selectively import or re-import items.
            </p>
            
            <h4>Will Zotpress auto-sync my library?</h4>
            
            <p>
                Unfortunately, not yet. This feature is in the works. For now, you have to manually sync your library
                by selectively importing collections (and their items and subcollections) or re-importing your library.
            </p>
            
            <h4>How can I edit a Zotero account listed on the Accounts page?</h4>
            
            <p>You can't, but you <em>can</em> delete the account and re-add it with the new information.</p>
            
            <h4>How do I find a group ID?</h4>
            
            <p>
                There are two ways, depending on the age of the group.
                Older Zotero groups will have their group ID listed in the URL: a number 1-6+ digits in length after "groups". New Zotero groups may hide their group ID behind a moniker.
                If you're the group owner, you can login to <a title="Zotero" href="http://www.zotero.org/">Zotero</a>, click on "Groups", and then hover over or click on "Manage Group" under the group's title.
                Everyone else can view the RSS Feed of the group and note the group id in the URL.
            </p>
            
            <h4>I've added a group to Zotpress, but it's not displaying citations. How do I display a group's citations?</h4>
            
            <p>
                You can list any group on Zotpress as long as you have the correct private key.
                If you're not the group owner, you can try sending the owner a request for one.
            </p>
            
            <h4>How do I find a collection ID?</h4>
            
            <p>It's displayed next to the collection name on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page.</p>
            
            <h4>How do I find an item key (citation ID)?</h4>
            
            <p>It's displayed beneath the citation on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page. It's also listed on the dropdown associated with each item you search via the Reference widget (found on post add/edit screens).</p>
            
            <h4>Zotpress won't import my library, or only imports some of my library.</h4>
            
            <p>First, check with your web host or server admin to make sure that one of cURL, fopen with Streams (PHP 5), or fsockopen is enabled. If so, check to see if your server has any restrictions on timeouts (Zotpress sometimes needs more than 30 seconds to process a request to the Zotero servers).</p>
            
        </div>