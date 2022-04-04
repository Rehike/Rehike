<?php
class YtSigninCore {
	public $isSignedIn;
	public $sapisid = null;
	public $username = null;
	public $ucid = null;
	public $googBrandAccId = null;
	public $avatarUrl = null;
	private $root;
	private $testUrl = 'https://www.youtube.com/user/jawed/about?pbj=1';
	private $serverResponse = null;
	
	private $cacheExists;
	
	public static function getHttpHeaders() {
		return 0;
	}
	
	public static function getCookies() {
		return 0;
	}
	
	private $httpCookies;
	
	public function generateSapisidHash($origin) {
		return 0;
	}
	
	function getAvatarUrlFromResponse() {
		return 0;
	}
	
	function getBrandAccIdFromResponse() {
		return 0;
	}
	
	function getFromServer() {
		return 0;
	}
	
	function getCacheContents() {
		return 0;
	}
	
	function generateCache($update = false) {
		return 0;
	}
	
	function resolveState($ignoreCache = false) {
		return 0;
	}
	
	function __construct() {
		/*
		 * PHP is a weird language and this is the only way to assign a value
		 * to a variable in a class at construction.
		*/
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->cacheExists = file_exists($this->root . '/cache/sc2.json') ? true : false;
		$this->httpCookies = self::getCookies();
		
		
		if (isset($_COOKIE['SAPISID'])) {
			$this->isSignedIn = false;
			return 0; // Hacky premature return
		} else {
			$this->isSignedIn = false;
			return 0; // Hacky premature return
		}
	}
}