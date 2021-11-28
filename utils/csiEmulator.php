<?php
class CsiEmulator {
	private static function getHostPublicIp() {
		/*
		 * TODO (nightlinbit): This doesn't seem to be possible without connecting
		 * to an external domain? Still worth looking into I suppose.
		*/
		$checkDomains = [
			'ipinfo.io/ip',
			'myexternalip.com/raw',
			'ifconfig.me',
			'checkip.amazonaws.com',
			'api.ipify.org',
			// below are evil ipv6 (hopefully they don't cause issues)
			'wtfismyip.com/text',
			'ifconfig.co',
			'ip-adresim.app',
			'icanhazip.com'
		];
		for ($i = 0, $j = count($checkDomains); $i < $j; $i++) {
			$ch = curl_init($checkDomains[$i]);
			curl_setopt_array($ch, [
				CURLOPT_POST => 0,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1
			]);
			$response = curl_exec($ch);
			curl_close($ch);
			$valid = filter_var($response, FILTER_VALIDATE_IP);
			if ($valid) {
				return $response;
			}
		}
	}
	private static function resolveClient($client, $headers) {
    switch ($client) {
      case 1:
        $client = (object) [
          'name' => '1',
          'version' => '1'
        ];
        break;
      case 2:
        $client = (object) [
          'name' => '1',
          'version' => '2'
        ];
    }
		if ($headers) {
      return [
        'X-YouTube-Client-Name: ' . $client->name,
        'X-YouTube-Client-Version: ' . $client->version
      ];
    } else {
      return $client;
    }
	}
	public static function emulate($client, $visitorData = '', $hl = 'en', $gl = 'US') {
		$client = self::resolveClient($client, false);
		return (object) [
			'client' => (object) [
				'hl' => $hl,
				'gl' => $gl,
				'remoteHost' => self::getHostPublicIp(),
				'deviceMake' => '',
				'deviceModel' => '',
				'visitorData' => $visitorData,
				'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0,gzip(gfe)',
				'clientName' => $client->name,
				'clientVersion' => $client->version,
				'osName' => 'Windows',
				'osVersion' => '10.0',
				'originalUrl' => 'https://www.youtube.com',
				'platform' => 'DESKTOP',
				'clientFormFactor' => 'UNKNOWN_FORM_FACTOR',
				'timeZone' => 'America/Phoenix',
				'browserName' => 'Firefox',
				'browserVersion' => '89.0',
				'screenWidthPoints' => 1574,
				'screenHeightPoints' => 661,
				'screenPixelDensity' => 1,
				'screenDensityFloat' => 1,
				'utcOffsetMinutes' => -420,
				'userInterfaceTheme' => 'USER_INTERFACE_THEME_LIGHT',
				'mainAppWebInfo' => (object) [
					'graftUrl' => '/',
					'webDisplayMode' => 'WEB_DISPLAY_MODE_BROWSER',
					'isWebNativeShareAvailable' => 'false'
				],
				'user' => (object) [
					'lockedSafetyMode' => 'false'
				],
				'request' => (object) [
					'useSsl' => true,
					'internalExperimentFlags' => [],
					'consistencyTokenJars' => []
				],
				'clickTracking' => (object) [
					'clickTrackingParams' => 'CBkQsV4iEwiowZ6PqZfxAhWHRUwIHVTBCp0='
				]
			]
		];
	}
	
	public static function encryptVisitorData($visitor) {
		function toYtDateBin($unixDate) {
			$unixDate = floor($unixDate);
			if (strlen((string)$unixDate) > 10) {
				$diff = strlen((string)$unixDate) - 10;
				$unixDate = floor($unixDate / (10 ** $diff));
			}
			$bin = decbin($unixDate);
			//consolelog('[date: bin to base2] ' . $bin);
			// ugly byte padding hack
			$bin = substr("00000000000000000000000000000000", strlen($bin)) . $bin;
			//consolelog('[date: ugly byte padding hack] ' . $bin);
			$carry = substr($bin, 0, 4);
			//consolelog('[date: carry] ' . $carry);
			$bin = substr($bin, strlen($carry), strlen($bin));
			//consolelog('[date: bin - carry] ' . $bin);
			$bin = str_split($bin, 7);
			for ($i = 0, $j = count($bin); $i < $j; $i++) {
				$bin[$i] = '1' . $bin[$i];
			}
			$bin = implode('', $bin);
			$finalbin = '0000' . $carry . $bin;
			//consolelog('[date: finalbin] ' . $finalbin);
			$hex = dechex(bindec($finalbin));
			//consolelog('[date: tohex] ' . $hex);
			$hex = (strlen($hex) & 1) ? '0' . $hex : $hex;
			//consolelog('[date: pad hex] ' . $hex);
			$hex = str_split($hex, 2);
			//consolelog('[date: split hex by 2] ' . implode(',', $hex));
			$hex = array_reverse($hex);
			//consolelog('[date: reverse hex] ' . implode(',', $hex));
			$hex = implode('', $hex);
			return $hex;
		}
	
		function b16tob64($str) {
			$response = '';
			foreach (str_split($str, 2) as $pair) {
				$response .= chr(hexdec($pair));
			}
			return base64_encode($response);
		}
		
		$magic = '0a0b';
		$dateSeparator = '28';
		//consolelog('[magic, dateSeparator] ' . $magic . ', ' . $dateSeparator);
		$date = toYtDateBin(time());
		//consolelog('[date] ' . $date);
		$payload = bin2hex($visitor);
		//consolelog('[payload] ' . $payload);
		$hexout = strtolower($magic . $payload . $dateSeparator . $date);
		//consolelog('[hexout] ' . $hexout);
	
		$response = b16tob64($hexout);
		return rtrim(strtr($response, '+/', '-_'), '='); 
	}
}