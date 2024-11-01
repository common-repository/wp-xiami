<?php
class fetchjs{
	const API_URL_PREFIX = "http://www.xiami.com/app";
	const ALBUM_URL = "/iphone/album/id/";
	const COLLECT_URL = "/android/collect?id=";
	const USER_ALBUM_URL = "/android/lib-albums?uid=";
	const USER_PAGED_URL = "&page=";
	const USER_COLLECT_URL = "/android/lib-collects?uid=";
	const ALBUM_KEY_PREFIX = "/album/";
	const COLLECT_KEY_PREFIX = "/collect/";
	const USER_KEY_PREFIX = "/user/";

	public function __construct(){
	}

	public function album($album_id){
		$key = self::ALBUM_KEY_PREFIX . $album_id;
		$url = self::API_URL_PREFIX . self::ALBUM_URL . $album_id;

		$cache = $this->get_cache($key);
		if( $cache ) return $cache;

		$response = $this->http($url); 

		if(  $response["status"]=="ok" && $response["album"] ){
			$result = $response["album"];
			$count = count($result["songs"]);

			if(  $count < 1 ) return false;

			$album = array(
				"collect_id" => $result["album_id"],
				"collect_title" => $result["title"],
				"collect_author" => '',
				"collect_type" => "albums",
				"collect_cover" => $result["album_logo"],
				"collect_count" => $count
			);

			foreach($result["songs"] as $key => $value){
				$song_id = $value["song_id"];
				$album["songs"][] = array(
					"song_id" => $song_id,
					"song_title" => $value["name"],
					"song_length" => $value["length"],
					"song_src" => $value["location"],
					"song_author" => $value["singers"]
				);
				$album["collect_author"] = $value["singers"];
			}

			$this->set_cache($key, $album);
			return $album;
		}

		return false;	
	}

	public function collect($collect_id){
		$key = self::COLLECT_KEY_PREFIX . $collect_id;
		$url = self::API_URL_PREFIX . self::COLLECT_URL . $collect_id;

		$cache = $this->get_cache($key);
		if( $cache ) return $cache;

		$response = $this->http($url); 

		if(  $response["status"]=="ok" && $response["collect"] ){
			$result = $response["collect"];
			$count = count($result["songs"]);

			if(  $count < 1 ) return false;

			$collect = array(
				"collect_id" => $result["id"],
				"collect_title" => $result["name"],
				"collect_author" => $result["nick_name"],
				"collect_type" => "collects",
				"collect_cover" => $result["logo"],
				"collect_count" => $count
			);

			foreach($result["songs"] as $key => $value){
				$song_id = $value["song_id"];
				$collect["songs"][] = array(
					"song_id" => $song_id,
					"song_title" => $value["name"],
					"song_length" => 0,
					"song_src" => $value["location"],
					"song_author" => $value["singers"]
				);
			}
			$this->set_cache($key, $collect);
			return $collect;
		}

		return false;		
	}

	public function user_album($user_id, $paged=1){
		$key = self::USER_KEY_PREFIX . $user_id . self::ALBUM_KEY_PREFIX . $collect_id;
		$url = self::API_URL_PREFIX . self::USER_ALBUM_URL . $user_id . self::USER_PAGED_URL . $paged;

		$cache = $this->get_cache($key);

		if( !$cache ){
			$response = $this->http($url);

			if( $response && $response['albums'] ){
				$cache = $response['albums'];

				$this->set_cache($key, $cache);
			}
		}

		if( $cache ){
			$result = array();
			foreach ($cache as $key => $value) {
				$type = gettype($value);

				if( $type === "object" ){
					$album_id = $value->obj_id;
				}else if( $type === "array" ){
					$album_id = $value['obj_id'];
				}else{
					return false;
				}
				
				$_album = $this->album($album_id);

				if( $_album ) $result[] = $_album;
			}

			return $result;
		}

		return false;
	}

	public function user_collect($user_id, $paged=1){
		$key = self::USER_KEY_PREFIX . $user_id . self::COLLECT_KEY_PREFIX . $collect_id;
		$url = self::API_URL_PREFIX . self::USER_COLLECT_URL . $user_id . self::USER_PAGED_URL . $paged;

		$cache = $this->get_cache($key);

		if( !$cache ){
			$response = $this->http($url); 

			if( $response && $response['collects'] ){
				$cache = $response['collects'];

				$this->set_cache($key, $cache);
			}
		}

		if( $cache ){
			$result = array();
			foreach ($cache as $key => $value) {
				$type = gettype($value);

				if( $type === "object" ){
					$collect_id = $value->obj_id;
				}else if( $type === "array" ){
					$collect_id = $value['obj_id'];
				}else{
					return false;
				}

				$_collect = $this->collect($collect_id);

				if( $_collect ) $result[] = $_collect;
			}

			return $result;
		}

		return false;
	}

	public function user_all($user_id){
        $albums;
        $collects;
        $result = array();

        $album = $this->user_album($user_id);
        if( !$album ) $album = array();

        $collect = $this->user_collect($user_id);
        if( !$collect ) $collect = array();

        $result = array_merge($album, $collect);

        return $result;
	}

	private function http($url){
		$response =  wp_remote_get( $url );
		if ( !is_wp_error($response) && $response['response']['code'] == 200 ){
			$response = $response['body'];
			if ( !empty($response) ){
				return json_decode($response, true);
			}
		}

		return false;
	}

	public function get_cache($key){
		$cache = get_transient($key);
		return $cache === false ? false : json_decode($cache);
	}

	public function set_cache($key, $value){
		$value  = json_encode($value);
		set_transient($key, $value, 60*60*6);
	}

	public function clear_cache($key){
		delete_transient($key);
	}
}