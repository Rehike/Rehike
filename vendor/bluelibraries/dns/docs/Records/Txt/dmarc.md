# DMARC (text) records

## Create

### Create from constructor
```php
$record = new DMARC([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getPolicy = ' . $record->getPolicy() . PHP_EOL;
echo 'getPercentage = ' . $record->getPercentage() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getRuf = ' . $record->getRuf() . PHP_EOL;
echo 'getFo = ' . $record->getFo() . PHP_EOL;
echo 'getAspf = ' . $record->getAspf() . PHP_EOL;
echo 'getAdkim = ' . $record->getAdkim() . PHP_EOL;
echo 'getReportFormat = ' . $record->getReportFormat() . PHP_EOL;
echo 'getReportInterval = ' . $record->getReportInterval() . PHP_EOL;
echo 'getSubdomainPolicy = ' . $record->getSubdomainPolicy() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _dmarc.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DMARK
getVersion = DMARC1
getPolicy = quarantine
getPercentage = 75
getRua = mailto:postmaster@test.com
getRuf = mailto:ruf@test.com
getFo = d
getAspf = s
getAdkim = r
getReportFormat = afrf
getReportInterval = 86400
getSubdomainPolicy = reject
getTxt = v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
```

### Create with a setter
```php
$record = new DMARC();
                $record->setData([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getPolicy = ' . $record->getPolicy() . PHP_EOL;
echo 'getPercentage = ' . $record->getPercentage() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getRuf = ' . $record->getRuf() . PHP_EOL;
echo 'getFo = ' . $record->getFo() . PHP_EOL;
echo 'getAspf = ' . $record->getAspf() . PHP_EOL;
echo 'getAdkim = ' . $record->getAdkim() . PHP_EOL;
echo 'getReportFormat = ' . $record->getReportFormat() . PHP_EOL;
echo 'getReportInterval = ' . $record->getReportInterval() . PHP_EOL;
echo 'getSubdomainPolicy = ' . $record->getSubdomainPolicy() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _dmarc.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DMARK
getVersion = DMARC1
getPolicy = quarantine
getPercentage = 75
getRua = mailto:postmaster@test.com
getRuf = mailto:ruf@test.com
getFo = d
getAspf = s
getAdkim = r
getReportFormat = afrf
getReportInterval = 86400
getSubdomainPolicy = reject
getTxt = v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
```

### Create from string
```php
$record = Record::fromString('_dmarc.bluelibraries.com 3600 IN TXT "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getPolicy = ' . $record->getPolicy() . PHP_EOL;
echo 'getPercentage = ' . $record->getPercentage() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getRuf = ' . $record->getRuf() . PHP_EOL;
echo 'getFo = ' . $record->getFo() . PHP_EOL;
echo 'getAspf = ' . $record->getAspf() . PHP_EOL;
echo 'getAdkim = ' . $record->getAdkim() . PHP_EOL;
echo 'getReportFormat = ' . $record->getReportFormat() . PHP_EOL;
echo 'getReportInterval = ' . $record->getReportInterval() . PHP_EOL;
echo 'getSubdomainPolicy = ' . $record->getSubdomainPolicy() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _dmarc.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DMARK
getVersion = DMARC1
getPolicy = quarantine
getPercentage = 75
getRua = mailto:postmaster@test.com
getRuf = mailto:ruf@test.com
getFo = d
getAspf = s
getAdkim = r
getReportFormat = afrf
getReportInterval = 86400
getSubdomainPolicy = reject
getTxt = v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400",
	'type' => "TXT"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getVersion = ' . $record->getVersion() . PHP_EOL;
echo 'getPolicy = ' . $record->getPolicy() . PHP_EOL;
echo 'getPercentage = ' . $record->getPercentage() . PHP_EOL;
echo 'getRua = ' . $record->getRua() . PHP_EOL;
echo 'getRuf = ' . $record->getRuf() . PHP_EOL;
echo 'getFo = ' . $record->getFo() . PHP_EOL;
echo 'getAspf = ' . $record->getAspf() . PHP_EOL;
echo 'getAdkim = ' . $record->getAdkim() . PHP_EOL;
echo 'getReportFormat = ' . $record->getReportFormat() . PHP_EOL;
echo 'getReportInterval = ' . $record->getReportInterval() . PHP_EOL;
echo 'getSubdomainPolicy = ' . $record->getSubdomainPolicy() . PHP_EOL;
echo 'getTxt = ' . $record->getTxt() . PHP_EOL;
```
```text
getHost = _dmarc.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = DMARK
getVersion = DMARC1
getPolicy = quarantine
getPercentage = 75
getRua = mailto:postmaster@test.com
getRuf = mailto:ruf@test.com
getFo = d
getAspf = s
getAdkim = r
getReportFormat = afrf
getReportInterval = 86400
getSubdomainPolicy = reject
getTxt = v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('_dmarc.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DMARC Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => /^v=DMARC1?;([a-z0-9;\ =:@_.]+)$/i
            [data:protected] => Array
                (
                    [host] => _dmarc.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => Array
                (
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('_dmarc.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DMARC Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => /^v=DMARC1?;([a-z0-9;\ =:@_.]+)$/i
            [data:protected] => Array
                (
                    [host] => _dmarc.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => Array
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

$records = $dns->get('_dmarc.bluelibraries.com', RecordTypes::TXT);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DMARC Object
        (
            [txtRegex:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => /^v=DMARC1?;([a-z0-9;\ =:@_.]+)$/i
            [data:protected] => Array
                (
                    [host] => _dmarc.bluelibraries.com
                    [ttl] => 3600
                    [txt] => v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
                    [type] => TXT
                    [class] => IN
                )

            [parsedValues:BlueLibraries\Dns\Records\Types\Txt\DMARC:private] => Array
                (
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new DMARC([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "_dmarc.bluelibraries.com 3600 IN TXT \"v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400\""
string2 = "_dmarc.bluelibraries.com 3600 IN TXT \"v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400\""
```

### Transform to JSON
```php
$record = new DMARC([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"_dmarc.bluelibraries.com","ttl":3600,"txt":"v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400","class":"IN","type":"TXT"}
```

### Transform to Array
```php
$record = new DMARC([
	'host' => "_dmarc.bluelibraries.com",
	'ttl' => 3600,
	'txt' => "v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => _dmarc.bluelibraries.com
    [ttl] => 3600
    [txt] => v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400
    [class] => IN
    [type] => TXT
)
```
