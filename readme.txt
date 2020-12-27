=== Related Posts by Categories and Tags ===
Tags: related posts, contextual related posts, related, similar posts, posts, custom post types, tags, categories
Contributors: webberzone, Ajay
Donate link: http://ajaydsouza.com/donate/
Stable tag: 1.6.0
Requires at least: 5.0
Tested up to: 5.6
License: GPLv2 or later

Restrict the related posts to the same category, tag or custom taxonomy. Requires Contextual Related Posts.

== Description ==

**This is the penultimate version of Related Posts by Categories and Tags.** Contextual Related Posts v3.0.0 will incorporate all the functionality of this plugin.

[Related Posts by Categories and Tags](https://webberzone.com/downloads/crp-taxonomy/) is an extension for [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) that adds another degree of related posts matching by adding the option to restrict these related posts to the same category, tag or custom taxonomy.

Additionally, you can also disable the contextual matching engine either on posts/pages or on only custom post types.

Requires Contextual Related Posts v2.6.0 or higher.


= Contribute =

**Related Posts by Categories and Tags** is open for contribution on [Github](https://github.com/ajaydsouza/crp-taxonomy)

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
3. New options in WP-Admin - Output options


== Frequently Asked Questions ==

If your question isn't listed here, please create a new post detailing your problem in the [WordPress.org support forum](http://wordpress.org/support/plugin/crp-taxonomy). It is the fastest way to get support as I monitor the forums regularly. I also provide [premium *paid* support via email](https://webberzone.com/support/).


== Changelog ==

= 1.6.0 =

This is the penultimate version and is compatible with Contextual Related Posts v2.9.3 and below. All its functonality will be included in Contextual Related Posts v3.0.0.

Some settings have been renamed in preparation for the new version.

* Enhancements:
	* Exclude on categories uses the short ciruit filter introduced in Contextual Related Posts v2.9.0
	* Use `$args` instead of global `$crp_settings` for all filters

* Bug fixes:
	* Use `crp_get_settings` instead of `crp_read_options`

= 1.5.0 =

Release post: [https://webberzone.com/blog/related-posts-by-categories-and-tags-v1-5-0/](https://webberzone.com/blog/related-posts-by-categories-and-tags-v1-5-0/)

* Features:
	* New option to enter the minimum number of common taxonomies to be matched before a post is considered related
	* New option to exclude display on select post categories. Does not work with tags or custom taxonomies

* Bug fixes:
	* Resetting Contextual Related Posts settings with this plugin activated caused an error

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

= 1.5.0 =
Bug fix, new options. Check Changelog for detailed updates
