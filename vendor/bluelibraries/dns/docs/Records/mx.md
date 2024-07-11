# MX records

## Create

### Create from constructor
```php
$record = new MX([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
```
```text
getHost = mx.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MX
getTarget = 192.168.0.1
getPriority = 10
```

### Create with a setter
```php
$record = new MX();
                $record->setData([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
```
```text
getHost = mx.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MX
getTarget = 192.168.0.1
getPriority = 10
```

### Create from string
```php
$record = Record::fromString('mx.bluelibraries.com 3600 IN MX 10 192.168.0.1');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
```
```text
getHost = mx.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MX
getTarget = 192.168.0.1
getPriority = 10
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1",
	'type' => "MX"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
```
```text
getHost = mx.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MX
getTarget = 192.168.0.1
getPriority = 10
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('mx.bluelibraries.com', RecordTypes::MX);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\MX Object
        (
            [data:protected] => Array
                (
                    [host] => mx.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 10
                    [target] => 192.168.0.1
                    [type] => MX
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('mx.bluelibraries.com', RecordTypes::MX);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\MX Object
        (
            [data:protected] => Array
                (
                    [host] => mx.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 10
                    [target] => 192.168.0.1
                    [type] => MX
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

$records = $dns->get('mx.bluelibraries.com', RecordTypes::MX);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\MX Object
        (
            [data:protected] => Array
                (
                    [host] => mx.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 10
                    [target] => 192.168.0.1
                    [type] => MX
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new MX([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "mx.bluelibraries.com 3600 IN MX 10 192.168.0.1"
string2 = "mx.bluelibraries.com 3600 IN MX 10 192.168.0.1"
```

### Transform to JSON
```php
$record = new MX([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"mx.bluelibraries.com","ttl":3600,"pri":10,"target":"192.168.0.1","class":"IN","type":"MX"}
```

### Transform to Array
```php
$record = new MX([
	'host' => "mx.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 10,
	'target' => "192.168.0.1"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => mx.bluelibraries.com
    [ttl] => 3600
    [pri] => 10
    [target] => 192.168.0.1
    [class] => IN
    [type] => MX
)
```
