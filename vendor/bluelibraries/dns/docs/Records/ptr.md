# PTR records

## Create

### Create from constructor
```php
$record = new PTR([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = PTR
getTarget = 192.168.0.1
```

### Create with a setter
```php
$record = new PTR();
                $record->setData([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = PTR
getTarget = 192.168.0.1
```

### Create from string
```php
$record = Record::fromString('ptr.bluelibraries.com 3600 IN PTR 192.168.0.1');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = PTR
getTarget = 192.168.0.1
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1",
	'type' => "PTR"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = PTR
getTarget = 192.168.0.1
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('ptr.bluelibraries.com', RecordTypes::PTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\PTR Object
        (
            [data:protected] => Array
                (
                    [host] => ptr.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => PTR
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('ptr.bluelibraries.com', RecordTypes::PTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\PTR Object
        (
            [data:protected] => Array
                (
                    [host] => ptr.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => PTR
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

$records = $dns->get('ptr.bluelibraries.com', RecordTypes::PTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\PTR Object
        (
            [data:protected] => Array
                (
                    [host] => ptr.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => PTR
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new PTR([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "ptr.bluelibraries.com 3600 IN PTR 192.168.0.1"
string2 = "ptr.bluelibraries.com 3600 IN PTR 192.168.0.1"
```

### Transform to JSON
```php
$record = new PTR([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"ptr.bluelibraries.com","ttl":3600,"target":"192.168.0.1","class":"IN","type":"PTR"}
```

### Transform to Array
```php
$record = new PTR([
	'host' => "ptr.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => ptr.bluelibraries.com
    [ttl] => 3600
    [target] => 192.168.0.1
    [class] => IN
    [type] => PTR
)
```
