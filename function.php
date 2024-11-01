<?php
global $fetchjs;

require WP_XIAMI_PATH . '/class.fetchjs.php';

if(!isset($fetchjs)){
	$fetchjs = new fetchjs();
}

add_action( 'wp_ajax_nopriv_wpxiami', 'wpxiami_callback' );
add_action( 'wp_ajax_wpxiami', 'wpxiami_callback' );
function wpxiami_callback() {
	global $fetchjs;

	$scope = $_GET['scope'];
	$user_id = $_GET['user_id'];

	switch ($scope) {
		case 'albums' :
			$result = array(
				'status' => 200,
				'msg' => $fetchjs->user_album($user_id)
			);
			break;

		case 'collects':
			$result = array(
				'status' =>  200,
				'msg' => $fetchjs->user_collect($user_id)
			);
			break;

		case 'all':
			$result = array(
				'status' =>  200,
				'msg' =>  $fetchjs->user_all($user_id)
			);
			break;						
		
		default:
			$result = array(
				'status' =>  400,
				'msg' =>  null
			);
	}

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}