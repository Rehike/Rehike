# NS records

## Create

### Create from constructor
```php
$record = new NS([
	'host' => "ns.bluelibraries.com",
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
getHost = ns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NS
getTarget = 192.168.0.1
```

### Create with a setter
```php
$record = new NS();
                $record->setData([
	'host' => "ns.bluelibraries.com",
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
getHost = ns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NS
getTarget = 192.168.0.1
```

### Create from string
```php
$record = Record::fromString('ns.bluelibraries.com 3600 IN NS 192.168.0.1');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NS
getTarget = 192.168.0.1
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "ns.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1",
	'type' => "NS"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = ns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NS
getTarget = 192.168.0.1
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('ns.bluelibraries.com', RecordTypes::NS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => ns.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => NS
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('ns.bluelibraries.com', RecordTypes::NS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => ns.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => NS
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

$records = $dns->get('ns.bluelibraries.com', RecordTypes::NS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => ns.bluelibraries.com
                    [ttl] => 3600
                    [target] => 192.168.0.1
                    [type] => NS
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new NS([
	'host' => "ns.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "ns.bluelibraries.com 3600 IN NS 192.168.0.1"
string2 = "ns.bluelibraries.com 3600 IN NS 192.168.0.1"
```

### Transform to JSON
```php
$record = new NS([
	'host' => "ns.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"ns.bluelibraries.com","ttl":3600,"target":"192.168.0.1","class":"IN","type":"NS"}
```

### Transform to Array
```php
$record = new NS([
	'host' => "ns.bluelibraries.com",
	'ttl' => 3600,
	'target' => "192.168.0.1"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => ns.bluelibraries.com
    [ttl] => 3600
    [target] => 192.168.0.1
    [class] => IN
    [type] => NS
)
```
