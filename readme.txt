=== API Log Pro ===
Contributors: hubbardlabs, bhubbard
Donate link: https://hubbardlabs.com
Tags: wp rest api, rest api, wp api, api, json, json api, logging, api-log-pro
Requires at least: 4.6
Tested up to: 6.0
Stable tag: 1.0.0
Requires PHP: 7.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple plugin to log WordPress Rest API Requests.

== Description ==

This plugin enables logging of all calls to the WordPress REST API. You can view all logs from the WordPress Admin under **API Log Pro**.

== Installation ==

1. Copy the `api-log-pro` folder into your `wp-content/plugins` folder
2. Activate the `API Log Pro` plugin via the plugin admin page


== Frequently Asked Questions ==

= What is the difference between outgoing and incoming logs? =

Incoming are requests made to the WordPress Reset API, while outbound requests are made to 3rd party API services using the wp_remote_request() function.

= How long are logs kept? =

Currently logs are kept for 15 days.

= Can I view the log via the api? =

Yes, you can use the WordPress api to view the logs if you have `manage options` permissions as a WordPress User. Here is the endpoint:

`/wp-json/api-log-pro/v1/logs`


== Changelog ==

= 0.0.1 =
* First Release, please read CHANGELOG.md for all changes.

== Upgrade Notice ==

Upgrade notices will be here.

== Screenshots ==

Screenshots coming soon.

== WP-CLI Support ==

This plugin offers some basic wp-cli support. You can use the following command to delete all the logs in the db.

`wp api-log-pro delete`
