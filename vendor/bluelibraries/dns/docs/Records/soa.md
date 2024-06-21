# SOA records

## Create

### Create from constructor
```php
$record = new SOA([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getMasterNameServer = ' . $record->getMasterNameServer() . PHP_EOL;
echo 'getRawEmailName = ' . $record->getRawEmailName() . PHP_EOL;
echo 'getAdministratorEmailAddress = ' . $record->getAdministratorEmailAddress() . PHP_EOL;
echo 'getSerial = ' . $record->getSerial() . PHP_EOL;
echo 'getRefresh = ' . $record->getRefresh() . PHP_EOL;
echo 'getRetry = ' . $record->getRetry() . PHP_EOL;
echo 'getExpire = ' . $record->getExpire() . PHP_EOL;
echo 'getMinimumTtl = ' . $record->getMinimumTtl() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SOA
getMasterNameServer = test.com
getRawEmailName = admin.test.com
getAdministratorEmailAddress = admin@test.com
getSerial = 123456789
getRefresh = 3600
getRetry = 10
getExpire = 1800
getMinimumTtl = 1200
```

### Create with a setter
```php
$record = new SOA();
                $record->setData([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getMasterNameServer = ' . $record->getMasterNameServer() . PHP_EOL;
echo 'getRawEmailName = ' . $record->getRawEmailName() . PHP_EOL;
echo 'getAdministratorEmailAddress = ' . $record->getAdministratorEmailAddress() . PHP_EOL;
echo 'getSerial = ' . $record->getSerial() . PHP_EOL;
echo 'getRefresh = ' . $record->getRefresh() . PHP_EOL;
echo 'getRetry = ' . $record->getRetry() . PHP_EOL;
echo 'getExpire = ' . $record->getExpire() . PHP_EOL;
echo 'getMinimumTtl = ' . $record->getMinimumTtl() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SOA
getMasterNameServer = test.com
getRawEmailName = admin.test.com
getAdministratorEmailAddress = admin@test.com
getSerial = 123456789
getRefresh = 3600
getRetry = 10
getExpire = 1800
getMinimumTtl = 1200
```

### Create from string
```php
$record = Record::fromString('bluelibraries.com 3600 IN SOA test.com admin.test.com 123456789 3600 10 1800 1200');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getMasterNameServer = ' . $record->getMasterNameServer() . PHP_EOL;
echo 'getRawEmailName = ' . $record->getRawEmailName() . PHP_EOL;
echo 'getAdministratorEmailAddress = ' . $record->getAdministratorEmailAddress() . PHP_EOL;
echo 'getSerial = ' . $record->getSerial() . PHP_EOL;
echo 'getRefresh = ' . $record->getRefresh() . PHP_EOL;
echo 'getRetry = ' . $record->getRetry() . PHP_EOL;
echo 'getExpire = ' . $record->getExpire() . PHP_EOL;
echo 'getMinimumTtl = ' . $record->getMinimumTtl() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SOA
getMasterNameServer = test.com
getRawEmailName = admin.test.com
getAdministratorEmailAddress = admin@test.com
getSerial = 123456789
getRefresh = 3600
getRetry = 10
getExpire = 1800
getMinimumTtl = 1200
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800,
	'type' => "SOA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getMasterNameServer = ' . $record->getMasterNameServer() . PHP_EOL;
echo 'getRawEmailName = ' . $record->getRawEmailName() . PHP_EOL;
echo 'getAdministratorEmailAddress = ' . $record->getAdministratorEmailAddress() . PHP_EOL;
echo 'getSerial = ' . $record->getSerial() . PHP_EOL;
echo 'getRefresh = ' . $record->getRefresh() . PHP_EOL;
echo 'getRetry = ' . $record->getRetry() . PHP_EOL;
echo 'getExpire = ' . $record->getExpire() . PHP_EOL;
echo 'getMinimumTtl = ' . $record->getMinimumTtl() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = SOA
getMasterNameServer = test.com
getRawEmailName = admin.test.com
getAdministratorEmailAddress = admin@test.com
getSerial = 123456789
getRefresh = 3600
getRetry = 10
getExpire = 1800
getMinimumTtl = 1200
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::SOA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SOA Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [serial] => 123456789
                    [retry] => 10
                    [mname] => test.com
                    [refresh] => 3600
                    [minimum-ttl] => 1200
                    [rname] => admin.test.com
                    [expire] => 1800
                    [type] => SOA
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('bluelibraries.com', RecordTypes::SOA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SOA Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [serial] => 123456789
                    [retry] => 10
                    [mname] => test.com
                    [refresh] => 3600
                    [minimum-ttl] => 1200
                    [rname] => admin.test.com
                    [expire] => 1800
                    [type] => SOA
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

$records = $dns->get('bluelibraries.com', RecordTypes::SOA);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\SOA Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [serial] => 123456789
                    [retry] => 10
                    [mname] => test.com
                    [refresh] => 3600
                    [minimum-ttl] => 1200
                    [rname] => admin.test.com
                    [expire] => 1800
                    [type] => SOA
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new SOA([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "bluelibraries.com 3600 IN SOA test.com admin.test.com 123456789 3600 10 1800 1200"
string2 = "bluelibraries.com 3600 IN SOA test.com admin.test.com 123456789 3600 10 1800 1200"
```

### Transform to JSON
```php
$record = new SOA([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"bluelibraries.com","ttl":3600,"serial":123456789,"retry":10,"mname":"test.com","refresh":3600,"minimum-ttl":1200,"rname":"admin.test.com","expire":1800,"class":"IN","type":"SOA"}
```

### Transform to Array
```php
$record = new SOA([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'serial' => 123456789,
	'retry' => 10,
	'mname' => "test.com",
	'refresh' => 3600,
	'minimum-ttl' => 1200,
	'rname' => "admin.test.com",
	'expire' => 1800
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => bluelibraries.com
    [ttl] => 3600
    [serial] => 123456789
    [retry] => 10
    [mname] => test.com
    [refresh] => 3600
    [minimum-ttl] => 1200
    [rname] => admin.test.com
    [expire] => 1800
    [class] => IN
    [type] => SOA
)
```
