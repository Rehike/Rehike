<?php
require_once($root . '/utils/csiEmulator.php');
class YoutubeiUtils {
	private static function urlTransform($url, $isPbj) {
		if (strpos($url, 'https://www.youtube.com') === false) {
			$newurl = 'https://www.youtube.com';
		}
		if (substr($url, 0, 1) != '/') {
			$newurl .= '/';
		}
		if (!$isPbj) {
			$newurl .= 'youtubei/v1/';
		}
		$newurl .= $url;
		if ($strpos($url, '?') === false) {
			$argchar = '?';
		} else {
			$argchar = '&';
		}
		if (!$isPbj) {
			$newUrl .= $argchar . 'key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';
		} else {
			$newUrl .= $argchar . 'pbj=1';
		}
		return $newUrl;
	}
	private static function generateYoutubeiBody($body, $version) {
		CsiEmulator::emulate($version);
	}
	public static function request($url, $isPbj = false, $body = (object) [], $post = true) {
		$url = self::urlTransform($url, $isPbj);
		$ch = curl_init($url);
		$httpheaders = ['Content-Type: application/json'];
		if (!$post) {
			if (is_string($body)) {
				$httpheaders[] = $body;
			} else if (is_array($body)) {
				$httpheaders = array_merge($httpheaders, $body);
			}
		}
      
      $version = 2;
      if (isset($body->CLIENT)) {
         $version = $body->CLIENT;
         unset($body->CLIENT);
      }
      
		curl_setopt_array($ch, [
			CURLOPT_HTTPHEADER => $httpheaders,
			CURLOPT_POST => $post,
			CURLOPT_POSTFIELDS => '', // todo
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1
		]);
		if ($post) {
			$body = self::generateYoutubeiBody($body, $version);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
	}
	public static function getHp($version = 2, $isContinuation = false, $continuation = '') {
		if (!$isContinuation) {
			$data = self::request('/', true,);
		} else {
			$data = self::request('browse', false, (object) [
            'CLIENT' => $version,
				'browseId' => 'FEwhat_to_watch',
				'continuation' => $continuation
			]);
		}
		return $data;
	}
}