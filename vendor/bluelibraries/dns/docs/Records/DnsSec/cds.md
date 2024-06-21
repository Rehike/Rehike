# CDS records

## Create

### Create from constructor
```php
$record = new CDS([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getKeyTag = ' . $record->getKeyTag() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getAlgorithmDigest = ' . $record->getAlgorithmDigest() . PHP_EOL;
echo 'getDigest = ' . $record->getDigest() . PHP_EOL;
```
```text
getHost = cds.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDS
getKeyTag = 2371
getAlgorithm = 13
getAlgorithmDigest = 3
getDigest = 1F987CC6583E92DF0890718C42
```

### Create with a setter
```php
$record = new CDS();
                $record->setData([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getKeyTag = ' . $record->getKeyTag() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getAlgorithmDigest = ' . $record->getAlgorithmDigest() . PHP_EOL;
echo 'getDigest = ' . $record->getDigest() . PHP_EOL;
```
```text
getHost = cds.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDS
getKeyTag = 2371
getAlgorithm = 13
getAlgorithmDigest = 3
getDigest = 1F987CC6583E92DF0890718C42
```

### Create from string
```php
$record = Record::fromString('cds.bluelibraries.com 3600 IN CDS 2371 13 3 1F987CC6583E92DF0890718C42');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getKeyTag = ' . $record->getKeyTag() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getAlgorithmDigest = ' . $record->getAlgorithmDigest() . PHP_EOL;
echo 'getDigest = ' . $record->getDigest() . PHP_EOL;
```
```text
getHost = cds.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDS
getKeyTag = 2371
getAlgorithm = 13
getAlgorithmDigest = 3
getDigest = 1F987CC6583E92DF0890718C42
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42",
	'type' => "CDS"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getKeyTag = ' . $record->getKeyTag() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getAlgorithmDigest = ' . $record->getAlgorithmDigest() . PHP_EOL;
echo 'getDigest = ' . $record->getDigest() . PHP_EOL;
```
```text
getHost = cds.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDS
getKeyTag = 2371
getAlgorithm = 13
getAlgorithmDigest = 3
getDigest = 1F987CC6583E92DF0890718C42
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('cds.bluelibraries.com', RecordTypes::CDS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDS Object
        (
            [data:protected] => Array
                (
                    [host] => cds.bluelibraries.com
                    [ttl] => 3600
                    [key-tag] => 2371
                    [algorithm] => 13
                    [algorithm-digest] => 3
                    [digest] => 1F987CC6583E92DF0890718C42
                    [type] => CDS
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('cds.bluelibraries.com', RecordTypes::CDS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDS Object
        (
            [data:protected] => Array
                (
                    [host] => cds.bluelibraries.com
                    [ttl] => 3600
                    [key-tag] => 2371
                    [algorithm] => 13
                    [algorithm-digest] => 3
                    [digest] => 1F987CC6583E92DF0890718C42
                    [type] => CDS
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

$records = $dns->get('cds.bluelibraries.com', RecordTypes::CDS);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDS Object
        (
            [data:protected] => Array
                (
                    [host] => cds.bluelibraries.com
                    [ttl] => 3600
                    [key-tag] => 2371
                    [algorithm] => 13
                    [algorithm-digest] => 3
                    [digest] => 1F987CC6583E92DF0890718C42
                    [type] => CDS
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new CDS([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "cds.bluelibraries.com 3600 IN CDS 2371 13 3 1F987CC6583E92DF0890718C42"
string2 = "cds.bluelibraries.com 3600 IN CDS 2371 13 3 1F987CC6583E92DF0890718C42"
```

### Transform to JSON
```php
$record = new CDS([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"cds.bluelibraries.com","ttl":3600,"key-tag":2371,"algorithm":13,"algorithm-digest":3,"digest":"1F987CC6583E92DF0890718C42","class":"IN","type":"CDS"}
```

### Transform to Array
```php
$record = new CDS([
	'host' => "cds.bluelibraries.com",
	'ttl' => 3600,
	'key-tag' => 2371,
	'algorithm' => 13,
	'algorithm-digest' => 3,
	'digest' => "1F987CC6583E92DF0890718C42"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => cds.bluelibraries.com
    [ttl] => 3600
    [key-tag] => 2371
    [algorithm] => 13
    [algorithm-digest] => 3
    [digest] => 1F987CC6583E92DF0890718C42
    [class] => IN
    [type] => CDS
)
```
