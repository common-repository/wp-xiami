<?php
/*
Plugin Name: WP-Xiami
Plugin URI: http://mufeng.me/wp-xiami.html
Description: 虾米音乐同步 WP-Xiami for wordpress xiami music player
Version: 0.0.8
Author: Mufeng
Author URI: http://mufeng.me
*/
global $WPXM;

define('WP_XIAMI_VERSION', '0.0.8');
define('WP_XIAMI_URL', plugins_url('', __FILE__));
define('WP_XIAMI_PATH', dirname( __FILE__ ));
define('WP_XIAMI_AJAX_URL', admin_url() . "admin-ajax.php");

require WP_XIAMI_PATH . '/function.php';
require WP_XIAMI_PATH . '/class.xiami.php';

if(!isset($WPXM)){
	$WPXM = new xiami();
}

function wp_xiami(){
    global $WPXM;
    $WPXM->display();
}