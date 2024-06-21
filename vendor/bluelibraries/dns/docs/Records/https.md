# TYPE65 (HTTPS) records

## Create

### Create from constructor
```php
$record = new HTTPS([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getSeparator = ' . $record->getSeparator() . PHP_EOL;
echo 'getOriginalLength = ' . $record->getOriginalLength() . PHP_EOL;
echo 'getData = ' . $record->getData() . PHP_EOL;
```
```text
getHost = https.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TYPE65
getSeparator = \#
getOriginalLength = 27
getData = 1000C0268330568332D3239AA
```

### Create with a setter
```php
$record = new HTTPS();
                $record->setData([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getSeparator = ' . $record->getSeparator() . PHP_EOL;
echo 'getOriginalLength = ' . $record->getOriginalLength() . PHP_EOL;
echo 'getData = ' . $record->getData() . PHP_EOL;
```
```text
getHost = https.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TYPE65
getSeparator = \#
getOriginalLength = 27
getData = 1000C0268330568332D3239AA
```

### Create from string
```php
$record = Record::fromString('https.bluelibraries.com 3600 IN TYPE65 \# 27 1000C0268330568332D3239AA');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getSeparator = ' . $record->getSeparator() . PHP_EOL;
echo 'getOriginalLength = ' . $record->getOriginalLength() . PHP_EOL;
echo 'getData = ' . $record->getData() . PHP_EOL;
```
```text
getHost = https.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TYPE65
getSeparator = \#
getOriginalLength = 27
getData = 1000C0268330568332D3239AA
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA",
	'type' => "TYPE65"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getSeparator = ' . $record->getSeparator() . PHP_EOL;
echo 'getOriginalLength = ' . $record->getOriginalLength() . PHP_EOL;
echo 'getData = ' . $record->getData() . PHP_EOL;
```
```text
getHost = https.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TYPE65
getSeparator = \#
getOriginalLength = 27
getData = 1000C0268330568332D3239AA
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('https.bluelibraries.com', RecordTypes::HTTPS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HTTPS Object
        (
            [data:protected] => Array
                (
                    [host] => https.bluelibraries.com
                    [ttl] => 3600
                    [separator] => \#
                    [original-length] => 27
                    [data] => 1000C0268330568332D3239AA
                    [type] => TYPE65
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('https.bluelibraries.com', RecordTypes::HTTPS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HTTPS Object
        (
            [data:protected] => Array
                (
                    [host] => https.bluelibraries.com
                    [ttl] => 3600
                    [separator] => \#
                    [original-length] => 27
                    [data] => 1000C0268330568332D3239AA
                    [type] => TYPE65
                    [class] => IN
                )

        )

)
```

### Retrieve without helper, using custom handler settings
```php
$dnsHandler = new TCP();
$dnsHandler->setRetries(2);
$dnsHandler->setTimeout(3);
$dnsHandler->setNameserver('8.8.8.8');

$dns = new DnsRecords($dnsHandler);

$records = $dns->get('https.bluelibraries.com', RecordTypes::HTTPS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HTTPS Object
        (
            [data:protected] => Array
                (
                    [host] => https.bluelibraries.com
                    [ttl] => 3600
                    [separator] => \#
                    [original-length] => 27
                    [data] => 1000C0268330568332D3239AA
                    [type] => TYPE65
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new HTTPS([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "https.bluelibraries.com 3600 IN TYPE65 \\# 27 1000C0268330568332D3239AA"
string2 = "https.bluelibraries.com 3600 IN TYPE65 \\# 27 1000C0268330568332D3239AA"
```

### Transform to JSON
```php
$record = new HTTPS([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"https.bluelibraries.com","ttl":3600,"separator":"\\#","original-length":27,"data":"1000C0268330568332D3239AA","class":"IN","type":"TYPE65"}
```

### Transform to Array
```php
$record = new HTTPS([
	'host' => "https.bluelibraries.com",
	'ttl' => 3600,
	'separator' => "\#",
	'original-length' => 27,
	'data' => "1000C0268330568332D3239AA"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => https.bluelibraries.com
    [ttl] => 3600
    [separator] => \#
    [original-length] => 27
    [data] => 1000C0268330568332D3239AA
    [class] => IN
    [type] => TYPE65
)
```
