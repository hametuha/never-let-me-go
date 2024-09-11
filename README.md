# Never Let Me Go

Contributors: Takahashi_Fumiki,hametuha  
Tags: delete, account  
Requires at least: 5.9  
Requires PHP: 7.2  
Tested up to: 6.6  
Stable tag: nightly

If someone wants to leave your WordPress, let them go.

<!-- only:github/ -->
[![Build Status](https://travis-ci.org/fumikito/Never-Let-Me-Go.svg)](https://travis-ci.org/fumikito/Never-Let-Me-Go)
<!-- /only:github -->

## Description

WordPress can allow your user to register by themself, but has no function to delete their own account.

You can also create **Resign Page** which displays message to your customer.

> Are you sure to delete your account? We miss you.

## Installation

Search "Never Let Me Go" in admin screen.
Altenatively, you can install it manually like below:

1. Upload `never-let-me-go` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In admin panel, Go to Resgin Setting and set up with instractions displayed.
4. Create **Resigning Page** and write some message to your user. That's it.

## Frequently Asked Questions

### How To Contribute

We host plugin on [github](https://github.com/fumikito/Never-Let-Me-Go) and any issues and pull requests are welcomed!
See [Contributing.md](https://github.com/hametuha/never-let-me-go/blob/master/Contributing.md) for more details.

### Does this plugin is ready for Gutenberg?

Maybe yes. If you find bugs, please [let us know](https://github.com/fumikito/Never-Let-Me-Go).

### Does this plugin is ready for block editor?

Partially yes. Because "Resign Page" can be split via <code>Page Break</code> block. We have plan to make **Resign Button** block and more flexible laytout availability. Please send your request.

## Screenshots

1. Set up your resign option.

## Changelog

### 1.2.2

* Bugfix: Ensure user cache will be cleared.

### 1.2.1

* Bugfix: clear user cache when user data has been hashed.

### 1.2.0

* **Notice** PHP requirements is now PHP 5.6 and over.
* Add consent checkbox option.
* User meta can be filtered by allow list. If you want to keep some of user's data, customize allow list.

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

### 1.2.0

Since 1.2.0, PHP 5.6 and over is required.

### 1.1.0

Since 1.1.0, PHP 5.4 and over is required.

### 0.9.0

Since 0.9.0, PHP 5.3 and over is required. If your WordPress is running on PHP 5.2, stay outdated or update your PHP. 
0.8.2 is the latest version which supports PHP 5.2.

### 0.8

Nothing.
