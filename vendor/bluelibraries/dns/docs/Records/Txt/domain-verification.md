# Domain verification (text) records

## Create

### Create from constructor
```php
$record = new DomainVerification([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getExtendedTypeName = ' . $record->getExtendedTypeName() . PHP_EOL;
echo 'getProvider = ' . $record->getProvider() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = verificatin.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DOMAIN-VERIFICATION
getExtendedTypeName = DOMAIN-VERIFICATION
getProvider = google
getValue = eroii-nu-mor-niciodata
getTxt = google-site-verification=eroii-nu-mor-niciodata
```

### Create with a setter
```php
$record = new DomainVerification();
                $record->setData([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getExtendedTypeName = ' . $record->getExtendedTypeName() . PHP_EOL;
echo 'getProvider = ' . $record->getProvider() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = verificatin.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DOMAIN-VERIFICATION
getExtendedTypeName = DOMAIN-VERIFICATION
getProvider = google
getValue = eroii-nu-mor-niciodata
getTxt = google-site-verification=eroii-nu-mor-niciodata
```

### Create from string
```php
$record = Record::fromString('verificatin.test.bluelibraries.com 3600 IN TXT "google-site-verification=eroii-nu-mor-niciodata"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getExtendedTypeName = ' . $record->getExtendedTypeName() . PHP_EOL;
echo 'getProvider = ' . $record->getProvider() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = verificatin.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DOMAIN-VERIFICATION
getExtendedTypeName = DOMAIN-VERIFICATION
getProvider = google
getValue = eroii-nu-mor-niciodata
getTxt = google-site-verification=eroii-nu-mor-niciodata
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getExtendedTypeName = ' . $record->getExtendedTypeName() . PHP_EOL;
echo 'getProvider = ' . $record->getProvider() . PHP_EOL;
echo 'getValue = ' . $record->getValue() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = verificatin.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DOMAIN-VERIFICATION
getExtendedTypeName = DOMAIN-VERIFICATION
getProvider = google
getValue = eroii-nu-mor-niciodata
getTxt = google-site-verification=eroii-nu-mor-niciodata
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('verificatin.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => verificatin.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => google-site-verification=eroii-nu-mor-niciodata
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('verificatin.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => verificatin.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => google-site-verification=eroii-nu-mor-niciodata
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

$records = $dns->get('verificatin.test.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => verificatin.test.bluelibraries.com
                    [ttl] => 3600
                    [txt] => google-site-verification=eroii-nu-mor-niciodata
                    [type] => TXT
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new DomainVerification([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "verificatin.test.bluelibraries.com 3600 IN TXT \"google-site-verification=eroii-nu-mor-niciodata\""
string2 = "verificatin.test.bluelibraries.com 3600 IN TXT \"google-site-verification=eroii-nu-mor-niciodata\""
```

### Transform to JSON
```php
$record = new DomainVerification([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"verificatin.test.bluelibraries.com","ttl":3600,"txt":"google-site-verification=eroii-nu-mor-niciodata","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new DomainVerification([
	'host' => "verificatin.test.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "google-site-verification=eroii-nu-mor-niciodata"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => verificatin.test.bluelibraries.com
    [ttl] => 3600
    [txt] => google-site-verification=eroii-nu-mor-niciodata
    [class] => IN
    [type] => TXT
)
```
