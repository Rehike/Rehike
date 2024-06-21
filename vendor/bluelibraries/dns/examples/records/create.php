<?php

use BlueLibraries\Dns\Facade\Record;
use BlueLibraries\Dns\Records\Types\TXT;

$record = new TXT([
    'host' => 'bluelibraries.com',
    'ttl'  => 3600,
    'txt'  => 'test value'
]);
echo '<h1>TXT record - data from constructor</h1>';
echo '<pre>' . var_dump($record) . '</pre>';


$record = new TXT();
$record->setData(
    [
        'host' => 'bluelibraries.com',
        'ttl'  => 3600,
        'txt'  => 'test value'
    ]
);
echo '<h1>TXT record - data from setter</h1>';
echo '<pre>' . var_dump($record) . '</pre>';


$record = Record::fromString('bluelibraries.com 3600 IN TXT "test value"');
echo '<h1>TXT record - data from string</h1>';
echo '<pre>' . var_dump($record) . '</pre>';


$record = Record::fromNormalizedArray(
    [
        'type' => 'TXT',
        'host' => 'bluelibraries.com',
        'ttl'  => 3600,
        'txt'  => 'test value',
    ]
);
echo '<h1>TXT record - data from normalized array</h1>';
echo '<pre>' . var_dump($record) . '</pre>';
