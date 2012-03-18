<?php
/**
 * @package never_let_me_go
 * @version 0.8.1
 */
/*
Plugin Name: Never Let Me Go
Plugin URI: http://hametuha.co.jp
Author: Takahshi Fumiki
Version: 0.8.1
Author URI: http://takahashifumiki.com
Description: This Plugin allows your user to delete his/her own account. If you want, you can also display somehow painfull thank-you message on his resignation.
Text Domain: never-let-me-go
Domain Path: /language/
*/

//ユーティリティクラスの読み込み
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."Never_Let_Me_Go.php";

/**
 * @var $nlmg Never_Let_Me_Go
 */
$nlmg = new Never_Let_Me_Go(__FILE__, "0.8.1");
