# SPF (text) records

## Create

### Create from constructor
```php
$record = new SPF([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHosts = ';
print_r($record->getHosts()) . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = spf.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SPF
getHosts = Array
(
    [0] => include:_spf.test.bluelibraries.com
)
getTxt = v=spf1 include:_spf.test.bluelibraries.com
```

### Create with a setter
```php
$record = new SPF();
                $record->setData([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHosts = ';
print_r($record->getHosts()) . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = spf.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SPF
getHosts = Array
(
    [0] => include:_spf.test.bluelibraries.com
)
getTxt = v=spf1 include:_spf.test.bluelibraries.com
```

### Create from string
```php
$record = Record::fromString('spf.test.bluelibraries.com 3600 IN TXT "v=spf1 include:_spf.test.bluelibraries.com"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHosts = ';
print_r($record->getHosts()) . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = spf.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SPF
getHosts = Array
(
    [0] => include:_spf.test.bluelibraries.com
)
getTxt = v=spf1 include:_spf.test.bluelibraries.com
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getHosts = ';
print_r($record->getHosts()) . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = spf.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SPF
getHosts = Array
(
    [0] => include:_spf.test.bluelibraries.com
)
getTxt = v=spf1 include:_spf.test.bluelibraries.com
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('spf.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\SPF Object
        (
            [data:protected] => Array
                (
                    [host] => spf.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=spf1 include:_spf.test.bluelibraries.com
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('spf.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\SPF Object
        (
            [data:protected] => Array
                (
                    [host] => spf.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=spf1 include:_spf.test.bluelibraries.com
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

$records = $dns->get('spf.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\SPF Object
        (
            [data:protected] => Array
                (
                    [host] => spf.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=spf1 include:_spf.test.bluelibraries.com
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new SPF([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "spf.test.bluelibraries.com 3600 IN TXT \"v=spf1 include:_spf.test.bluelibraries.com\""
string2 = "spf.test.bluelibraries.com 3600 IN TXT \"v=spf1 include:_spf.test.bluelibraries.com\""
```

### Transform to JSON
```php
$record = new SPF([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"spf.test.bluelibraries.com","ttl":3600,"txt":"v=spf1 include:_spf.test.bluelibraries.com","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new SPF([
	'host' => "spf.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=spf1 include:_spf.test.bluelibraries.com"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => spf.test.bluelibraries.com
    [ttl] => 3600
    [txt] => v=spf1 include:_spf.test.bluelibraries.com
    [class] => IN
    [type] => TXT
)
```
