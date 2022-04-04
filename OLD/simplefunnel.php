<?php
$host = 'https://www.youtube.com/'; // leading / is IMPORTANT!
$hostvalue = 'www.youtube.com';

// Resolve the request method:
switch($_SERVER['REQUEST_METHOD']) {
	case 'GET': $reqmethod = 'GET'; break;
	case 'POST': $reqmethod = 'POST'; break;
}

// Retrieve POST fields (if post):
if ($reqmethod == 'POST') {
	$postfields = file_get_contents('php://input');
} else {
	$postfields = '';
}
// Passthrough all request headers_list:
$httpheader = array();
$httpheader[] = 'Host: '.$hostvalue;
foreach (getallheaders() as $name => $value) {
	if (!in_array($name, ['Accept','Accept-Encoding','Host'], true)) {
		$httpheader[] = $name.': '.$value;
	}
}

if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$ua = $_SERVER['HTTP_USER_AGENT'];
} else {
	$ua = '';
}

$curlarray = array(
	CURLOPT_CUSTOMREQUEST=>$reqmethod,
	CURLOPT_POSTFIELDS=>$postfields,
	CURLOPT_RETURNTRANSFER=>true,
	CURLOPT_HEADER=>0,
	CURLOPT_FOLLOWLOCATION=>1,
	CURLOPT_HTTPHEADER=>$httpheader,
	CURLOPT_USERAGENT=>$ua);

// echo json_encode($curlarray); //debug

$ch = curl_init($host.$_SERVER['REQUEST_URI']);
curl_setopt_array($ch, $curlarray,);
$response_body = curl_exec($ch);
$contenttype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

header('Content-Type: '.$contenttype);

echo $response_body;
curl_close($ch);