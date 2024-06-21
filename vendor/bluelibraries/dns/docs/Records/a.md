# Address records

## Create

### Create from constructor
```php
$record = new A([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIp = ' . $record->getIp() . PHP_EOL;
```
```text
getHost = a.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = A
getIp = 192.168.0.1
```

### Create with a setter
```php
$record = new A();
                $record->setData([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIp = ' . $record->getIp() . PHP_EOL;
```
```text
getHost = a.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = A
getIp = 192.168.0.1
```

### Create from string
```php
$record = Record::fromString('a.test.bluelibraries.com 3600 IN A 192.168.0.1');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIp = ' . $record->getIp() . PHP_EOL;
```
```text
getHost = a.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = A
getIp = 192.168.0.1
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1",
	'type' => "A"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getIp = ' . $record->getIp() . PHP_EOL;
```
```text
getHost = a.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = A
getIp = 192.168.0.1
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('a.test.bluelibraries.com', RecordTypes::A);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => a.test.bluelibraries.com
                    [ttl] => 3600
                    [ip] => 192.168.0.1
                    [type] => A
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('a.test.bluelibraries.com', RecordTypes::A);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => a.test.bluelibraries.com
                    [ttl] => 3600
                    [ip] => 192.168.0.1
                    [type] => A
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

$records = $dns->get('a.test.bluelibraries.com', RecordTypes::A);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => a.test.bluelibraries.com
                    [ttl] => 3600
                    [ip] => 192.168.0.1
                    [type] => A
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new A([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "a.test.bluelibraries.com 3600 IN A 192.168.0.1"
string2 = "a.test.bluelibraries.com 3600 IN A 192.168.0.1"
```

### Transform to JSON
```php
$record = new A([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"a.test.bluelibraries.com","ttl":3600,"ip":"192.168.0.1","class":"IN","type":"A"}
```

### Transform to Array
```php
$record = new A([
	'host' => "a.test.bluelibraries.com",
	'ttl' => 3600,
	'ip' => "192.168.0.1"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => a.test.bluelibraries.com
    [ttl] => 3600
    [ip] => 192.168.0.1
    [class] => IN
    [type] => A
)
```
