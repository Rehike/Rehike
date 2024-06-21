# CNAME (canonical name) records

## Create

### Create from constructor
```php
$record = new CNAME([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CNAME
getTarget = target.bluelibraries.com
```

### Create with a setter
```php
$record = new CNAME();
                $record->setData([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CNAME
getTarget = target.bluelibraries.com
```

### Create from string
```php
$record = Record::fromString('caa.test.bluelibraries.com 3600 IN CNAME target.bluelibraries.com');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CNAME
getTarget = target.bluelibraries.com
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com",
	'type' => "CNAME"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTarget = ' . $record->getTarget() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CNAME
getTarget = target.bluelibraries.com
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('caa.test.bluelibraries.com', RecordTypes::CNAME);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CNAME Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [target] => target.bluelibraries.com
                    [type] => CNAME
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('caa.test.bluelibraries.com', RecordTypes::CNAME);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CNAME Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [target] => target.bluelibraries.com
                    [type] => CNAME
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

$records = $dns->get('caa.test.bluelibraries.com', RecordTypes::CNAME);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CNAME Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [target] => target.bluelibraries.com
                    [type] => CNAME
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new CNAME([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "caa.test.bluelibraries.com 3600 IN CNAME target.bluelibraries.com"
string2 = "caa.test.bluelibraries.com 3600 IN CNAME target.bluelibraries.com"
```

### Transform to JSON
```php
$record = new CNAME([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"caa.test.bluelibraries.com","ttl":3600,"target":"target.bluelibraries.com","class":"IN","type":"CNAME"}
```

### Transform to Array
```php
$record = new CNAME([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'target' => "target.bluelibraries.com"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => caa.test.bluelibraries.com
    [ttl] => 3600
    [target] => target.bluelibraries.com
    [class] => IN
    [type] => CNAME
)
```
