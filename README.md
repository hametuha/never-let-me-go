# Never Let Me Go

Contributors: Takahashi_Fumiki,hametuha  
Tags: delete, account  
Requires at least: 4.7  
Requires PHP: 5.4  
Tested up to: 5.1.1  
Stable tag: 1.1.0

If someone wants to leave your WordPress, let them go.

<!-- only:github/ -->
[![Build Status](https://travis-ci.org/fumikito/Never-Let-Me-Go.svg)](https://travis-ci.org/fumikito/Never-Let-Me-Go)
<!-- /only:github -->

## Description

WordPress can allow your user to register by himself, but has no function to self delete.

You can also create **Resign Page** which displays message to your customer.

> Are you sure to delete your account? We miss you.

## Installation

Search "Never Let Me Go" in admin screen.
Altenatively, you can install it manually like below:

1. Upload `never-let-me-go` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In admin panel, Go to Resgin Setting and set up with instractions displayed.
4. Create resigning page and write some message to your user. That's it.

## Frequently Asked Questions

### How To Contribute

We host plugin on [github](https://github.com/fumikito/Never-Let-Me-Go) and any issues and pull requests are welcomed!

### Does this plugin is ready for Gutenberg?

Maybe yes. If you find bugs, please [let us know](https://github.com/fumikito/Never-Let-Me-Go).

## Screenshots

1. Set up your resign option.

## Changelog

### 1.1.0

* **Notice** PHP requirements is now PHP 5.4 and over.
* Add WooCommerce compatibility.

### 1.0.4

* Avoid password change email to users.
* Administrator can't leave site by default.

### 1.0.3

* Automatic deploy.

### 1.0.0

* Add some hooks
* Change development environment.

### 0.9.0

* Bugfix.
* Code refactored and PHP 5.3 is required.

### 0.8.2

* Now you can assign deleted user's contents to specific user 

### 0.8.1

* Bugfix. thank-you message is now displayed.

### 0.8

* 1st release.

## Upgrade Notice

### 1.1.0

Since 1.1.0, PHP .4 and over is required.

### 0.9.0

Since 0.9.0, PHP 5.3 and over is required. If your WordPress is running on PHP 5.2, stay outdated or update your PHP. 
0.8.2 is the latest version which supports PHP 5.2.

### 0.8

Nothing.