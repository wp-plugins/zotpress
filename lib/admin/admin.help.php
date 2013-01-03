
        <div id="zp-Zotpress" class="wrap">
            
            <?php include('admin.display.tabs.php'); ?>
            
            <h3>What is Zotpress?</h3>
            
            <div class="zp-Message">
                <p>
                    If you like Zotpress, let the world know with a <a class="zp-FiveStar" title="Rate Zotpress" href="http://wordpress.org/extend/plugins/zotpress/">rating</a> on Wordpress.com!
                </p>
            </div>
            
            <p>
                <a title="More of my plugins" href="http://katieseaborn.com/plugins/">Zotpress</a> bridges <a title="Zotero" href="https://www.zotero.org/settings/keys">Zotero</a>
                and Wordpress by allowing you to display items from your Zotero library through shortcodes and widgets.
                It also extends the basic meta functionality offered by Zotero by allowing you to add thumbnail images to and upload public files of your citations.
            </p>
            
            <p>There's a few ways to use Zotpress:</p>
            
            <ol class="zp-WaysToUseZotpress">
                <li>
                    <strong>The Zotpress Shortcode</strong><br />
                    Generate a bibliography wherever you can call shortcodes. <a title="Learn more" href="#zotpress">Learn more &raquo;</a>
                </li>
                <li>
                    <strong>The Zotpress In-Text Shortcodes</strong><br />
                    Create in-text citations and an auto-generated bibliography. <a title="Learn more" href="#intext">Learn more &raquo;</a>
                </li>
                <li>
                    <strong>The Zotpress Sidebar Widget</strong><br />
                    Use this widget in a sidebar to generate a bibliography. <a title="Learn more" href="#widget">Learn more &raquo;</a>
                </li>
            </ol>
            
            
            
            
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
                An example of how to use shortcode parameters is:
            </p>
            
            <code>
                [zotpress collection="ZKDTKM3X" limit="5"]
            </code>
            
            <p>
                This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X". (<strong>Hint:</strong> Collection keys are listed beside each item on the <a title="Browse" href="admin.php?page=Zotpress">Browse page</a>.)
            </p>
            
            <h4 class="short">Shortcode Parameters</h4>
            
            <h5>Filter by Account</h5>
            <ul>
                <li><code>userid</code> display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</li>
                <li><code>nickname</code> display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</li>
            </ul>
            
            <h5>Filter by Author or Year</h5>
            <ul>
                <li><code>author</code> or <code>authors</code> display a list of citations from a particular author/s. Format as follows: "Firstname Lastname" or "Lastname1,Firstname Lastname2", e.g. "Carl Sagan" or "Hawking,Carl Sagan". <strong>Note:</strong> "Carl Sagan","C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Sagan".</li>
                <li><code>year</code> or <code>years</code> display a list of citations from a particular year/s. Format as follows: "2009" or "2001,2008,2009". <strong>Note:</strong> You <em>can</em> display by Author and Year together.</li>
            </ul>
            
            <h5>Filter by Type</h5>
            <ul>
                <li><code>datatype</code> display a list of a particular data type. Options: items [default], tags, collections</li>
                <li><code>item</code> or <code>items</code> item key for single item/s.  For multiple items, format in a list, e.g. item="GMGCJU34,U9Z5JTKC"</li></li>
                <li><code>collection</code> or <code>collections</code> id of the collection/s to draw citations from. For multiple collections, format in a list, e.g. collection="GMGCJU34,U9Z5JTKC"</li>
                <li><code>tag</code> or <code>tags</code> name of the tag to draw citations from. For multiple tags, format in a list, e.g. tags="tag one,tag two" <strong>Warning:</strong> Will break if tag has a comma. <!--<strong>Note:</strong> make sure you replace all spaces with a <code>+</code> sign, e.g. the tag "electric fish" becomes "electric+fish".--></li>
            </ul>
            
            <h5>Filter Settings</h5>
            <ul>
                <li><code>inclusive</code> when filtering, include all items that match ANY criteria or exclude all items except those that match the criteria exactly. Works with collection/s, tag/s, author, year. Options: yes [default], no</li>
                <li><code>sortby</code> sort multiple citations. Options: title, author, date, default (latest added) [default]</li>
                <li><code>order</code> or <code>sort</code> sort order. Options: asc [default], desc</li>
                <li><code>title</code> display a title by year. Options: yes, no [default]</li>
                <li><code>limit</code> limit the item list to a certain amount. <strong>Optional.</strong> Options: numbers between 1 and infinity</li>
            </ul>
            
            <h5>Display Settings</h5>
            <ul>
                <!--<li><code>content</code> format of citation display. Options: html, bib [default]</li>-->
                <li><code>style</code> citation style. Options: apsa, apa [default], asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, mla, nlm, nature, vancouver. <strong>Note:</strong> Support for more styles is coming; see <a title="Zotero Style Repository" href="http://www.zotero.org/styles">Zotero Style Repository</a> for details. <!--<strong>Note:</strong> I haven't been able to get these styles working: chicago-note-bibliography, chicago-note, ieee, mhra, mhra_note_without_bibliography.--></li>
                <!--<li><code>order</code> order by a certain field. Options: itemType, language, conferenceName, volume, issue, place, publisher, date, series, seriesTitle, dateModified, dateAdded [default]. Potentially many more; see <a href="http://www.zotero.org/support/dev/data_model">Zotero Data Model</a>.</li>-->
                <li><code>showimage</code> whether or not to display the citation's image, if there is one. Options: yes, no [default]</li>
                <!--<li><code>url</code> whether or not to hyperlink the displayed citation URL, if there is one. Options: yes [default], no</li>-->
                <li><code>download</code> or <code>downloadable</code> whether or not to display the citation's download URL, if there is one. <strong>Enable this option only if you are legally able to provide your files for download.</strong> Options: yes, no [default]</li>
                <li><code>notes</code> whether or not to display the citation's notes, if they exist. <strong>Must have notes made public via the private key settings on Zotero.</strong> Options: yes, no [default]</li>
                <li><code>cite</code> make the displayed citations citable by generating RIS links. Options: yes, no [default]</li>
            </ul>
            
            
            
            <hr />
            
            <a name="intext"></a>
            <h3>Displaying Citations Using the Zotpress In-Text Shortcodes</h3>
            
            <p>
                Use one or more <code>[zotpressInText]</code> shortcodes in your blog entry. Place the <code>[zotpressInTextBib]</code> somewhere in your entry to auto-generate the in-text citations bibliography. Here's an example:
            </p>
            
            <p class="example">
                Katie said, "Zotpress is cooler than your shoes" <code>[zotpressInText item="{NCXAA92F,36}"]</code>.
            </p>
            
            <p>Which will display on your blog as:</p>
            
            <p class="example">
                Katie said, "Zotpress is cooler than your shoes" (Seaborn, 2012, p. 36).
            </p>
            
            <p>
                ... with an auto-generated bibliography wherever you've placed the <code>[zotpressInTextBib]</code>, of course.
            </p>
            
            <h5>Further Examples</h5>
            <ul>
                <li>With formatting: <code>[zotpressInText item="NCXAA92F" format="%a% (%d%, %p%)"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">author (date, pages)</span></li>
                <li>With multiple items: <code>[zotpressInText item="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]</code></li>
                <li>The old way: <code>[zotpressInText item="3ITTIXHP" pages="10-15"]</code> (Single items only, not recommended.)</li>
            </ul>
            
            
            
            <h4>Shortcode Parameters</h4>
            
            <p><strong>Note:</strong> These parameters only work for the <code>[zotpressInText]</code> shortcode.</p>
            
            <ul>
                <li><code>item</code> or <code>items</code> item keys and page number pairs formatted like so: <code>ITEMKEY</code> or <code>{ITEMKEY,PAGES}</code> or <code>{ITEMKEY1,PAGES},{ITEMKEY2,PAGES},...</code>.</li>
                <li><code>format</code> how the in-text citation should be presented. Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</li>
                <li><code>pages</code> what page/s you're referencing, if any. <em>DEPRECATED</em>.</li>
                <li><code>userid</code> set the user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</li>
                <li><code>nickname</code> set based on a Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</li>
            </ul>
            
            
            <hr />
            
            <a name="widget"></a>
            <h3>Displaying Citations Using the Zotpress Sidebar Widget</h3>
            
            <p>You can drag-n-drop a Zotpress sidebar widget on your <a title="Widgets" href="widgets.php">Widgets</a> page. Fill out the form, save, and you're done.</p>
            
            
            
            <hr />
            
            <h3>Zotpress Reference</h3>
            
            <p>
                Zotpress Reference is a metabox widget that shows up on your writing pages. It lets you quickly retrieve item keys for collections, tags, and citations.
                You can <strong>hide or show</strong> the widget using the "Screen Options" tab found at the upper-right corner of the screen when adding or
                editing posts.
            </p>
            
            
            
            <hr />
            
            <h3>F.A.Q.</h3>
            
            <h4>How can I sync or re-import my local library in Zotpress?</h4>
            
            <p>
                You can use the buttons found on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts</a>
                page next to the account for which you'd like to sync or re-import items. Accounts will auto-update depending on your
                <a title="Options" href="admin.php?page=Zotpress&amp;options=true">settings</a>.
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
                You can list any group on Zotpress as long as you have the correct private key. If you're not the group owner, you can try sending the owner a request for one.
            </p>
            
            <h4>How do I find a collection ID?</h4>
            
            <p>It's displayed next to the collection name in the filter dropdown on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page.</p>
            
            <h4>How do I find an item key (citation ID)?</h4>
            
            <p>It's displayed beneath the citation on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page. It's also listed on the dropdown associated with each item you search via the Reference widget (found on post add/edit screens).</p>
            
            <h4>I don't want collection names to display above my citations. How do I get rid of them?</h4>
            
            <p>In your stylesheet, add the following line: <code>h3.zp-Collection-Header { display: none; }</code> By the way, almost every Zotpress element has either an ID or class (or both) that can be selectively styled with CSS.</p>
            
        </div>