=== Frontpage Manager ===
Contributors: kirilisa
Donate link: http://kirilisa.com/
Tags: front page, frontpage, limit, category limit, paragraph limit, word limit, character limit, limit posts
Requires at least: 2.8
Tested up to: 3.1
Stable tag: trunk

Lets you customize how your frontpage and/or main posts page appears in a number of ways: limiting by category/ies, number of posts, number of words/characters/paragraphs.

== Description ==
Frontpage Manager allows you to customize how the posts appear on your frontpage in several different ways. 

* you can have the posts drawn from all categories or a subset of your choosing 
* you can choose how many posts should display (this is distinct from WordPress' built-in Reading Settings which applies to all blog pages)
* you can have each post limited in length by number of characters, words, or paragraphs
* you can choose which HTML tags should be stripped from posts
* you can specify a read more link, etc. if you like 
* you can hide the category in the page title, if you wish

The plugin will attempt to make sure that any tags that are interrupted by the post-limiting feature are matched so as not to cause formatting issues.

== Installation ==
* Install directly from WordPress (go to Plugins -> Add New)
OR
* Install manually
1. Unzip the plugin files to the `/wp-content/plugins/frontpage-manager/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set your preferred settings through the Frontpage Manager section under WordPress' Settings menu

== Compatibility ==
I have tested this plugin from WordPress 2.8.

== Screenshots ==
1. See screenshot-1.png for a view of the Settings.

== Frequently Asked Questions ==
= Does this plugin also apply if you have a static page set as your frontpage rather than latest posts? =

Yes. There are settings in the back end such that you can apply all options to your main posts page even if you have a static page as the front page, and you can also apply the word/character/paragraph limitation to the page that is your static front page.

= How can I style how the read more link looks? =

The link is contained within a div that has been given the class 'fpm`_`readon'. Simply add .fpm`_`readon{} to your theme's style.css and put whatever CSS styling you want in it.

== Download ==
[Version 1.3](http://kirilisa.com/downloads/projects/wordpress/frontpage-manager_1.3.zip "Download version 1.3")

== Changelog ==
= 0.9 beta -- December 2 2009 = 
* Plugin launched

= 0.91 beta -- December 4 2009 =
* Removed default from 'Read more linktext' so you don't have to have any if you don't want
* Allowed option 'all' for 'Tags to strip' for those who wish to get rid of all HTML markup
* Fixed small bug where 'Tags to strip' field was disappearing inappropriately

= 1.0 December 7 2009 =
* Implemented selection of multiple categories from which posts will be displayed
* Made it so 'Read more linktext' and 'Text ending' are not displayed when post is too short to require limiting

= 1.1 May 14 2010 =
* Fixed the PHP error that showed up when no category was selected, and put in a warning
* Made it so you can apply the word/character/paragraph limitation to a static front page
* Made it so you can apply both post category/number limitation and word/character/paragraph limitation to main posts page, when it isn't the front page (i.e. you have a static front page)
* Made it so that the post category is not displayed in the title of the homepage

= 1.2 June 6 2010 =
* Bug fix: Made the hide category in page title a togglable option in the backend since it doesn't always seem to work :-P This is just a quick workaround until I have the time to devote to figuring out why my method of hiding the category in page title, doesn't always work (sigh!)

= 1.3 February 25 2011 =
* Bug fix: The existing method of limiting by category was not working with WordPress 3.1 so I added in another method that should.
* Bug fix: Made a small change having to do with hiding the category in the page title.
