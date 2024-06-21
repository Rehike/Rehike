<?php

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Facade\DNS;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;


// get all TXT using TCP/IP
$allRecords1 = DNS::getRecords('test.com', RecordTypes::TXT);
echo '<h1>Get all TXT with TCP/IP</h1>';
echo '<pre>' . print_r($allRecords1, true) . '</pre>';

//Let's display first TXT value
echo '<h1>Get record TXT value</h1>';
echo 'Text=' . $allRecords1[0]->getTxt();


// Let's use classic object
$allRecords2 = (new DnsRecords())
    ->get('test.com', RecordTypes::TXT);
echo '<h1>Get all TXT with TCP/IP by using classic object</h1>';
echo '<pre>' . print_r($allRecords2, true) . '</pre>';


// Let's use classic object - with custom settings
$allRecords3 = (new DnsRecords(
    (new TCP())
    ->setNameserver('8.8.8.8')
    ->setTimeout(3)
    ->setRetries(2)
))
    ->get('test.com', RecordTypes::TXT);
echo '<h1>Get all TXT with TCP/IP by using classic object and custom settings</h1>';
echo '<pre>' . print_r($allRecords3, true) . '</pre>';
