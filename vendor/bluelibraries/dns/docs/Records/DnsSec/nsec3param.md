# NSEC3 Param records

## Create

### Create from constructor
```php
$record = new NSEC3PARAM([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getIterations = ' . $record->getIterations() . PHP_EOL;
echo 'getSalt = ' . $record->getSalt() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC3PARAM
getAlgorithm = 12
getFlags = 255
getIterations = 3
getSalt = salt==
```

### Create with a setter
```php
$record = new NSEC3PARAM();
                $record->setData([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getIterations = ' . $record->getIterations() . PHP_EOL;
echo 'getSalt = ' . $record->getSalt() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC3PARAM
getAlgorithm = 12
getFlags = 255
getIterations = 3
getSalt = salt==
```

### Create from string
```php
$record = Record::fromString('bluelibraries.com 3600 IN NSEC3PARAM 12 255 3 salt==');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getIterations = ' . $record->getIterations() . PHP_EOL;
echo 'getSalt = ' . $record->getSalt() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC3PARAM
getAlgorithm = 12
getFlags = 255
getIterations = 3
getSalt = salt==
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt==",
	'type' => "NSEC3PARAM"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getIterations = ' . $record->getIterations() . PHP_EOL;
echo 'getSalt = ' . $record->getSalt() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC3PARAM
getAlgorithm = 12
getFlags = 255
getIterations = 3
getSalt = salt==
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::NSEC3PARAM);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [value] => nsec3param-value
                    [algorithm] => 12
                    [flags] => 255
                    [iterations] => 3
                    [salt] => salt==
                    [type] => NSEC3PARAM
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('bluelibraries.com', RecordTypes::NSEC3PARAM);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [value] => nsec3param-value
                    [algorithm] => 12
                    [flags] => 255
                    [iterations] => 3
                    [salt] => salt==
                    [type] => NSEC3PARAM
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

$records = $dns->get('bluelibraries.com', RecordTypes::NSEC3PARAM);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [value] => nsec3param-value
                    [algorithm] => 12
                    [flags] => 255
                    [iterations] => 3
                    [salt] => salt==
                    [type] => NSEC3PARAM
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new NSEC3PARAM([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt=="
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "bluelibraries.com 3600 IN NSEC3PARAM 12 255 3 salt=="
string2 = "bluelibraries.com 3600 IN NSEC3PARAM 12 255 3 salt=="
```

### Transform to JSON
```php
$record = new NSEC3PARAM([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt=="
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"bluelibraries.com","ttl":3600,"value":"nsec3param-value","algorithm":12,"flags":255,"iterations":3,"salt":"salt==","class":"IN","type":"NSEC3PARAM"}
```

### Transform to Array
```php
$record = new NSEC3PARAM([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'value' => "nsec3param-value",
	'algorithm' => 12,
	'flags' => 255,
	'iterations' => 3,
	'salt' => "salt=="
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => bluelibraries.com
    [ttl] => 3600
    [value] => nsec3param-value
    [algorithm] => 12
    [flags] => 255
    [iterations] => 3
    [salt] => salt==
    [class] => IN
    [type] => NSEC3PARAM
)
```
