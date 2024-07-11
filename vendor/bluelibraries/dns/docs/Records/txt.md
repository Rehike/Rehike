# TXT (text) records

## Create

### Create from constructor
```php
$record = new TXT([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\""
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = txt.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TXT
getTxt = heroes never die - eroii nu mor niciodata "txt"
```

### Create with a setter
```php
$record = new TXT();
                $record->setData([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\""
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = txt.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TXT
getTxt = heroes never die - eroii nu mor niciodata "txt"
```

### Create from string
```php
$record = Record::fromString('txt.test.bluelibraries.com 3600 IN TXT "heroes never die - eroii nu mor niciodata "txt""');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = txt.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TXT
getTxt = heroes never die - eroii nu mor niciodata "txt"
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\"",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = txt.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TXT
getTxt = heroes never die - eroii nu mor niciodata "txt"
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('txt.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => txt.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => heroes never die - eroii nu mor niciodata "txt"
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('txt.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => txt.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => heroes never die - eroii nu mor niciodata "txt"
                    [type] => TXT
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

$records = $dns->get('txt.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => txt.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => heroes never die - eroii nu mor niciodata "txt"
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new TXT([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\""
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "txt.test.bluelibraries.com 3600 IN TXT \"heroes never die - eroii nu mor niciodata \\\"txt\\\"\""
string2 = "txt.test.bluelibraries.com 3600 IN TXT \"heroes never die - eroii nu mor niciodata \\\"txt\\\"\""
```

### Transform to JSON
```php
$record = new TXT([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\""
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"txt.test.bluelibraries.com","ttl":3600,"txt":"heroes never die - eroii nu mor niciodata \"txt\"","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new TXT([
	'host' => "txt.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "heroes never die - eroii nu mor niciodata \"txt\""
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => txt.test.bluelibraries.com
    [ttl] => 3600
    [txt] => heroes never die - eroii nu mor niciodata "txt"
    [class] => IN
    [type] => TXT
)
```
