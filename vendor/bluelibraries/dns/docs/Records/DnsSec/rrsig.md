# RRSIG records

## Create

### Create from constructor
```php
$record = new RRSIG([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTypeCovered = ' . $record->getTypeCovered() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getLabelsNumber = ' . $record->getLabelsNumber() . PHP_EOL;
echo 'getOriginalTtl = ' . $record->getOriginalTtl() . PHP_EOL;
echo 'getExpiration = ' . $record->getExpiration() . PHP_EOL;
echo 'getCreation = ' . $record->getCreation() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getSignerName = ' . $record->getSignerName() . PHP_EOL;
echo 'getSignature = ' . $record->getSignature() . PHP_EOL;
```
```text
getHost = rrsig.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = RRSIG
getTypeCovered = A
getAlgorithm = 1
getLabelsNumber = 2
getOriginalTtl = 3600
getExpiration = 169254
getCreation = 169253
getTag = 49890
getSignerName = bluelibraries.com
getSignature = ==signature==
```

### Create with a setter
```php
$record = new RRSIG();
                $record->setData([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature=="
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTypeCovered = ' . $record->getTypeCovered() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getLabelsNumber = ' . $record->getLabelsNumber() . PHP_EOL;
echo 'getOriginalTtl = ' . $record->getOriginalTtl() . PHP_EOL;
echo 'getExpiration = ' . $record->getExpiration() . PHP_EOL;
echo 'getCreation = ' . $record->getCreation() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getSignerName = ' . $record->getSignerName() . PHP_EOL;
echo 'getSignature = ' . $record->getSignature() . PHP_EOL;
```
```text
getHost = rrsig.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = RRSIG
getTypeCovered = A
getAlgorithm = 1
getLabelsNumber = 2
getOriginalTtl = 3600
getExpiration = 169254
getCreation = 169253
getTag = 49890
getSignerName = bluelibraries.com
getSignature = ==signature==
```

### Create from string
```php
$record = Record::fromString('rrsig.test.bluelibraries.com 3600 IN RRSIG A 1 2 3600 169254 169253 49890 bluelibraries.com ==signature==');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTypeCovered = ' . $record->getTypeCovered() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getLabelsNumber = ' . $record->getLabelsNumber() . PHP_EOL;
echo 'getOriginalTtl = ' . $record->getOriginalTtl() . PHP_EOL;
echo 'getExpiration = ' . $record->getExpiration() . PHP_EOL;
echo 'getCreation = ' . $record->getCreation() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getSignerName = ' . $record->getSignerName() . PHP_EOL;
echo 'getSignature = ' . $record->getSignature() . PHP_EOL;
```
```text
getHost = rrsig.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = RRSIG
getTypeCovered = A
getAlgorithm = 1
getLabelsNumber = 2
getOriginalTtl = 3600
getExpiration = 169254
getCreation = 169253
getTag = 49890
getSignerName = bluelibraries.com
getSignature = ==signature==
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature==",
	'type' => "RRSIG"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getTypeCovered = ' . $record->getTypeCovered() . PHP_EOL;
echo 'getAlgorithm = ' . $record->getAlgorithm() . PHP_EOL;
echo 'getLabelsNumber = ' . $record->getLabelsNumber() . PHP_EOL;
echo 'getOriginalTtl = ' . $record->getOriginalTtl() . PHP_EOL;
echo 'getExpiration = ' . $record->getExpiration() . PHP_EOL;
echo 'getCreation = ' . $record->getCreation() . PHP_EOL;
echo 'getTag = ' . $record->getTag() . PHP_EOL;
echo 'getSignerName = ' . $record->getSignerName() . PHP_EOL;
echo 'getSignature = ' . $record->getSignature() . PHP_EOL;
```
```text
getHost = rrsig.test.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = RRSIG
getTypeCovered = A
getAlgorithm = 1
getLabelsNumber = 2
getOriginalTtl = 3600
getExpiration = 169254
getCreation = 169253
getTag = 49890
getSignerName = bluelibraries.com
getSignature = ==signature==
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::RRSIG);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\RRSIG Object
        (
            [data:protected] => Array
                (
                    [host] => rrsig.test.bluelibraries.com
                    [ttl] => 3600
                    [type-covered] => A
                    [algorithm] => 1
                    [labels-number] => 2
                    [original-ttl] => 3600
                    [signature-expiration] => 169254
                    [signature-creation] => 169253
                    [key-tag] => 49890
                    [signer-name] => bluelibraries.com
                    [signature] => ==signature==
                    [type] => RRSIG
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('bluelibraries.com', RecordTypes::RRSIG);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\RRSIG Object
        (
            [data:protected] => Array
                (
                    [host] => rrsig.test.bluelibraries.com
                    [ttl] => 3600
                    [type-covered] => A
                    [algorithm] => 1
                    [labels-number] => 2
                    [original-ttl] => 3600
                    [signature-expiration] => 169254
                    [signature-creation] => 169253
                    [key-tag] => 49890
                    [signer-name] => bluelibraries.com
                    [signature] => ==signature==
                    [type] => RRSIG
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

$records = $dns->get('bluelibraries.com', RecordTypes::RRSIG);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\RRSIG Object
        (
            [data:protected] => Array
                (
                    [host] => rrsig.test.bluelibraries.com
                    [ttl] => 3600
                    [type-covered] => A
                    [algorithm] => 1
                    [labels-number] => 2
                    [original-ttl] => 3600
                    [signature-expiration] => 169254
                    [signature-creation] => 169253
                    [key-tag] => 49890
                    [signer-name] => bluelibraries.com
                    [signature] => ==signature==
                    [type] => RRSIG
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new RRSIG([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature=="
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "rrsig.test.bluelibraries.com 3600 IN RRSIG A 1 2 3600 169254 169253 49890 bluelibraries.com ==signature=="
string2 = "rrsig.test.bluelibraries.com 3600 IN RRSIG A 1 2 3600 169254 169253 49890 bluelibraries.com ==signature=="
```

### Transform to JSON
```php
$record = new RRSIG([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature=="
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"rrsig.test.bluelibraries.com","ttl":3600,"type-covered":"A","algorithm":1,"labels-number":2,"original-ttl":3600,"signature-expiration":169254,"signature-creation":169253,"key-tag":49890,"signer-name":"bluelibraries.com","signature":"==signature==","class":"IN","type":"RRSIG"}
```

### Transform to Array
```php
$record = new RRSIG([
	'host' => "rrsig.test.bluelibraries.com",
	'ttl' => 3600,
	'type-covered' => "A",
	'algorithm' => 1,
	'labels-number' => 2,
	'original-ttl' => 3600,
	'signature-expiration' => 169254,
	'signature-creation' => 169253,
	'key-tag' => 49890,
	'signer-name' => "bluelibraries.com",
	'signature' => "==signature=="
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => rrsig.test.bluelibraries.com
    [ttl] => 3600
    [type-covered] => A
    [algorithm] => 1
    [labels-number] => 2
    [original-ttl] => 3600
    [signature-expiration] => 169254
    [signature-creation] => 169253
    [key-tag] => 49890
    [signer-name] => bluelibraries.com
    [signature] => ==signature==
    [class] => IN
    [type] => RRSIG
)
```
