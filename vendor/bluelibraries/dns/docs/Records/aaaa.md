# Address (IP v6) records

## Create

### Create from constructor
```php
$record = new AAAA([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIPV6 = ' . $record->getIPV6() . PHP_EOL;
```
```text
getHost = aaaa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = AAAA
getIPV6 = ::ffff:1451:6f55
```

### Create with a setter
```php
$record = new AAAA();
                $record->setData([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIPV6 = ' . $record->getIPV6() . PHP_EOL;
```
```text
getHost = aaaa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = AAAA
getIPV6 = ::ffff:1451:6f55
```

### Create from string
```php
$record = Record::fromString('aaaa.test.bluelibraries.com 3600 IN AAAA ::ffff:1451:6f55');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIPV6 = ' . $record->getIPV6() . PHP_EOL;
```
```text
getHost = aaaa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = AAAA
getIPV6 = ::ffff:1451:6f55
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55",
	'type' => "AAAA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIPV6 = ' . $record->getIPV6() . PHP_EOL;
```
```text
getHost = aaaa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = AAAA
getIPV6 = ::ffff:1451:6f55
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('aaaa.test.bluelibraries.com', RecordTypes::AAAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\AAAA Object
        (
            [data:protected] => Array
                (
                    [host] => aaaa.test.bluelibraries.com
                    [ttl] => 3600
                    [ipv6] => ::ffff:1451:6f55
                    [type] => AAAA
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('aaaa.test.bluelibraries.com', RecordTypes::AAAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\AAAA Object
        (
            [data:protected] => Array
                (
                    [host] => aaaa.test.bluelibraries.com
                    [ttl] => 3600
                    [ipv6] => ::ffff:1451:6f55
                    [type] => AAAA
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

$records = $dns->get('aaaa.test.bluelibraries.com', RecordTypes::AAAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\AAAA Object
        (
            [data:protected] => Array
                (
                    [host] => aaaa.test.bluelibraries.com
                    [ttl] => 3600
                    [ipv6] => ::ffff:1451:6f55
                    [type] => AAAA
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new AAAA([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "aaaa.test.bluelibraries.com 3600 IN AAAA ::ffff:1451:6f55"
string2 = "aaaa.test.bluelibraries.com 3600 IN AAAA ::ffff:1451:6f55"
```

### Transform to JSON
```php
$record = new AAAA([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"aaaa.test.bluelibraries.com","ttl":3600,"ipv6":"::ffff:1451:6f55","class":"IN","type":"AAAA"}
```

### Transform to Array
```php
$record = new AAAA([
	'host' => "aaaa.test.bluelibraries.com",
	'ttl' => 3600,
	'ipv6' => "::ffff:1451:6f55"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => aaaa.test.bluelibraries.com
    [ttl] => 3600
    [ipv6] => ::ffff:1451:6f55
    [class] => IN
    [type] => AAAA
)
```
