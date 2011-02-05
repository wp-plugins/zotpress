        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.tabs.php'); ?>
            
            <h3>What is Zotpress?</h3>
            
            <p>
                Zotpress displays your <a href="https://www.zotero.org/settings/keys">Zotero</a> citations on Wordpress.
                It also extends the basic meta functionality offered by Zotero by allowing you to add a thumbnail image to your citations.
            </p>
            
            <h3>Displaying Citations Using Shortcode</h3>
            
            <p>
                You can display your Zotero citations in a number of ways.
                To display a complete list of citations for all accounts in the default bibliography style (APA), simply use this shortcode:
            </p>
            
            <code>
                [zotpress]
            </code>
            
            <h4>Shortcode Parameters</h4>
            
            <p>
                Here's a list of parameters you can use to display projects in different ways:
            </p>
            
            <ul>
                <li><code>api_user_id</code> display a list of citations from a particular user or group.</li>
                <li><code>nickname</code> display a list of citations by a particular nickname.</li>
                <li><code>author</code> display a list of citations from a particular author. Format as follows: "Firstname+Lastname", e.g. "Carl+Sagan". <strong>Note:</strong> "C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Carl Sagan".</li>
            </ul>
            
            <ul>
                <li><code>data_type</code> display a list of a particular data type. Options: items [default], tags, collections</li>
                <li><code>collection_id</code> id of the collection to draw citations from.</li>
                <li><code>item_key</code> item key for a single item.</li>
                <li><code>tag_name</code> name of the tag to draw citations from. <strong>Note:</strong> make sure you replace all spaces with a <code>+</code> sign, e.g. the tag "electric fish" becomes "electric+fish".</li>
            </ul>
            
            <ul>
                <li><code>content</code> format of citation display. Options: html, bib [default]</li>
                <li><code>style</code> citation style. Options: chicago-note-bibliography, harvard1, mhra, mla, nature, vancouver, apsa, asa, apa [default]. <strong>Note:</strong> Support for more styles is coming; see <a href="http://www.zotero.org/styles">Zotero Style Repository</a> for details.</li>
                <li><code>order</code> order by a certain field. Options: itemType, language, conferenceName, volume, issue, place, publisher, date, series, seriesTitle, dateModified, dateAdded [default]. Potentially many more; see <a href="http://www.zotero.org/support/dev/data_model">Zotero Data Model</a>.</li>
                <li><code>sort</code> sort direction of the order field. Options: asc, desc [default]</li>
                <li><code>limit</code> limit the item list to a certain amount. Options: numbers between 1-99 [default: 50]</li>
            </ul>
            
            <ul>
                <li><code>image</code> whether or not to display the citation's image, if there is one. Options: yes, no [default]</li>
            </ul>
            
            <?php /* ?>
            <ul>
                <li><code>curl</code> try Curl if you're server is strict. Option: true</li>
            </ul>
            <?php */ ?>
            
            <h3>An Example</h3>
            
            <p>
                An example of the shortcode using parameters is:
            </p>
            
            <code>
                [zotpress collection="ZKDTKM3X" limit="5"]
            </code>
            
            <p>
                This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X".
            </p>
            
            <h3>Pre-emptive F.A.Q.</h3>
            
            <p>These questions haven't been asked yet, but they <em>could</em> be&mdash;so I'll pre-empt them with an answer each.</p>
            
            <h4>Why do citations take so long to load?</h4>
            
            <p>I've found the Zotero server to be a little slow. Not much any of us can do about it.</p>
            
            <h4>Why are only 99 citations listed? I know I have more.</h4>
            
            <p>The Zotero server has a 99 citation request limit right now. Hopefully this limit will be lifted at some point. In the meantime, apply shortcode paramaters to narrow your results: this should let right ones through.</p>
            
            <h4>How can I edit a Zotero account listing?</h4>
            
            <p>You can't. I thought it would be easier to just delete the listing and re-add it with the new information. If I'm wrong, let me know, and I'll see about adding "Edit" functionality.</p>
            
            <h4>How do I find a group ID?</h4>
            
            <p>
                There are two ways, depending on the age of the group.
                Older Zotero groups will have their group ID listed in the URL: a five-digit number after "groups". New Zotero groups may hide their group ID behind a moniker.
                If you're not the group owner, you can login to <a href="http://www.zotero.org/">Zotero</a>, click "Groups", and then hover over or click on "Manage Group" under the group's title.
                Everyone else can view the RSS Feed of the group and note the group id in the URL.
            </p>
            
            <h4>I've added a group to Zotpress, but it's not displaying citations. How do I display a group's citations?</h4>
            
            <p>
                You can list any group on Zotpress, but only groups that have a "Public" Group Type will allow you to display their citations publicly, and
                only the group owner can set this option. If you're not the group owner, you can try sending the owner a request to make the group's citations public.
            </p>
            
            <h4>How do I find a collection ID?</h4>
            
            <p>It's displayed next to the collection name in the filter dropdown on the <a href="admin.php?page=Zotpress&amp;display=true">Citations</a> page.</p>
            
            <h4>How do I find an item key (citation ID)?</h4>
            
            <p>It's displayed beneath the citation on the <a href="admin.php?page=Zotpress&amp;display=true">Citations</a> page.</p>
            
        </div>