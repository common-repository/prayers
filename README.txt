=== Prayers ===
Contributors: kalebheitzman
Tags: prayer
Donate link: #
Requires at least: 4.3
Tested up to: 4.4
Stable tag: trunk
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

Lets an organization share, update, and manage prayer requests via their website. This plugin also provides JSON feeds for other services to consume and requires the WP REST API be installed and activated first.

== Description ==
Lets an organization share, update, and manage prayer requests via their website. 

Frontend (read anonymous) users can submit requests via an embeddable prayer form using the shortcode [prayer_form]. They can also click a "pray" button that lets the original poster know their request has been prayed for by someone else. Each request displays a count of how many times it has been prayed for. All requests can be categorized and tagged like a post.

Authorized users can track and manage prayer requests via a custom admin page. They can approve requests, mark them as answered as well as look at several other pieces of information stored with each request like geolocation data (displayable on a frontend map).

This plugin also provides JSON feeds for other services to consume and requires the WP REST API (https://wordpress.org/plugins/rest-api/) be installed and activated first. You can access the Feeds through the Prayer admin menu.

== Installation ==
Download echo into your /wp-content/plugins directory. Navigate to Plugins and activate Prayer. Upon activation, the plugin will create a new passwordless user named Prayer to associate frontend submissions with.

== Frequently Asked Questions ==
Q: Are there shortcodes available?
A: There are 3 shortcodes available.

[prayers limit="10" start_date="last month" end_date="today"]

[prayers_map px_height="320" px_width="500" pct_height="" pct_width=""]

[prayers_mailchimp_signup]

Q: How do I create a prayer listing?
A: Embed the [prayers] shortcode on any standard page.

Q: How do users submit prayer requests from my website?
A: Embed the [prayers_form] shortcode on any standard page.

Q: How do I display a prayer map?
A: Embed the [prayers_map] shortcode on any page. Full-width pages work well with the prayer map.

== Screenshots ==
1. Prayer Listing
2. Admin Prayer List
3. Settings

== Changelog ==
v.0.9.0 Initial Release

== Upgrade Notice ==
Initial Release