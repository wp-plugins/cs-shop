=== CS Shop ===
Contributors: cottonspace
Tags: ad,ads,advertising,affiliate,shortcode,yahoo,amazon
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.9.8

You can easily create a product search page from the affiliate services of Japan.

== Description ==

You can easily create a product search page from the affiliate services of Japan.

Simply by writing a short code to just one page, shopping malls will be created on your page.

Unfortunately, this plugin is only available in Japan.

[Readme page in Japanese.](http://www.csync.net/wp-plugin/cs-shop/cs-shop-readme/)

== Installation ==

1. Upload `cs-shop` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Select the setting of 'CS Shop' from the Admin menu, Please save to set the account information for the affiliate service.
1. Create a new page, Please write a short code `[csshop service="rakuten"]` to that page. (This is the case you select the Rakuten.)
1. Please visit the page that you created. Categories of the affiliate service that you set should be displayed.

This is the most simple configuration steps. Short code to a variety of attributes can be set. Please show 'Shortcode Example' page.

== Changelog ==

= 0.9.8 =
* Add support ValueCommerce.

= 0.9.7.1 =
* Remove the feature of LinkShare Crossover search and Merchandiser integration.

= 0.9.7 =
* Add support LinkShare.
* Remove "action" parameter from shortcode attributes.

= 0.9.6.1 =
* Remove debugging code.

= 0.9.6 =
* Add support Yahoo! Shopping API.

= 0.9.5.3 =
* Fixed control of the authority of the admin menu.

= 0.9.5.2 =
* Add show Review score, and refine some codes.

= 0.9.5.1 =
* Add display credit of Rakuten service.
* If there is no product image to view an alternate image.

= 0.9.5 =
* Add support Amazon Product Advertising API.

= 0.9.4 =
* Fixed bug that failed to download by the execution environment.
* Fixed a bug does not work on sites that do not have to configure the settings of Permalink.

= 0.9.3 =
* When the content is empty, Added a logic that does not cache.

= 0.9.2 =
* Add Cache feature using Cache_Lite.

= 0.9.1 =
* Add Timeout and Retry feature for service access.

= 0.9 =
* The first release version.

== Upgrade Notice ==

= 0.9.8 =
Support ValueCommerce.

= 0.9.7.1 =
Remove the feature of LinkShare Crossover search and Merchandiser integration.

= 0.9.7 =
Support LinkShare.

= 0.9.6.1 =
Re-release version 0.9.6.

= 0.9.6 =
Support Yahoo! Shopping.

= 0.9.5.3 =
Admin menu security update.

= 0.9.5.2 =
Add show Review score.

= 0.9.5.1 =
If you use the Rakuten, you must version up.

= 0.9.5 =
Support Amazon service.

= 0.9.4 =
Fixed lots of bugs.

= 0.9.3 =
This is a simple fix of source code.

= 0.9.2 =
Reduce the number of queries, and improve response time.

= 0.9.1 =
If you have problem which the search results is not displayed, which may be resolved.

= 0.9 =
The first release version.

== Shortcode Example ==

To view the results of a search by specifying the keyword 'foo' automatically.

`[csshop service="rakuten" keyword="foo"]`

`[csshop service="amazon" keyword="foo"]`

`[csshop service="yahoo" keyword="foo"]`

`[csshop service="linkshare" keyword="foo"]`

`[csshop service="valuecommerce" keyword="foo"]`

To view the results of a search by specifying the specified category automatically.

`[csshop service="rakuten" category="100026"]`

`[csshop service="amazon" category="Electronics"]`

`[csshop service="yahoo" category="2505"]`

`[csshop service="linkshare" category="Electronics"]`

`[csshop service="valuecommerce" category="electronics"]`
