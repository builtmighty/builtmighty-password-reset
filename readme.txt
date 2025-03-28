=== Built Mighty Password Reset ===
Contributors: tylerjohnsondesign, radams-builtmighty
Donate link: https://builtmighty.com
Tags: passwords, users, reset
Requires at least: 6.0
Tested up to: 10
Stable tag: 6.4.3
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Our password reset plugin gives site administrators better tools around requiring users to reset their passwords.

== Description ==

This plugin includes both a timed password reset and a bulk password reset. The timed reset requires users to update their password every x amount of days. The bulk reset, however, requires users of a certain user level to reset their password, via a link sent to their email, on login and does not allow them to login until they've done so.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= 1.4.0 =
* Added Block UI for password reset form on submit.
* Fixed Bug where Password would save wrong on password reset.

= 1.3.0 =
* Update Key creation and validation to use WP Form Reset Keys.
* Update Forms to use WC Password Reset Form if template found.
* Fixed bug where Sanitized characters would remain in output for notices.

= 1.2.1 =
* Validate condition check for password reset page in enqueue method

= 1.2.0 =
* Update the password reset exception to apply the same rule to passwords created by admins in the admin users panel 

= 1.1.0 =
* Add Exclusion Interval.

= 1.0.0 =
* Initial launch
