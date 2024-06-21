# TLS reporting (text) records

## Create

### Create from constructor
```php
$record = new TlsReporting([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _smtp._tls.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TLS-REPORTING
getVersion = TLSRPTv1
getRua = mailto:postmaster@test.com
getTxt = v=TLSRPTv1; rua=mailto:postmaster@test.com
```

### Create with a setter
```php
$record = new TlsReporting();
                $record->setData([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _smtp._tls.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TLS-REPORTING
getVersion = TLSRPTv1
getRua = mailto:postmaster@test.com
getTxt = v=TLSRPTv1; rua=mailto:postmaster@test.com
```

### Create from string
```php
$record = Record::fromString('_smtp._tls.bluelibraries.com 3600 IN TXT "v=TLSRPTv1; rua=mailto:postmaster@test.com"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _smtp._tls.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TLS-REPORTING
getVersion = TLSRPTv1
getRua = mailto:postmaster@test.com
getTxt = v=TLSRPTv1; rua=mailto:postmaster@test.com
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _smtp._tls.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = TLS-REPORTING
getVersion = TLSRPTv1
getRua = mailto:postmaster@test.com
getTxt = v=TLSRPTv1; rua=mailto:postmaster@test.com
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('_smtp._tls.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\TLSReporting Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => /^v=TLSRPTv1; rua=mailto:([a-z.\-_@]+)((,mailto\:([a-z.\-_@]+))+)?$/i
            [data:protected] => Array
                (
                    [host] => _smtp._tls.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=TLSRPTv1; rua=mailto:postmaster@test.com
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => Array
                (
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('_smtp._tls.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\TLSReporting Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => /^v=TLSRPTv1; rua=mailto:([a-z.\-_@]+)((,mailto\:([a-z.\-_@]+))+)?$/i
            [data:protected] => Array
                (
                    [host] => _smtp._tls.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=TLSRPTv1; rua=mailto:postmaster@test.com
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => Array
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

$records = $dns->get('_smtp._tls.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\TLSReporting Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => /^v=TLSRPTv1; rua=mailto:([a-z.\-_@]+)((,mailto\:([a-z.\-_@]+))+)?$/i
            [data:protected] => Array
                (
                    [host] => _smtp._tls.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=TLSRPTv1; rua=mailto:postmaster@test.com
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\TLSReporting:private] => Array
                (
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new TlsReporting([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "_smtp._tls.bluelibraries.com 3600 IN TXT \"v=TLSRPTv1; rua=mailto:postmaster@test.com\""
string2 = "_smtp._tls.bluelibraries.com 3600 IN TXT \"v=TLSRPTv1; rua=mailto:postmaster@test.com\""
```

### Transform to JSON
```php
$record = new TlsReporting([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"_smtp._tls.bluelibraries.com","ttl":3600,"txt":"v=TLSRPTv1; rua=mailto:postmaster@test.com","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new TlsReporting([
	'host' => "_smtp._tls.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=TLSRPTv1; rua=mailto:postmaster@test.com"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => _smtp._tls.bluelibraries.com
    [ttl] => 3600
    [txt] => v=TLSRPTv1; rua=mailto:postmaster@test.com
    [class] => IN
    [type] => TXT
)
```
