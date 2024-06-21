<?php

use BlueLibraries\Dns\Facade\DNS;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Records\RecordTypes;

// Retrieve all records using TCP/IP
$allRecords1 = DNS::getRecords('test.com', RecordTypes::ALL);
echo '<pre>' . print_r($allRecords1, true) . '</pre>';

// Retrieve all records using DIG
$allRecords2 = DNS::getRecords('test.com', RecordTypes::ALL, DnsHandlerTypes::DIG);
echo '<pre>' . print_r($allRecords2, true) . '</pre>';

// Retrieve all records using PHP function `dns_get_record`
$allRecords3 = DNS::getRecords('test.com', RecordTypes::ALL, DnsHandlerTypes::DNS_GET_RECORD);
echo '<pre>' . print_r($allRecords3, true) . '</pre>';

