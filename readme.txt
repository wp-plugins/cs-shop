=== CS Shop ===
Contributors: cottonspace
Tags: ads,affiliate
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.9.1

You can easily create a product search page from the affiliate services of Japan.

== Description ==

You can easily create a product search page from the affiliate services of Japan.

Simply by writing a short code to just one page, shopping malls will be created on your page.

Unfortunately, this plugin is only available in Japan.

In the first version only supports the Rakuten affiliate service. However, in future versions, I will also support the other affiliate services.

== Installation ==

1. Upload `cs-shop` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Select the setting of 'CS Shop' from the Admin menu, Please save to set the account information for the affiliate service.
1. Create a new page, Please write a short code `[csshop service="rakuten"]` to that page. (This is the case you select the Rakuten.)
1. Please visit the page that you created. Categories of the affiliate service that you set should be displayed.

This is the most simple configuration steps. Short code to a variety of attributes can be set.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 0.9.1 =
* Add Timeout and Retry feature for service access.

= 0.9 =
* The first release version.

== Upgrade Notice ==

= 0.9.1 =
If you have problem which the search results is not displayed, which may be resolved.

= 0.9 =
The first release version.

== Shortcode Example ==

To view the results of a search by specifying the keyword 'foo' automatically. (This is the case you select the Rakuten.)

`[csshop service="rakuten" action="search" keyword="foo"]`

To view the results of a search by specifying the category 'computers' automatically. (This is the case you select the Rakuten.)

`[csshop service="rakuten" action="search" category="100026"]`
