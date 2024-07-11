# MTA-STS (text) records

## Create

### Create from constructor
```php
$record = new MtaSts([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getId = ' . $record->getId() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _mta-sts.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MTA-STS-REPORTING
getVersion = STSv1
getId = test1234
getTxt = v=STSv1; id=test1234
```

### Create with a setter
```php
$record = new MtaSts();
                $record->setData([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getId = ' . $record->getId() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _mta-sts.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MTA-STS-REPORTING
getVersion = STSv1
getId = test1234
getTxt = v=STSv1; id=test1234
```

### Create from string
```php
$record = Record::fromString('_mta-sts.bluelibraries.com 3600 IN TXT "v=STSv1; id=test1234"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getId = ' . $record->getId() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _mta-sts.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MTA-STS-REPORTING
getVersion = STSv1
getId = test1234
getTxt = v=STSv1; id=test1234
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getId = ' . $record->getId() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _mta-sts.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = MTA-STS-REPORTING
getVersion = STSv1
getId = test1234
getTxt = v=STSv1; id=test1234
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('_mta-sts.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\MtaSts Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => /^v=STSv1; id=([a-z0-9]+){1,32}$/i
            [data:protected] => Array
                (
                    [host] => _mta-sts.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=STSv1; id=test1234
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => Array
                (
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('_mta-sts.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\MtaSts Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => /^v=STSv1; id=([a-z0-9]+){1,32}$/i
            [data:protected] => Array
                (
                    [host] => _mta-sts.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=STSv1; id=test1234
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => Array
                (
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

$records = $dns->get('_mta-sts.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\MtaSts Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => /^v=STSv1; id=([a-z0-9]+){1,32}$/i
            [data:protected] => Array
                (
                    [host] => _mta-sts.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=STSv1; id=test1234
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\MtaSts:private] => Array
                (
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new MtaSts([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "_mta-sts.bluelibraries.com 3600 IN TXT \"v=STSv1; id=test1234\""
string2 = "_mta-sts.bluelibraries.com 3600 IN TXT \"v=STSv1; id=test1234\""
```

### Transform to JSON
```php
$record = new MtaSts([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"_mta-sts.bluelibraries.com","ttl":3600,"txt":"v=STSv1; id=test1234","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new MtaSts([
	'host' => "_mta-sts.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=STSv1; id=test1234"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => _mta-sts.bluelibraries.com
    [ttl] => 3600
    [txt] => v=STSv1; id=test1234
    [class] => IN
    [type] => TXT
)
```
