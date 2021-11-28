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
		$headers = getallheaders();
		foreach($headers as $key=>$val) {
			return $key . ': ' . $val . '';
		}
	}
	
	public static function getCookies() {
		if (isset($_SERVER['COOKIE'])) {
			return $_SERVER['COOKIE'];
		} else {
			return []; // Hopefully avoids type issues to return an empty array.
		}
	}
	
	private $httpCookies;
	
	public function generateSapisidHash($origin) {
		$sapisid = $this->sapisid;
		$curtime = time();
		$hash = sha1($curname . ' ' . $sapisid . ' ' . $origin);
		return "SAPISIDHASH {$curtime}_{$hash}";
	}
	
	function getAvatarUrlFromResponse() {
		return $this->serverResponse[1]->response->topbar->desktopTopbarRenderer->
			topbarButtons[3]->topbarMenuRenderer->avatar->thumbnails[0]->url;
	}
	
	function getBrandAccIdFromResponse() {
		/* 
		 *	TODO: This can probably be cleaned up significantly.
		 *	Look into datasyncId more and find a less specific way to
		 *	retrieve it.
		*/
		$datasyncId = $this->serverResponse[1]->response->responseContext->
			mainAppWebResponseContext->datasyncId;
		if (strlen($datasyncId) == 44) {
			return substr($datasyncId, 0, 21);
		} else {
			return null; // We use the master account.
		}
	}
	
	function getFromServer() {
		$ch = curl_init($this->testUrl);
		$cookies = $this->httpCookies;
		
		curl_setopt_array($ch, array(
			CURLOPT_HTTPHEADER=>array(
				"Cookie: {$cookies}"
			),
			CURLOPT_POST=>true,
			CURLOPT_RETURNTRANSFER=>true
		));
		
		$response = curl_exec($ch);
      curl_close($ch);
		return $response;
	}
	
	function getCacheContents() {
		$cache = file_get_contents($this->root . '/cache/sc2.json');
		try {
			$json = json_decode($cache);
		} catch(Exception $e) {
			return 'ERR_INVALID_CACHE';
		}
		return $json;
	}
	
	function generateCache($update = false) {
		if ($update) {
			$curcache = $this->getCacheContents();
		}
		if ($this->cacheExists) {
			unlink($root . '/cache/sc2.json');
		}
		$newCacheFile = fopen($root . '/cache/sc2.json', 'w');
		$encryptedAccountId = crypt($this->sapisid);
		if (!$update) {
			$cacheBody = (object) [];
		} else {
			$cacheBody = $curcache;
		}
		$cacheBody->expire = time() + 2678400;
		$cacheBody->{$encryptedAccountId} = (object) [
			'username' => $this->username,
			'ucid' => $this->ucid,
			'googBrandAccId' => $this->googBrandAccId,
			'avatarUrl' => $this->avatarUrl
		];
		fwrite($newCacheFile, json_encode($cacheBody));
		fclose($newCacheFile);
	}
	
	function resolveState($ignoreCache = false) {
		$useCache = false;
		if (!$ignoreCache) {
			if ($this->cacheExists) {
				$cache = $this->getCacheContents();
				if ($cache !== 'ERR_INVALID_CACHE') {
					$cacheKey = crypt($this->sapisid); 
					if (isset($cache->{$cacheKey})) {
						$cacheRoot = $cache->{$cacheKey};
						$useCache = true;
					}
				}
			}
		}
		if ($useCache) {
			$testfor = explode(" ", "username ucid googBrandAccId avatarUrl");
			for ($i = 0, $j = count($testfor); $i < $j; $i++) {
				try {
					$this->{$testfor[$i]} = $cacheRoot->{$testfor[$i]};
				} catch (Exception $e) {
					return $this->resolveState(true);
				}
			}
		} else {
			$this->getFromServer();
		}
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
			$this->sapisid = $_COOKIE['SAPISID'];
			$this->resolveState();
		} else {
			$this->isSignedIn = false;
			return 0; // Hacky premature return
		}
	}
}