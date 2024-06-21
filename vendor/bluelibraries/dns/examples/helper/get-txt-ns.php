<?php

use BlueLibraries\Dns\Facade\DNS;
use BlueLibraries\Dns\Records\RecordTypes;

// Get TXT and NS records using TCP/IP
$allRecords = DNS::getRecords('test.com', [RecordTypes::TXT, RecordTypes::NS]);
echo '<pre>' . print_r($allRecords, true) . '</pre>';
