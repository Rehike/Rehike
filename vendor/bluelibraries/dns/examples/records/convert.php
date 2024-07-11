<?php

use BlueLibraries\Dns\Records\Types\TXT;

$record = new TXT([
    'host' => 'bluelibraries.com',
    'ttl'  => 3600,
    'txt'  => 'test value'
]);

echo '<h1>Convert TXT record to array</h1>';
echo '<pre>' . print_r($record->toArray(), true) . '</pre>';


echo '<h1>Convert TXT record to string</h1>';
echo '<pre>' . print_r($record->toString(), true) . '</pre>';


echo '<h1>Convert TXT record to string by using cast</h1>';
echo '<pre>' . print_r((string)$record, true) . '</pre>';


echo '<h1>Convert TXT record to json by using cast</h1>';
echo '<pre>' . print_r(json_encode($record), true) . '</pre>';
