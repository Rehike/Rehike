# NAPTR records

## Create

### Create from constructor
```php
$record = new NAPTR([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => "."
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getOrder = ' . $record->getOrder() . PHP_EOL;
echo 'getPreference = ' . $record->getPreference() . PHP_EOL;
echo 'getFlag = ' . $record->getFlag() . PHP_EOL;
echo 'getServices = ' . $record->getServices() . PHP_EOL;
echo 'getRegex = ' . $record->getRegex() . PHP_EOL;
echo 'getReplacement = ' . $record->getReplacement() . PHP_EOL;
```
```text
getHost = naptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NAPTR
getOrder = 100
getPreference = 10
getFlag = U
getServices = SIP+D2U
getRegex = !^.*$!sip:service@example.com!
getReplacement = .
```

### Create with a setter
```php
$record = new NAPTR();
                $record->setData([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => "."
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getOrder = ' . $record->getOrder() . PHP_EOL;
echo 'getPreference = ' . $record->getPreference() . PHP_EOL;
echo 'getFlag = ' . $record->getFlag() . PHP_EOL;
echo 'getServices = ' . $record->getServices() . PHP_EOL;
echo 'getRegex = ' . $record->getRegex() . PHP_EOL;
echo 'getReplacement = ' . $record->getReplacement() . PHP_EOL;
```
```text
getHost = naptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NAPTR
getOrder = 100
getPreference = 10
getFlag = U
getServices = SIP+D2U
getRegex = !^.*$!sip:service@example.com!
getReplacement = .
```

### Create from string
```php
$record = Record::fromString('naptr.bluelibraries.com 3600 IN NAPTR 100 10 "U" "SIP+D2U" "!^.*$!sip:service@example.com!" .');

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getOrder = ' . $record->getOrder() . PHP_EOL;
echo 'getPreference = ' . $record->getPreference() . PHP_EOL;
echo 'getFlag = ' . $record->getFlag() . PHP_EOL;
echo 'getServices = ' . $record->getServices() . PHP_EOL;
echo 'getRegex = ' . $record->getRegex() . PHP_EOL;
echo 'getReplacement = ' . $record->getReplacement() . PHP_EOL;
```
```text
getHost = naptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NAPTR
getOrder = 100
getPreference = 10
getFlag = U
getServices = SIP+D2U
getRegex = !^.*$!sip:service@example.com!
getReplacement =
```

### Create from initialized array
```php
$record = Record::fromNormalizedArray([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => ".",
	'type' => "NAPTR"
]);

echo 'getHost = ' . $record->getHost() . PHP_EOL;
echo 'getTtl = ' . $record->getTtl() . PHP_EOL;
echo 'getClass = ' . $record->getClass() . PHP_EOL;
echo 'getTypeName = ' . $record->getTypeName() . PHP_EOL;
echo 'getOrder = ' . $record->getOrder() . PHP_EOL;
echo 'getPreference = ' . $record->getPreference() . PHP_EOL;
echo 'getFlag = ' . $record->getFlag() . PHP_EOL;
echo 'getServices = ' . $record->getServices() . PHP_EOL;
echo 'getRegex = ' . $record->getRegex() . PHP_EOL;
echo 'getReplacement = ' . $record->getReplacement() . PHP_EOL;
```
```text
getHost = naptr.bluelibraries.com
getTtl = 3600
getClass = IN
getTypeName = NAPTR
getOrder = 100
getPreference = 10
getFlag = U
getServices = SIP+D2U
getRegex = !^.*$!sip:service@example.com!
getReplacement = .
```

## Retrieve from Internet

### Retrieve with helper
```php
$records = DNS::getRecords('naptr.bluelibraries.com', RecordTypes::NAPTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NAPTR Object
        (
            [data:protected] => Array
                (
                    [host] => naptr.bluelibraries.com
                    [ttl] => 3600
                    [order] => 100
                    [pref] => 10
                    [flag] => U
                    [services] => SIP+D2U
                    [regex] => !^.*$!sip:service@example.com!
                    [replacement] => .
                    [type] => NAPTR
                    [class] => IN
                )

        )

)
```

### Retrieve without helper
```php
$dns = new DnsRecords();
$records = $dns->get('naptr.bluelibraries.com', RecordTypes::NAPTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NAPTR Object
        (
            [data:protected] => Array
                (
                    [host] => naptr.bluelibraries.com
                    [ttl] => 3600
                    [order] => 100
                    [pref] => 10
                    [flag] => U
                    [services] => SIP+D2U
                    [regex] => !^.*$!sip:service@example.com!
                    [replacement] => .
                    [type] => NAPTR
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

$records = $dns->get('naptr.bluelibraries.com', RecordTypes::NAPTR);

print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NAPTR Object
        (
            [data:protected] => Array
                (
                    [host] => naptr.bluelibraries.com
                    [ttl] => 3600
                    [order] => 100
                    [pref] => 10
                    [flag] => U
                    [services] => SIP+D2U
                    [regex] => !^.*$!sip:service@example.com!
                    [replacement] => .
                    [type] => NAPTR
                    [class] => IN
                )

        )

)
```

## Transform

### Transform to String
```php
$record = new NAPTR([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => "."
]);

echo 'string1 = ' . json_encode($record->toString()) . PHP_EOL;
echo 'string2 = ' . json_encode((string)$record) . PHP_EOL;
```
```text
string1 = "naptr.bluelibraries.com 3600 IN NAPTR 100 10 \"U\" \"SIP+D2U\" \"!^.*$!sip:service@example.com!\" ."
string2 = "naptr.bluelibraries.com 3600 IN NAPTR 100 10 \"U\" \"SIP+D2U\" \"!^.*$!sip:service@example.com!\" ."
```

### Transform to JSON
```php
$record = new NAPTR([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => "."
]);

echo 'JSON = ' . json_encode($record) . PHP_EOL;
```
```text
JSON = {"host":"naptr.bluelibraries.com","ttl":3600,"order":100,"pref":10,"flag":"U","services":"SIP+D2U","regex":"!^.*$!sip:service@example.com!","replacement":".","class":"IN","type":"NAPTR"}
```

### Transform to Array
```php
$record = new NAPTR([
	'host' => "naptr.bluelibraries.com",
	'ttl' => 3600,
	'order' => 100,
	'pref' => 10,
	'flag' => "U",
	'services' => "SIP+D2U",
	'regex' => "!^.*$!sip:service@example.com!",
	'replacement' => "."
]);

print_r($record->toArray());
```
```text
Array
(
    [host] => naptr.bluelibraries.com
    [ttl] => 3600
    [order] => 100
    [pref] => 10
    [flag] => U
    [services] => SIP+D2U
    [regex] => !^.*$!sip:service@example.com!
    [replacement] => .
    [class] => IN
    [type] => NAPTR
)
```
