# DKIM (text) records

## Create

### Create from constructor
```php
$record = new DKIM([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getKeyType = ' . $record->getKeyType() . PHP_EOL;
echo 'getHashType = ' . $record->getHashType() . PHP_EOL;
echo 'getGroup = ' . $record->getGroup() . PHP_EOL;
echo 'getNotes = ' . $record->getNotes() . PHP_EOL;
echo 'getQuery = ' . $record->getQuery() . PHP_EOL;
echo 'getServiceType = ' . $record->getServiceType() . PHP_EOL;
echo 'getTestingType = ' . $record->getTestingType() . PHP_EOL;
echo 'getSelector = ' . $record->getSelector() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = dkim._domainkey.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DKIM
getPublicKey = publickey
getVersion = DKIM1
getKeyType = rsa
getHashType = a
getGroup = group-test
getNotes = notes
getQuery = test-query
getServiceType = X
getTestingType = 0
getSelector = dkim
getTxt = v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
```

### Create with a setter
```php
$record = new DKIM();
                $record->setData([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getKeyType = ' . $record->getKeyType() . PHP_EOL;
echo 'getHashType = ' . $record->getHashType() . PHP_EOL;
echo 'getGroup = ' . $record->getGroup() . PHP_EOL;
echo 'getNotes = ' . $record->getNotes() . PHP_EOL;
echo 'getQuery = ' . $record->getQuery() . PHP_EOL;
echo 'getServiceType = ' . $record->getServiceType() . PHP_EOL;
echo 'getTestingType = ' . $record->getTestingType() . PHP_EOL;
echo 'getSelector = ' . $record->getSelector() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = dkim._domainkey.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DKIM
getPublicKey = publickey
getVersion = DKIM1
getKeyType = rsa
getHashType = a
getGroup = group-test
getNotes = notes
getQuery = test-query
getServiceType = X
getTestingType = 0
getSelector = dkim
getTxt = v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
```

### Create from string
```php
$record = Record::fromString('dkim._domainkey.bluelibraries.com 3600 IN TXT "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getKeyType = ' . $record->getKeyType() . PHP_EOL;
echo 'getHashType = ' . $record->getHashType() . PHP_EOL;
echo 'getGroup = ' . $record->getGroup() . PHP_EOL;
echo 'getNotes = ' . $record->getNotes() . PHP_EOL;
echo 'getQuery = ' . $record->getQuery() . PHP_EOL;
echo 'getServiceType = ' . $record->getServiceType() . PHP_EOL;
echo 'getTestingType = ' . $record->getTestingType() . PHP_EOL;
echo 'getSelector = ' . $record->getSelector() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = dkim._domainkey.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DKIM
getPublicKey = publickey
getVersion = DKIM1
getKeyType = rsa
getHashType = a
getGroup = group-test
getNotes = notes
getQuery = test-query
getServiceType = X
getTestingType = 0
getSelector = dkim
getTxt = v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getPublicKey = ' . $record->getPublicKey() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getKeyType = ' . $record->getKeyType() . PHP_EOL;
echo 'getHashType = ' . $record->getHashType() . PHP_EOL;
echo 'getGroup = ' . $record->getGroup() . PHP_EOL;
echo 'getNotes = ' . $record->getNotes() . PHP_EOL;
echo 'getQuery = ' . $record->getQuery() . PHP_EOL;
echo 'getServiceType = ' . $record->getServiceType() . PHP_EOL;
echo 'getTestingType = ' . $record->getTestingType() . PHP_EOL;
echo 'getSelector = ' . $record->getSelector() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = dkim._domainkey.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DKIM
getPublicKey = publickey
getVersion = DKIM1
getKeyType = rsa
getHashType = a
getGroup = group-test
getNotes = notes
getQuery = test-query
getServiceType = X
getTestingType = 0
getSelector = dkim
getTxt = v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('dkim._domainkey.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DKIM Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => /^v=DKIM1;([a-z0-9; =]+)p=([a-zA-Z0-9\/+]+)/i
            [data:protected] => Array
                (
                    [host] => dkim._domainkey.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => Array
                (
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('dkim._domainkey.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DKIM Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => /^v=DKIM1;([a-z0-9; =]+)p=([a-zA-Z0-9\/+]+)/i
            [data:protected] => Array
                (
                    [host] => dkim._domainkey.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => Array
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

$records = $dns->get('dkim._domainkey.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DKIM Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => /^v=DKIM1;([a-z0-9; =]+)p=([a-zA-Z0-9\/+]+)/i
            [data:protected] => Array
                (
                    [host] => dkim._domainkey.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DKIM:private] => Array
                (
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new DKIM([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "dkim._domainkey.bluelibraries.com 3600 IN TXT \"v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0\""
string2 = "dkim._domainkey.bluelibraries.com 3600 IN TXT \"v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0\""
```

### Transform to JSON
```php
$record = new DKIM([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"dkim._domainkey.bluelibraries.com","ttl":3600,"txt":"v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new DKIM([
	'host' => "dkim._domainkey.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => dkim._domainkey.bluelibraries.com
    [ttl] => 3600
    [txt] => v=DKIM1; p=publickey; k=rsa; h=a; g=group-test; n=notes;q=test-query;s=X; t=0
    [class] => IN
    [type] => TXT
)
```
