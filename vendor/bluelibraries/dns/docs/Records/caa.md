# Certification Authority Authorization records

## Create

### Create from constructor
```php
$record = new CAA([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CAA
getFlags = 1
getTag = tag-value
getValue = mult succes
```

### Create with a setter
```php
$record = new CAA();
                $record->setData([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CAA
getFlags = 1
getTag = tag-value
getValue = mult succes
```

### Create from string
```php
$record = Record::fromString('caa.test.bluelibraries.com 3600 IN CAA 1 tag-value mult succes');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CAA
getFlags = 1
getTag = tag-value
getValue = mult succes
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value",
	'type' => "CAA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
```
```text
getHost = caa.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CAA
getFlags = 1
getTag = tag-value
getValue = mult succes
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('caa.test.bluelibraries.com', RecordTypes::CAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CAA Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [value] => mult succes
                    [flags] => 1
                    [tag] => tag-value
                    [type] => CAA
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('caa.test.bluelibraries.com', RecordTypes::CAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CAA Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [value] => mult succes
                    [flags] => 1
                    [tag] => tag-value
                    [type] => CAA
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

$records = $dns->get('caa.test.bluelibraries.com', RecordTypes::CAA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\CAA Object
        (
            [data:protected] => Array
                (
                    [host] => caa.test.bluelibraries.com
                    [ttl] => 3600
                    [value] => mult succes
                    [flags] => 1
                    [tag] => tag-value
                    [type] => CAA
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new CAA([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "caa.test.bluelibraries.com 3600 IN CAA 1 tag-value mult succes"
string2 = "caa.test.bluelibraries.com 3600 IN CAA 1 tag-value mult succes"
```

### Transform to JSON
```php
$record = new CAA([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"caa.test.bluelibraries.com","ttl":3600,"value":"mult succes","flags":1,"tag":"tag-value","class":"IN","type":"CAA"}
```

### Transform to Array
```php
$record = new CAA([
	'host' => "caa.test.bluelibraries.com",
	'ttl' => 3600,
	'value' => "mult succes",
	'flags' => 1,
	'tag' => "tag-value"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => caa.test.bluelibraries.com
    [ttl] => 3600
    [value] => mult succes
    [flags] => 1
    [tag] => tag-value
    [class] => IN
    [type] => CAA
)
```
