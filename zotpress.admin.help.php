        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.admin.display.tabs.php'); ?>
            
            <h3>What is Zotpress?</h3>
            
            <div class="zp-Message">
                <p>
                    If you like Zotpress, let the world know with a <a class="zp-FiveStar" title="Rate Zotpress" href="http://wordpress.org/extend/plugins/zotpress/">rating</a> on Wordpress.com!
                </p>
            </div>
            
            <p>
                <a href="http://katieseaborn.com/plugins/">Zotpress</a> displays your <a href="https://www.zotero.org/settings/keys">Zotero</a> citations on Wordpress.
                It also extends the basic meta functionality offered by Zotero by allowing you to add thumbnail images to and upload publicly visible PDF files of your citations.
            </p>
            
            <p>There's a few ways to use Zotpress:</p>
            
            <ol class="zp-WaysToUseZotpress">
                <li>
                    <strong>The Zotpress Shortcode</strong><br />
                    Generate a bibliography of your Zotero citations wherever you can call shortcodes. <a href="#zotpress">Learn more &raquo;</a>
                </li>
                <li>
                    <strong>The Zotpress InText Shortcode</strong><br />
                    Generate APA-styled in-text citations and generate a bibliography that appears at the bottom of your entry. <a href="#intext">Learn more &raquo;</a>
                </li>
                <li>
                    <strong>The Zotpress Sidebar Widget</strong><br />
                    Drag-n-drop this widget onto a sidebar and fill out the form to generate a bibliography. <a href="#widget">Learn more &raquo;</a>
                </li>
            </ol>
            
            
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
                This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X". (<strong>Hint:</strong> Collection keys are listed beside each item in the Collections dropdown on the <a href="admin.php?page=Zotpress&amp;display=true">Citations page</a>.)
            </p>
            
            <h4 class="short">Shortcode Parameters</h4>
            
            <h5>Filter by Acccount</h5>
            <ul>
                <li><code>userid</code> display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</li>
                <li><code>nickname</code> display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</li>
            </ul>
            
            <h5>Filter by Author or Year</h5>
            <ul>
                <li><code>author</code> display a list of citations from a particular author. Format as follows: "Firstname+Lastname", e.g. "Carl+Sagan". <strong>Note:</strong> "C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Carl Sagan".</li>
                <li><code>year</code> display a list of citations from a particular year. Format as follows: "2009". <strong>Note:</strong> You <em>can</em> display by Author and Year together.</li>
            </ul>
            
            <h5>Filter by Type</h5>
            <ul>
                <li><code>datatype</code> display a list of a particular data type. Options: items [default], tags, collections</li>
                <li><code>collection</code> id of the collection/s to draw citations from. For multiple collections, format in a list, e.g. colleciton="GMGCJU34,U9Z5JTKC"</li>
                <li><code>item</code> item key for single item/s.  For multiple items, format in a list, e.g. item="GMGCJU34,U9Z5JTKC"</li></li>
                <li><code>tag</code> name of the tag to draw citations from. <strong>Note:</strong> make sure you replace all spaces with a <code>+</code> sign, e.g. the tag "electric fish" becomes "electric+fish".</li>
            </ul>
            
            <h5>Display Settings</h5>
            <ul>
                <li><code>content</code> format of citation display. Options: html, bib [default]</li>
                <li><code>style</code> citation style. Options: apsa, apa [default], asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, mla, nlm, nature, vancouver. <strong>Note:</strong> Support for more styles is coming; see <a href="http://www.zotero.org/styles">Zotero Style Repository</a> for details. <strong>Note:</strong> I haven't been able to get these styles working: chicago-note-bibliography, chicago-note, ieee, mhra, mhra_note_without_bibliography.</li>
                <!--<li><code>order</code> order by a certain field. Options: itemType, language, conferenceName, volume, issue, place, publisher, date, series, seriesTitle, dateModified, dateAdded [default]. Potentially many more; see <a href="http://www.zotero.org/support/dev/data_model">Zotero Data Model</a>.</li>-->
                <li><code>sortby</code> a temporary "order" paramater. Options: author, date, latest added [default]</li>
                <li><code>sort</code> sort direction of the order field, which is update date by default. Options: asc, desc [default]</li>
                <li><code>title</code> display a title by year. Options: yes, no [default]</li>
                <li><code>limit</code> limit the item list to a certain amount. Options: numbers between 1-99 [default: 50]</li>
                <li><code>showimage</code> whether or not to display the citation's image, if there is one. Options: yes, no [default]</li>
                <!--<li><code>url</code> whether or not to hyperlink the displayed citation URL, if there is one. Options: yes [default], no</li>-->
                <li><code>downloadable</code> whether or not to display the citation's download URL, if there is one. <strong>Enable this option only if you are legally able to provide your files for download.</strong> Options: yes, no [default]</li>
                <li><code>notes</code> whether or not to display the citation's notes, if they exist. <strong>Must have notes made publicly viewable via the private key settings on Zotero.</strong> Options: yes, no [default]</li>
            </ul>
            
            
            <a name="intext"></a>
            <h3>Displaying Citations Using the Zotpress InText Shortcode</h3>
            
            <p>
                When you use one or more Zotpress Intext shortcodes, a bibliography will be auto-generated at the end of the entry. Here's an example:
            </p>
            
            <p class="example">
                Katie said, "I am a Zotpress user, and I am rad" <code>[zotpressInText item="U9Z5JTKC" pages="36"]</code>.
            </p>
            
            <p>Which will display on your blog as:</p>
            
            <p class="example">
                Katie said, "I am a Zotpress user, and I am rad" (Seaborn, 2011, p. 36).
            </p>
            
            <p>
                ... with an auto-generated bibliography at the bottom of the entry, of course.
            </p>
            
            <h4>Shortcode Parameters</h4>
            
            <ul>
                <li><code>item</code> item key for a single item. <strong>Hint:</strong> Use Zotpress Reference to quickly retrieve item keys.</li>
                <li><code>pages</code> what page/s you're referencing, if any. OPTIONAL.</li>
                <li><code>userid</code> set the user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</li>
                <li><code>nickname</code> set based on a Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</li>
            </ul>
            
            
            <a name="widget"></a>
            <h3>Displaying Citations Using the Zotpress Sidebar Widget</h3>
            
            <p>You can drag-n-drop a Zotpress sidebar widget on your <a href="widgets.php">Widgets</a> page. Fill out the form, save, and you're done.</p>
            
            
            <h3>Zotpress Reference</h3>
            
            <p>Zotpress Reference is a metabox widget that shows up on your writing pages. It lets you quickly retrieve item keys for collections, tags, and citations. You can hide or show the widget using the "Screen Options" tab when adding or editing posts.</p>
            
            
            <h3>Pre-emptive F.A.Q.</h3>
            
            <p>These questions haven't been asked yet, but they <em>could</em> be&mdash;so I'll pre-empt them with an answer each.</p>
            
            <h4>Why do citations take a bit of time to load?</h4>
            
            <p>You may encounter a delay when Zotpress populates the cache or checks to see if there's an update. If you're using cURL, things should be quite a bit quicker since the first version of Zotpress. Also, the diligent folks at Zotero are working on their server speed as you read this.</p>
            
            <h4>Why are only 99 citations listed? I know I have more.</h4>
            
            <p>The Zotero server has a 99 citation request limit right now. Hopefully this limit will be lifted at some point. In the meantime, apply shortcode paramaters to narrow your results: this should let right ones through.</p>
            
            <h4>How can I edit a Zotero account listed on the Accounts page?</h4>
            
            <p>You can't. I thought it would be easier to just delete the listing and re-add it with the new information. If I'm wrong, let me know, and I'll see about adding "Edit" functionality.</p>
            
            <h4>How do I find a group ID?</h4>
            
            <p>
                There are two ways, depending on the age of the group.
                Older Zotero groups will have their group ID listed in the URL: a number 1-6+ digits in length after "groups". New Zotero groups may hide their group ID behind a moniker.
                If you're the group owner, you can login to <a href="http://www.zotero.org/">Zotero</a>, click on "Groups", and then hover over or click on "Manage Group" under the group's title.
                Everyone else can view the RSS Feed of the group and note the group id in the URL.
            </p>
            
            <h4>I've added a group to Zotpress, but it's not displaying citations. How do I display a group's citations?</h4>
            
            <p>
                You can list any group on Zotpress as long as you have the correct private key. If you're not the group owner, you can try sending the owner a request for one.
            </p>
            
            <h4>How do I find a collection ID?</h4>
            
            <p>It's displayed next to the collection name in the filter dropdown on the <a href="admin.php?page=Zotpress&amp;display=true">Citations</a> page.</p>
            
            <h4>How do I find an item key (citation ID)?</h4>
            
            <p>It's displayed beneath the citation on the <a href="admin.php?page=Zotpress&amp;display=true">Citations</a> page.</p>
            
            <h4>I don't want collection names to display above my citations. How do I get rid of them?</h4>
            
            <p>In your stylesheet, add the following line: <code>h3.zp-Collection-Header { display: none; }</code> By the way, almost every Zotpress element has either an ID or class (or both) and can be selectively styled with CSS.</p>
            
            <h4>What happened to the "order" parameter and sorting in general?</h4>
            
            <p>As far as I can tell, it's not working on the Zotero side of things as of Zotpress 3.0. Please see the note beside "order" on <a href="http://www.zotero.org/support/dev/server_api">this page</a>. In the meantime, try using the temporary "sortby" parameter.
            
            <!--<h4>Why isn't Zotpress sorting my citations?</h4>-->
            <!---->
            <!--<p>Make sure that you're trying to sort a <em>set</em> of citations, not a single citation. The "sort", "order" and "limit" shortcode parameters will only work on a set of citations. Also, multiple Zotpress shortcode calls can't be sorted together (at least for now).-->
            
        </div>