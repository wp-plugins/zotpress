=== Plugin Name ===
Contributors: kseaborn
Plugin Name: Zotpress
Plugin URI: http://katieseaborn.com/plugins/
Donate link: http://katieseaborn.com/
Tags: zotero, zotpress, citation manager, citations, citation, cite, citing, bibliography, bibliographies, reference, referencing, references, reference list, reference manager, academic, academia, scholar, scholarly, cv, curriculum vitae, resume
Author URI: http://katieseaborn.com/
Author: Katie Seaborn
Requires at least: 3.0.4
Tested up to: 3.1.2
Stable tag: 4.1

Zotpress displays your Zotero citations on Wordpress.

== Description ==

[Zotpress](http://katieseaborn.com/plugins/ "Zotpress for WordPress") displays your [Zotero](http://zotero.org/ "Zotero") citations on Wordpress. It also extends Zotero's meta functionality by allowing you to add thumbnail images to your citations.

[Zotero](http://zotero.org/ "Zotero") is a community-based cross-platform citation manager that integrates with your browser and word processor.

= Features =
* Display your Zotero citations on your blog
* Display citations, collections, or tags
* Selective CSS styling via IDs and classes
* Add both user and group Zotero accounts
* Add thumbnail images to your citations
* Let visitors download your publications
* And more!

Tested in Firefox 4, Safari 5, IE7 and IE8.

= Requirements =
jQuery included in your theme, cUrl [preferably] or file_get_contents enabled on your server.  Optional, but recommended: OAuth enabled on your server.

== Installation ==

1. Upload the folder `zotpress` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. If your theme doesn't already support jQuery, you'll need to add `<?php wp_enqueue_script("jquery"); ?>` above the `<?php wp_head(); ?>` call in your theme's header template.
1. Place the `[zotpress]` shortcode in your blog entry or enable the Zotpress sidebar widget.

= Shortcode =
You can display your Zotero citations in a number of ways. To display a complete list of citations for an account in the default bibliography style (APA), simply use this shortcode:

[zotpress userid="00000"]

An example of the shortcode using parameters is:

[zotpress collection="ZKDTKM3X" limit="5"]

This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X".

= Shortcode Parameters: =
Here's a list of parameters you can use to display projects in different ways:

* `userid` display a list of citations from a particular user or group. REQUIRED if not using "nickname" parameter.
* `nickname` display a list of citations by a particular Zotero account nickname.
* `author` display a list of citations from a particular author. Format as follows: "Firstname+Lastname", e.g. "Carl+Sagan". Note: "C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Carl Sagan".
* `year` display a list of citations from a particular year. Format as follows: "2009". Note: You can display by Author and Year together.
* `datatype` display a list of a particular data type. Options: items [default], tags, collections
* `collection` id of the collection to draw citations from.
* `item` item key for a single item.
* `tag` name of the tag to draw citations from. Note: make sure you replace all spaces with a + sign, e.g. the tag "electric fish" becomes "electric+fish".
* `content` format of citation display. Options: html, bib [default]
* `style` citation style. Options: chicago-note-bibliography, harvard1, mhra, mla, nature, vancouver, apsa, asa, apa [default]. Note: Support for more styles coming; see Zotero Style Repository for details.
* `sort` sort direction of the order field. Options: asc, desc [default]
* `sortby` a temporary "order" paramater. Options: author, date, latest added [default]
* `title` display a title by year. Options: yes, no [default]
* `limit` limit the item list to a certain amount. Options: numbers between 1-99 [default: 50]
* `showimage` whether or not to display the citation's image, if there is one. Options: yes, no [default]
* `downloadable` whether or not to display the citation's download URL, if there is one. Options: yes, no [default]

== Frequently Asked Questions ==

The F.A.Q. can be found on the "Help" page of every Zotpress install. If you have a question that isn't answered there, freel free to post a message in the [forums](http://wordpress.org/tags/zotpress "Zotero forums on Wordpress.com").

== Screenshots ==

1. Display and filter your Zotero citations by account, collection or tag on the admin page. Upload images to citations. Special characters are supported.
2. Manage both user and group Zotero accounts. Easy private key creation using OAuth (as long as your server supports it).
3. Search for item keys, citation ids and tag names using the convenient "Zotpress Reference" meta box.

== Changelog ==

= 1.0 =
* Zotpress makes its debut.

= 1.1 =
* Fixed up the readme.txt. Added a friendly redirect for new users. Made IE8-compliant. Moved some JS calls to footer. Now selectively loads some JS. Made tags and collections into lists for easier formatting.

= 1.2 =
* Optimized JavaScript functions. Fixed some grammatical errors on the Help page. More selective loading of JavaScript. And most importantly ... added a Zotpress widget option. This also means you can have more than one Zotpress call on a single page.

= 1.3 =
* Added cURL, which is (maybe?) quicker, (definitely?) safer, and (more likely to be?) supported. Requests default to cURL first now.

= 1.4 =
* Caching enabled, which should speed things up a bit.

= 1.5 =
* Groups citation style issue fixed.

= 1.6 =
* Critical request method issue fixed.

= 2.0 =
* Zotpress completely restructured.
* Most requests now made through PHP. Shortcode requests made through PHP/jQuery combo for user-friendliness on the front-end.
* Cross-user caching implemented. Updates request data every 10 minutes and only if request made.
* Increased security now that private keys are no longer exposed through JavaScript.
* Can now filter by Tag in admin.

= 2.1 =
* Now cURL-friendly again.

= 2.2 =
* Fixed CURLOPT_FOLLOWLOCATION error.

= 2.3 =
* Fixed Group "invalid key" error.

= 2.4 =
* Can now display by year.
* New option to display download links, should they be available.

= 2.5 =
* Re-wrote display code.
* Tidied up JavaScript.
* Fixed update table code.

= 2.5.1 =
* Fixed single citation display bug.

= 2.5.2 =
* Fixed image display for author/year citations.

= 2.6 =
* Important: Reduced multiple instantiations of JavaScript.
* Download option added to Widget.
* Proper download links for PDFs implemented.

= 2.6.1 =
* Can now give group accounts a public key.
* Downloads can now be accessed by anyone (assuming you've enabled downloading).

= 3.0 =
* New "Zotpress Reference" widget, meant to speed up the process of adding shortcodes to your posts and pages by allowing you to selectively search for ids directly on the add and edit pages.
* OAuth is now supported, which means that you don't have to go out of your way to generate the required private key for your Zotero account anymore (unless your server doesn't support OAuth, of course).
* I've changed the way Zotpress's admin splash page loads. Before, the page would hang until finished loading the latest citations from Zotero. This is a friendlier way of letting you know what Zotpress is up to.
* Manual re-caching and clear cache options added, for those who desire to refresh the cache at their leisure.
* Citations that have URLs will now have their URLs automatically hyperlinked.
* More IDs and classes added for greater CSS styling possibilities.
* Improved handling of multiple Zotpress shortcode calls on a single page.
* Code reduced and refined plugin-wide, which should equal an overall performance improvement.
* "Order" parameter no longer available, at least for now; see http://www.zotero.org/support/dev/server_api
* "Forcing cURL" option abandoned. If your server supports it, cURL will be used; otherwise, Zotpress will resort to file_get_contents(). 

= 3.0.1 =
* Sidebar widget fixed.
* Styles in IE refined.
* Conditional OAuth messages implemented.

= 3.0.2 =
* Meta box fixed in IE and Safari.
* Styles fixed in IE and Safari.

= 3.0.3 =
* Groups accounts citation display fixed.

= 3.0.4 =
* Fixed display images issue.
* Separated out sidebar widget code from main file.

= 3.1 =
* New way of caching requests. Speed increase for requests that have already been cached.
* No more multiple accounts per shortcode. A "user_api_id" or "nickname" must be set.
* No more collection titles. You can use the Zotero Reference meta box to find and add this information above collection shortcode calls.

= 3.1.1 =
* Fix: Sidebar widget bug.

= 3.1.2 =
* Added backwards compatibility measure with respect to the new api_user_id / nickname requirement.
* Fixed citation display positioning bugs.
* Applied new caching method to sidebar widget.

= 3.1.3 =
* Temporary fix for web servers that don't support long URLs. Unfortunately no special caching for these folks. New solution in the works.

= 4.0 =
* Switched method of requesting from jQuery to PHP. Should mean a speed increase (particularly for Firefox users).
* Many shortcode parameters have been changed; these parameters are now deprecated: api_user_id (now userid), item_key (now item), tag_name (now tag), data_type (now datatype), collection_id (now collection), download (now downloadable), image (now showimage).
* New shortcode parameter "sortby" allows you to sort by "author" (first author) and "date" (publication date). By default, citations are sorted by latest added.

= 4.1 =
* Bugfixes: Filtering by author and date reinstated.
* New: Titles by year. (New parameter: title)

== Upgrade Notice ==

= 1.2 =
Lots of little issues fixed. Plus, you can now use a Zotpress widget instead of shortcode.

= 1.3 =
Implemented cURL, which should help those having read/write issues on their server.

= 1.4 =
Speed increase with newly added caching feature.

= 1.5 =
Important: Groups citation style issue fixed.

= 1.6 =
Critical request method issue fixed.

= 2.0 =
Zotpress overhaul. Security and performance increases.

= 2.1 =
Now cURL-friendly again.

= 2.2 =
Fixed CURLOPT_FOLLOWLOCATION error.

= 2.3 =
Fixed Group "invalid key" error.

= 2.4 =
Can now display by year. Option to display download links.

= 2.5 =
Re-wrote display code and tidied up JavaScript. Fixed update table code.

= 2.5.1 =
Fixed single citation display bug.

= 2.6 =
Important: JavaScript reductions; download option added to Widget; proper PDF download links.

= 2.6.1 =
Downloads can now be accessed by anyone.

= 3.0 =
Major release! OAuth, convenient "Zotpress Reference" meta box, friendly lag handling, numerous bug fixes, and more!

= 3.0.1 =
Sidebar widget fixed.

= 3.0.2 =
Meta box now working in IE and Safari!

= 3.0.3 =
Groups accounts citation display fixed.

= 3.0.4 =
Fixed display images issue.

= 3.1 =
Speed increase and a new way of caching. No more multiple accounts per shortcode. No more auto-display of collection title.

= 3.1.1 =
Fixed Sidebar widget bug.

= 3.1.2 =
Bug fixes and clean up.

= 3.1.3 =
Bug fixes.

= 4.0 =
Requests now processed by PHP instead of jQuery. Shortcode parameters re-envisioned (but backwards-compatible). Can now sort by author and date.

= 4.1 =
Bugfixes: Filtering by year and author reinstated. New: Titles for year.