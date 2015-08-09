=== Captcha on Login ===
Contributors: Anderson.Makiyama
Tags: capcha, login, plugin capcha, image on login, secure code to login
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: trunk

Protect your blog from login brute force attacks adding a captcha on login page of your site 

== Description ==

**Overview**

Nowadays, it is common hackers trying to get access to your blog, so, it is very important you make some actions to prevent your blog from being hacked. A good way to turn the login system of your blog more secure is to use that plugin Captcha on Login. It will Protect your blog from login brute force attacks adding a captcha on login page. That plugin also locks IPS after a specific number of login tries failed, note that on the plugin's options page you can set these numbers.

Currently, the plugin also allow us to change the default admin username from admin to whatever you want. Changing the username of the admin is a great idea to prevent brute force atacks and turn your blog more secure.

https://www.youtube.com/watch?v=4VCSiDpJvfQ

**Features**

* Limit the number of login attempts throught wp login form as well as auth cookies.

* Option to change admin username and grown up the security

* Report page, where you can see blocked ips and last 1000 sucess and failed logins. 

* Option to block IPs permanently

* Option to unblock IPs

* Option to define maximum number of login attempts befor locking the ip

* Some visual options, as background image, color of text, number of characters, etc.

**Sobre**

The plugin Captcha on Login was created by Anderson Makiyama of [Plugin Wordpress](http://plugin-wp.net)

 

== Installation ==

To install, just follow this steps:

1- Send the plugin to plugins folder, by default it should be: /wp-content/plugins/
2- Do login on your blog's panel, go to Plugins section and activate the plugin Captcha on Login
3- Now, on the left site of your blog's panel, a new option called "Captcha on Login" will appear, click on it to go to the options page
4- Set the options and save changes. 

== License ==

This file is part of this plugin.
This Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Publish Anonymous Posts. If not, see <http://www.gnu.org/licenses/>.

== Frequently Asked Questions ==

= May I suggest ideas and modification to this plugin? =
Yes, you can, just leave a comment at [Captcha on Login](http://plugin-wp.net/captcha-on-login)

== Screenshots ==
1. WP Login Page with "Captcha on Login" plugin enabled
2. Plugin's options page
3. The report page

== Changelog ==

= 0.1 =
* Plugin publication

= 1.0 =
* Added an option to unblock blocked ips
* Added an report page where you can see all blocked ips and the last 100 sucess or failed logins
* Added an option to change default admin username and increase the security of your blog

= 1.1 =
* Changed the report page from last 100 logins to last 1000 logins and fixed a little bug.

= 2.0 =
* The plugin started limiting also the number of login attempts using auth cookies
* The plugin allows you adding permanent blocked ips
* Added an report are for see all permanent blocked ips

= 2.1 =
* Now working even when GD is not installed on the server

= 2.1.1 =
* Now it is possible to change username of any user

= 2.1.2 =
* Changed some part of the code and icons.
