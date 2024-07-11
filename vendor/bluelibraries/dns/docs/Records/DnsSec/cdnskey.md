# CDNSKey records

## Create

### Create from constructor
```php
$record = new CDNSKey([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getProtocol = ' . $record->getProtocol() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
```
```text
getHost = ckdns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDNSKEY
getFlags = 255
getProtocol = 3
getAlgorithm = 12
getPublicKey = public-key==
```

### Create with a setter
```php
$record = new CDNSKey();
                $record->setData([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getProtocol = ' . $record->getProtocol() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
```
```text
getHost = ckdns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDNSKEY
getFlags = 255
getProtocol = 3
getAlgorithm = 12
getPublicKey = public-key==
```

### Create from string
```php
$record = Record::fromString('ckdns.bluelibraries.com 3600 IN CDNSKEY 255 3 12 public-key==');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getProtocol = ' . $record->getProtocol() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
```
```text
getHost = ckdns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDNSKEY
getFlags = 255
getProtocol = 3
getAlgorithm = 12
getPublicKey = public-key==
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key==",
	'type' => "CDNSKEY"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getFlags = ' . $record->getFlags() . PHP_EOL;
echo 'getProtocol = ' . $record->getProtocol() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
```
```text
getHost = ckdns.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = CDNSKEY
getFlags = 255
getProtocol = 3
getAlgorithm = 12
getPublicKey = public-key==
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('ckdns.bluelibraries.com', RecordTypes::CDNSKEY);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDNSKey Object
        (
            [data:protected] => Array
                (
                    [host] => ckdns.bluelibraries.com
                    [ttl] => 3600
                    [value] => cdnskey-value
                    [flags] => 255
                    [protocol] => 3
                    [algorithm] => 12
                    [public-key] => public-key==
                    [type] => CDNSKEY
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('ckdns.bluelibraries.com', RecordTypes::CDNSKEY);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDNSKey Object
        (
            [data:protected] => Array
                (
                    [host] => ckdns.bluelibraries.com
                    [ttl] => 3600
                    [value] => cdnskey-value
                    [flags] => 255
                    [protocol] => 3
                    [algorithm] => 12
                    [public-key] => public-key==
                    [type] => CDNSKEY
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

$records = $dns->get('ckdns.bluelibraries.com', RecordTypes::CDNSKEY);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\CDNSKey Object
        (
            [data:protected] => Array
                (
                    [host] => ckdns.bluelibraries.com
                    [ttl] => 3600
                    [value] => cdnskey-value
                    [flags] => 255
                    [protocol] => 3
                    [algorithm] => 12
                    [public-key] => public-key==
                    [type] => CDNSKEY
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new CDNSKey([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key=="
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "ckdns.bluelibraries.com 3600 IN CDNSKEY 255 3 12 public-key=="
string2 = "ckdns.bluelibraries.com 3600 IN CDNSKEY 255 3 12 public-key=="
```

### Transform to JSON
```php
$record = new CDNSKey([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key=="
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"ckdns.bluelibraries.com","ttl":3600,"value":"cdnskey-value","flags":255,"protocol":3,"algorithm":12,"public-key":"public-key==","class":"IN","type":"CDNSKEY"}
```

### Transform to Array
```php
$record = new CDNSKey([
	'host' => "ckdns.bluelibraries.com",
	'ttl' => 3600,
	'value' => "cdnskey-value",
	'flags' => 255,
	'protocol' => 3,
	'algorithm' => 12,
	'public-key' => "public-key=="
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => ckdns.bluelibraries.com
    [ttl] => 3600
    [value] => cdnskey-value
    [flags] => 255
    [protocol] => 3
    [algorithm] => 12
    [public-key] => public-key==
    [class] => IN
    [type] => CDNSKEY
)
```
