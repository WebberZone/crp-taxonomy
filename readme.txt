=== Related Posts by Categories and Tags ===
Tags: related posts, related, similar posts, posts, custom post types, tags, categories
Contributors: webberzone, Ajay
Donate link: http://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.8
Tested up to: 5.3
License: GPLv2 or later

Restrict the related posts to the same category, tag or custom taxonomy. Requires Contextual Related Posts.

== Description ==

[CRP Taxonomy Tools](https://webberzone.com/downloads/crp-taxonomy/) is an extension for [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) that adds another degree of related posts matching by adding the option to restrict these related posts to the same category, tag or custom taxonomy.

Additionally, you can also disable the contextual matching engine either on posts/pages or on only custom post types.

Requires Contextual Related Posts v2.6.0 or higher.


= Contribute =

Related Posts by Categories and Tags is open for contribution on [Github](https://github.com/ajaydsouza/crp-taxonomy)

So, if you've got some cool feature that you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.


== Installation ==

= WordPress install (the easy way) =
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Related Posts by Categories and Tags"

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =
1. Download the plugin

2. Extract the contents of crp-taxonomy.zip to wp-content/plugins/ folder. You should get a folder called crp-taxonomy.

3. Activate the Plugin in WP-Admin.

4. Goto **Settings &raquo; Related Posts** to configure. You'll find two checkboxes under General Options


== Screenshots ==

1. New options in WP-Admin - General options
2. New options in WP-Admin - List Tuning options

== Frequently Asked Questions ==

If your question isn't listed here, please create a new post detailing your problem in the [WordPress.org support forum](http://wordpress.org/support/plugin/crp-taxonomy). It is the fastest way to get support as I monitor the forums regularly. I also provide [premium *paid* support via email](https://webberzone.com/support/).


== Changelog ==

= 1.4.2 =

* Bug fixes:
	* Resetting Contextual Relatd Posts settings with this plugin activated caused an error

= 1.4.1 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v2-6-1/](https://webberzone.com/blog/contextual-related-posts-v2-6-1/)

* Bug fixes:
	* Saving settings added admin notices incorrectly
	* Delete settings on uninstall

= 1.4.0 =

Release post: [https://webberzone.com/blog/related-posts-by-categories-and-tags-v1-4-0/](https://webberzone.com/blog/related-posts-by-categories-and-tags-v1-4-0/)

Plugin has been renamed to "Related Posts by Categories and Tags"

* Enhancements:
	* Upgrade the plugin for the new Settings API in Contextual Related Posts v2.6.0
	* Add a notice in the admin page if Contextual Related Posts v2.6.0 and above is not installed

* Deprecated:
	*  Old settings interface - crpt_crp_default_options, crpt_save_options, crt_general_options, crt_tuning_options

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

= 1.4.2 =
Bug fix release. Check Changelog for detailed updates
