# HINFO (host information) records

## Create

### Create from constructor
```php
$record = new HINFO([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHardware = ' . $record->getHardware() . PHP_EOL;
echo 'getOperatingSystem = ' . $record->getOperatingSystem() . PHP_EOL;
```
```text
getHost = hinfo.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = HINFO
getHardware = Pentium 1
getOperatingSystem = Win 95
```

### Create with a setter
```php
$record = new HINFO();
                $record->setData([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHardware = ' . $record->getHardware() . PHP_EOL;
echo 'getOperatingSystem = ' . $record->getOperatingSystem() . PHP_EOL;
```
```text
getHost = hinfo.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = HINFO
getHardware = Pentium 1
getOperatingSystem = Win 95
```

### Create from string
```php
$record = Record::fromString('hinfo.test.bluelibraries.com 3600 IN HINFO "Pentium 1" "Win 95"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHardware = ' . $record->getHardware() . PHP_EOL;
echo 'getOperatingSystem = ' . $record->getOperatingSystem() . PHP_EOL;
```
```text
getHost = hinfo.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = HINFO
getHardware = "Pentium
getOperatingSystem = 1" "Win 95"
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95",
	'type' => "HINFO"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHardware = ' . $record->getHardware() . PHP_EOL;
echo 'getOperatingSystem = ' . $record->getOperatingSystem() . PHP_EOL;
```
```text
getHost = hinfo.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = HINFO
getHardware = Pentium 1
getOperatingSystem = Win 95
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('hinfo.bluelibraries.com', RecordTypes::HINFO);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HINFO Object
        (
            [data:protected] => Array
                (
                    [host] => hinfo.test.bluelibraries.com
                    [ttl] => 3600
                    [hardware] => Pentium 1
                    [os] => Win 95
                    [type] => HINFO
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('hinfo.bluelibraries.com', RecordTypes::HINFO);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HINFO Object
        (
            [data:protected] => Array
                (
                    [host] => hinfo.test.bluelibraries.com
                    [ttl] => 3600
                    [hardware] => Pentium 1
                    [os] => Win 95
                    [type] => HINFO
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

$records = $dns->get('hinfo.bluelibraries.com', RecordTypes::HINFO);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\HINFO Object
        (
            [data:protected] => Array
                (
                    [host] => hinfo.test.bluelibraries.com
                    [ttl] => 3600
                    [hardware] => Pentium 1
                    [os] => Win 95
                    [type] => HINFO
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new HINFO([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "hinfo.test.bluelibraries.com 3600 IN HINFO \"Pentium 1\" \"Win 95\""
string2 = "hinfo.test.bluelibraries.com 3600 IN HINFO \"Pentium 1\" \"Win 95\""
```

### Transform to JSON
```php
$record = new HINFO([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"hinfo.test.bluelibraries.com","ttl":3600,"hardware":"Pentium 1","os":"Win 95","class":"IN","type":"HINFO"}
```

### Transform to Array
```php
$record = new HINFO([
	'host' => "hinfo.test.bluelibraries.com",
	'ttl' => 3600,
	'hardware' => "Pentium 1",
	'os' => "Win 95"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => hinfo.test.bluelibraries.com
    [ttl] => 3600
    [hardware] => Pentium 1
    [os] => Win 95
    [class] => IN
    [type] => HINFO
)
```
