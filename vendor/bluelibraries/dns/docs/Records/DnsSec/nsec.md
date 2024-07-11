# NSEC records

## Create

### Create from constructor
```php
$record = new NSEC([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getNextAuthoritativeName = ' . $record->getNextAuthoritativeName() . PHP_EOL;
echo 'getTypes = ' . $record->getTypes() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC
getNextAuthoritativeName = auth.bluelibraries.com
getTypes = A AAAA NS SOA
```

### Create with a setter
```php
$record = new NSEC();
                $record->setData([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getNextAuthoritativeName = ' . $record->getNextAuthoritativeName() . PHP_EOL;
echo 'getTypes = ' . $record->getTypes() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC
getNextAuthoritativeName = auth.bluelibraries.com
getTypes = A AAAA NS SOA
```

### Create from string
```php
$record = Record::fromString('bluelibraries.com 3600 IN NSEC auth.bluelibraries.com A AAAA NS SOA');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getNextAuthoritativeName = ' . $record->getNextAuthoritativeName() . PHP_EOL;
echo 'getTypes = ' . $record->getTypes() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC
getNextAuthoritativeName = auth.bluelibraries.com
getTypes = A AAAA NS SOA
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA",
	'type' => "NSEC"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getNextAuthoritativeName = ' . $record->getNextAuthoritativeName() . PHP_EOL;
echo 'getTypes = ' . $record->getTypes() . PHP_EOL;
```
```text
getHost = bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NSEC
getNextAuthoritativeName = auth.bluelibraries.com
getTypes = A AAAA NS SOA
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::NSEC);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [next-authoritative-name] => auth.bluelibraries.com
                    [types] => A AAAA NS SOA
                    [type] => NSEC
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('bluelibraries.com', RecordTypes::NSEC);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [next-authoritative-name] => auth.bluelibraries.com
                    [types] => A AAAA NS SOA
                    [type] => NSEC
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

$records = $dns->get('bluelibraries.com', RecordTypes::NSEC);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\DnsSec\NSEC Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [next-authoritative-name] => auth.bluelibraries.com
                    [types] => A AAAA NS SOA
                    [type] => NSEC
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new NSEC([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA"
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "bluelibraries.com 3600 IN NSEC auth.bluelibraries.com A AAAA NS SOA"
string2 = "bluelibraries.com 3600 IN NSEC auth.bluelibraries.com A AAAA NS SOA"
```

### Transform to JSON
```php
$record = new NSEC([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA"
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"bluelibraries.com","ttl":3600,"next-authoritative-name":"auth.bluelibraries.com","types":"A AAAA NS SOA","class":"IN","type":"NSEC"}
```

### Transform to Array
```php
$record = new NSEC([
	'host' => "bluelibraries.com",
	'ttl' => 3600,
	'next-authoritative-name' => "auth.bluelibraries.com",
	'types' => "A AAAA NS SOA"
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => bluelibraries.com
    [ttl] => 3600
    [next-authoritative-name] => auth.bluelibraries.com
    [types] => A AAAA NS SOA
    [class] => IN
    [type] => NSEC
)
```
