<?php
/**
 * @package never_let_me_go
 * @version 0.8
 */
/*
Plugin Name: Never Let Me Go
Plugin URI: http://hametuha.co.jp
Description: Allow user to self delete.
Author: Hametuha inc.
Version: 0.8
Author URI: http://hametuha.co.jp
*/

//ユーティリティクラスの読み込み
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."Hametuha_Library.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."Never_Let_Me_Go.php";

/**
 * @var $nlmg Never_Let_Me_Go
 */
$nlmg = new Never_Let_Me_Go(__FILE__, "0.8", "never-let-me-go");
