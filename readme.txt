=== Contextual Related Posts Taxonomy Tools ===
Tags: related posts, related, similar posts, posts, custom post types, tags, categories
Contributors: webberzone, Ajay
Donate link: http://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.1
Tested up to: 4.8
License: GPLv2 or later

Restrict the related posts by Contextual Related Posts to the same category, tag or custom taxonomy

== Description ==

[CRP Taxonomy Tools](https://webberzone.com/downloads/crp-taxonomy/) is an extension for [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) that adds another degree of related posts matching by adding the option to restrict these related posts to the same category, tag or custom taxonomy. This plugin adds additional checkboxes under General options under the Related Posts page.

Additionally, you can also disable the contextual matching engine either on posts/pages or on only custom post types.

Requires Contextual Related Posts v2.0 or higher.


= Contribute =

Contextual Related Posts Taxonomy Tools is open for contribution on [Github](https://github.com/ajaydsouza/crp-taxonomy)

So, if you've got some cool feature that you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.


== Installation ==

= WordPress install (the easy way) =
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Contextual Related Posts Taxonomy Tools"

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =
1. Download the plugin

2. Extract the contents of crp-taxonomy.zip to wp-content/plugins/ folder. You should get a folder called crp-taxonomy.

3. Activate the Plugin in WP-Admin.

4. Goto **Settings &raquo; Related Posts** to configure. You'll find two checkboxes under General Options


== Screenshots ==

1. CRP Taxonomy options in WP-Admin - General options


== Frequently Asked Questions ==

If your question isn't listed here, please create a new post detailing your problem in the [WordPress.org support forum](http://wordpress.org/support/plugin/crp-taxonomy). It is the fastest way to get support as I monitor the forums regularly. I also provide [premium *paid* support via email](https://webberzone.com/support/).


== Changelog ==

= 1.3.0 =
* Enhancements:
	* When "Match all taxonomy terms" is selected, only taxonomies for the current post type is selected. This reduces the cases where no posts are found. Posts are also now properly ranked by relevancy

* Bug fixes:
	* Fixed PHP notices

= 1.2.0 =
* Features:
	* Option to match by all taxonomies - Contributed by [Enchiridion](https://github.com/Enchiridion)

* Bug fixes:
	* Filters not working when only taxonomies are being used - Contributed by [Enchiridion](https://github.com/Enchiridion)
	* Activation of the plugin failed with Contextual Related Posts v2.2.x

= 1.1.0 =
* Features:
	* Added support for custom taxonomies. Very useful if your custom post types have custom taxonomies
	* Disable contextual matching

= 1.0.0 =
* Initial release


== Upgrade Notice ==

= 1.3.0 =
* Bug fixes; Optimisation when selecting "Match all taxonomy terms";
Check Changelog for detailed updates
