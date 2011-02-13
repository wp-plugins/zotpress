=== Plugin Name ===
Contributors: kseaborn
Plugin Name: Zotpress
Plugin URI: http://katieseaborn.com/plugins/
Tags: zotero, zotpress, citation manager, citations, citation, bibliography, bibliographies, reference, references, reference list, reference manager, academic, academia, scholar, scholarly, cv, curriculum vitae, resume
Author URI: http://katieseaborn.com/
Author: Katie Seaborn
Requires at least: 3.0.4
Tested up to: 3.0.5
Stable tag: 2.2

Zotpress displays your Zotero citations on Wordpress.

== Description ==

Zotpress displays your [Zotero](http://zotero.org/ "Zotero") citations on Wordpress. It also extends Zotero's meta functionality by allowing you to add thumbnail images to your citations.

Zotero is a community-based cross-platform citation manager that integrates with your browser and word processor.

Features:

* Display your Zotero citations on your blog
* Display citations, collections, or tags
* Sort by a variety of options, including: author, collection, tag, and more.
* Add both user and group Zotero accounts
* And more!

Tested in Firefox 3 (Mac/Win), IE7 and IE8.

Requirements: cUrl [preferably] or file_get_contents enabled on your server.

== Installation ==

1. Upload the folder `zotpress` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the `[zotpress]` shortcode in your blog entry

**Shortcode**
You can display your Zotero citations in a number of ways. To display a complete list of citations for all accounts in the default bibliography style (APA), simply use this shortcode:

[zotpress]

**Shortcode Parameters:**
Here's a list of parameters you can use to display projects in different ways:

* api_user_id: display a list of citations from a particular user or group.
* nickname: display a list of citations by a particular nickname.
* author: display a list of citations from a particular author. Format as follows: "Firstname+Lastname", e.g. "Carl+Sagan". Note: "C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are not the same as "Carl Sagan".
* data_type: display a list of a particular data type. Options: items [default], tags, collections
* collection_id: id of the collection to draw citations from.
* item_key: item key for a single item.
* tag_name: name of the tag to draw citations from. Note: make sure you replace all spaces with a + sign, e.g. the tag "electric fish" becomes "electric+fish".
* content: format of citation display. Options: html, bib [default]
* style: citation style. Options: chicago-note-bibliography, harvard1, mhra, mla, nature, vancouver, apsa, asa, apa [default]. Note: Support for more styles coming; see Zotero Style Repository for details.
* order: order by a certain field. Options: itemType, language, conferenceName, volume, issue, place, publisher, date, series, seriesTitle, dateModified, dateAdded [default]. Potentially many more; see Zotero Data Model.
* sort: sort direction of the order field. Options: asc, desc [default]
* limit: limit the item list to a certain amount. Options: numbers between 1-99 [default: 50]
* image: whether or not to display the citation's image, if there is one. Options: yes, no [default]

**An Example**
An example of the shortcode using parameters is:

[zotpress collection="ZKDTKM3X" limit="5"]

This shortcode will display a list of five citations from the collection with the key "ZKDTKM3X". 

== Screenshots ==

1. Display and filter your Zotero citations
2. Manage your Zotero accounts

== Changelog ==

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

== Upgrade Notice ==

= 2.0 =
Zotpress overhaul. Security and performance increases.

= 2.1 =
Now cURL-friendly again.

= 2.2 =
Fixed CURLOPT_FOLLOWLOCATION error.