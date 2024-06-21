# SRV records

## Create

### Create from constructor
```php
$record = new SRV([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
echo 'getWeight = ' . $record->getWeight() . PHP_EOL;
echo 'getPort = ' . $record->getPort() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = srv.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SRV
getPriority = 1
getWeight = 9
getPort = 10
getTarget = 192.168.0.1
```

### Create with a setter
```php
$record = new SRV();
                $record->setData([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
echo 'getWeight = ' . $record->getWeight() . PHP_EOL;
echo 'getPort = ' . $record->getPort() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = srv.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SRV
getPriority = 1
getWeight = 9
getPort = 10
getTarget = 192.168.0.1
```

### Create from string
```php
$record = Record::fromString('srv.bluelibraries.com 3600 IN SRV 1 9 10 192.168.0.1');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
echo 'getWeight = ' . $record->getWeight() . PHP_EOL;
echo 'getPort = ' . $record->getPort() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = srv.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SRV
getPriority = 1
getWeight = 9
getPort = 10
getTarget = 192.168.0.1
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9,
	'type' => "SRV"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPriority = ' . $record->getPriority() . PHP_EOL;
echo 'getWeight = ' . $record->getWeight() . PHP_EOL;
echo 'getPort = ' . $record->getPort() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = srv.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SRV
getPriority = 1
getWeight = 9
getPort = 10
getTarget = 192.168.0.1
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('srv.bluelibraries.com', RecordTypes::SRV);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SRV Object
        (
            [data:protected] => Array
                (
                    [host] => srv.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 1
                    [port] => 10
                    [target] => 192.168.0.1
                    [weight] => 9
                    [type] => SRV
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('srv.bluelibraries.com', RecordTypes::SRV);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SRV Object
        (
            [data:protected] => Array
                (
                    [host] => srv.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 1
                    [port] => 10
                    [target] => 192.168.0.1
                    [weight] => 9
                    [type] => SRV
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

$records = $dns->get('srv.bluelibraries.com', RecordTypes::SRV);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SRV Object
        (
            [data:protected] => Array
                (
                    [host] => srv.bluelibraries.com
                    [ttl] => 3600
                    [pri] => 1
                    [port] => 10
                    [target] => 192.168.0.1
                    [weight] => 9
                    [type] => SRV
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new SRV([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "srv.bluelibraries.com 3600 IN SRV 1 9 10 192.168.0.1"
string2 = "srv.bluelibraries.com 3600 IN SRV 1 9 10 192.168.0.1"
```

### Transform to JSON
```php
$record = new SRV([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"srv.bluelibraries.com","ttl":3600,"pri":1,"port":10,"target":"192.168.0.1","weight":9,"class":"IN","type":"SRV"}
```

### Transform to Array
```php
$record = new SRV([
	'host' => "srv.bluelibraries.com",
	'ttl' => 3600,
	'pri' => 1,
	'port' => 10,
	'target' => "192.168.0.1",
	'weight' => 9
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => srv.bluelibraries.com
    [ttl] => 3600
    [pri] => 1
    [port] => 10
    [target] => 192.168.0.1
    [weight] => 9
    [class] => IN
    [type] => SRV
)
```
