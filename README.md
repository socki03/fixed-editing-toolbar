# WP Fixed Editing Toolbar
Contributors: socki03    
Tags: fixed, toolbar, submit, editing, post, page    
Requires at least: 4.0    
Tested up to: 4.1.1    
License: GPLv2 or later    
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Affix the submit box as a toolbar to make it easier to update posts, pages and CPT's.

# Installation
1. Upload `fixed-editing-toolbar` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress

# Changelog
## 0.3
1. Made a couple significant modifications to the CSS, including left & right positioning and all the subsequent changes in that fashion.
2. Added an 'Fixed Editing Toolbar' page under Settings to change the fixed toolbar to one of four positions (top is default).
	A. Also, all default colors schemes work on the settings page.
3. Adjusted and removed defined variables, as they wouldn't work with relative root URLs.  Found this while testing using Vagrant Share.
4. Removed old CSS files (the ones I used before CSS flex).
5. Adjusted CSS to media query only of 783px and above;
	A. Started working on tablet media query, but did not finish yet.

## 0.2
1. Included a custom version of the Wordpress submit-div meta box
2. Changed structure to flexbox.
3. Modified the styling so post changes (post date, status, & visibility) to not be quite as intrusive.

## 0.1
1. Initial build based on the Wordpress submit-div meta box
