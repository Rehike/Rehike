# DNS

[![PHP-7.4 ](https://github.com/bluelibraries/dns/actions/workflows/build-7.4.yml/badge.svg)](https://github.com/bluelibraries/dns/actions/workflows/build-7.4.yml) 
[![PHP-8.0 ](https://github.com/bluelibraries/dns/actions/workflows/build-8.0.yml/badge.svg)](https://github.com/bluelibraries/dns/actions/workflows/build-8.0.yml) 
[![PHP-8.1 ](https://github.com/bluelibraries/dns/actions/workflows/build-8.1.yml/badge.svg)](https://github.com/bluelibraries/dns/actions/workflows/build-8.1.yml)
[![PHP-8.2 ](https://github.com/bluelibraries/dns/actions/workflows/build-8.2.yml/badge.svg)](https://github.com/bluelibraries/dns/actions/workflows/build-8.2.yml)
[![PHPUnit](https://github.com/bluelibraries/dns/actions/workflows/phpunit.yml/badge.svg)](https://github.com/bluelibraries/dns/actions/workflows/phpunit.yml)
[![codecov](https://codecov.io/gh/bluelibraries/dns/branch/main/graph/badge.svg?token=CQBMZ4EDED)](https://codecov.io/gh/bluelibraries/dns)

## Use certain DNS handler for DNS interrogation

## FOR PHP  >= 7.4 ONLY

## **For older PHP version** we strongly suggest **[bluelibraries/php5-dns](https://github.com/bluelibraries/php5-dns)**
 
## **[Demo](https://gethostinfo.com/records/)**

### Example:
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::ANY);
print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 21600
                    [class] => IN
                    [type] => NS
                    [target] => ns3.instradns.com
                )
        )
    [1] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 21600
                    [class] => IN
                    [type] => A
                    [ip] => 198.50.252.64
                )
        )
)
```

## Install via `composer`
```text
composer require bluelibraries/dns
```


### This package contains **4** types which can be used for DNS interrogations

1. **DnsGetRecord** based on `dns_get_record` PHP function
2. **Dig** based on `dig` shell command (better than `dns_get_record` and
   still secured)
3. **UDP** based on `raw` DNS calls using `UDP/socket` - useful for short
   answered queries as UDP answers might be limited to `512` bytes
4. **TCP** based on `raw` DNS calls
   using `TCP/socket` - <font style="color:#3399FF; font-size:16px;font-weight:bold">this the best</font> and is set
   as `default` handler

### Dns handlers comparison

| Feature                                                       | DNS_GET_RECORD | DIG     | UDP     | TCP     |
|---------------------------------------------------------------|----------------|---------|---------|---------|
| **Force timeout** limit                                       | NO             | **YES** | **YES** | **YES** |
| Detect **more record types** <br/>that are defined in **PHP** | NO             | **YES** | **YES** | **YES** |
| Use **custom nameserver**                                     | NO             | **YES** | **YES** | **YES** |
| Handle **large responses**                                    | **YES**        | **YES** | NO      | **YES** |
| No need for **extra modules/packages** for running            | **YES**        | NO      | **YES** | **YES** |

### Dns handlers custom settings
```php
// Let's customize the DNS request handler - TCP
$dnsHandler = (new TCP())
    ->setPort(53)
    ->setNameserver('8.8.8.8')
    ->setTimeout(3) // limit execution to 3 seconds
    ->setRetries(5); // allows 5 retries if response fails

// Let's initialize the DNS records service
$dnsRecordsService = new DnsRecords($dnsHandler);

// let's get some TXT records from `bluelibraries.com`
$records = $dnsRecordsService->get('bluelibraries.com', RecordTypes::TXT);

// let's display them
print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => TXT
                    [txt] => google-site-verification=kWtestq0tP8Ae_WJhRwUcZoqpdEkvuXJk
                )
        )
    [1] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => TXT
                    [txt] => 55d34914-636b-4x-b349-fdb9f2c1eaca
                )
        )
)
```
### Similar for UDP and DIG
```php
$dnsHandler = (new UDP())
    ->setPort(53)
    ->setNameserver('8.8.8.8')
    ->setTimeout(3) // limit execution to 3 seconds
    ->setRetries(5); // allows 5 retries if response fails

$dnsHandler = (new DIG())
    ->setPort(53)
    ->setNameserver('8.8.8.8')
    ->setTimeout(3) // limit execution to 3 seconds
    ->setRetries(5); // allows 5 retries if response fails
```

### DnsGetRecord - this handler has a limited number of settings
```php
// DnsGetRecord allows only Timeout and Retries, but there is no control over timeout
// so the timeout may be much longer than the limit we set!
$dnsHandler = (new DnsGetRecord())
    ->setTimeout(3) // limit execution to 3 seconds
    ->setRetries(5); // allows 5 retries if response fails
```
## Retrieve records examples, and more...
- [A](./docs/Records/a.md)
- [NS](./docs/Records/ns.md)
- [CNAME](./docs/Records/cname.md)
- [SOA](./docs/Records/soa.md)
- [PTR](./docs/Records/ptr.md)
- [HINFO](./docs/Records/hinfo.md)
- [MX](./docs/Records/mx.md)
- [TXT](./docs/Records/txt.md)
  - [SPF](./docs/Records/Txt/spf.md)
  - [DKIM](./docs/Records/Txt/dkim.md)
  - [DMARC](./docs/Records/Txt/dmarc.md)
  - [MtaSts](./docs/Records/Txt/mta-sts.md)
  - [TlsReporting](./docs/Records/Txt/tls-reporting.md)
  - [DomainVerification](./docs/Records/Txt/domain-verification.md)
- [AAAA](./docs/Records/aaaa.md)
- [SRV](./docs/Records/srv.md)
- [NAPTR](./docs/Records/naptr.md)
- [DS](./docs/Records/DnsSec/ds.md)
- [RRSIG](./docs/Records/DnsSec/rrsig.md)
- [NSEC](./docs/Records/DnsSec/nsec.md)
- [DNSKEY](./docs/Records/DnsSec/dnskey.md)
- [NSEC3PARAM](./docs/Records/DnsSec/nsec3param.md)
- [CDS](./docs/Records/DnsSec/cds.md)
- [CDNSKEY](./docs/Records/DnsSec/cdnskey.md)
- [HTTPS](./docs/Records/https.md)
- [CAA](./docs/Records/caa.md)


### Retrieve records using `dns_get_record`

```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::TXT, DnsHandlerTypes::DNS_GET_RECORD);
print_r($records);
```

```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [class] => IN
                    [ttl] => 0
                    [type] => TXT
                    [txt] => google-site-verification=test-636b-4a56-b349-test
                )
        )
)
```

### Retrieve records using `dig`

```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::TXT, DnsHandlerTypes::DIG);
print_r($records);
```

```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [class] => IN
                    [ttl] => 0
                    [type] => TXT
                    [txt] => google-site-verification=test-636b-4a56-b349-test
                )
        )
)
```

### Retrieve records using `UDP`

```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::TXT, DnsHandlerTypes::UDP);
print_r($records);
```

```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [class] => IN
                    [ttl] => 0
                    [type] => TXT
                    [txt] => google-site-verification=test-636b-4a56-b349-test
                )
        )
)
```

### Retrieve records using `TCP`

```php
// TCP is the default DNS handler, so if you are using it then you can skip it
$records = DNS::getRecords('bluelibraries.com', RecordTypes::TXT);
print_r($records);
```

```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [class] => IN
                    [ttl] => 0
                    [type] => TXT
                    [txt] => google-site-verification=test-636b-4a56-b349-test
                )
        )
)
```

### Retrieve TXT records

```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::TXT);
print_r($records);
```

```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3454
                    [class] => IN
                    [type] => TXT
                    [txt] => google-site-verification=kW9t2V_S7WjOX57zq0tP8Ae_WJhRwUcZoqpdEkvuXJk
                )
        )
    [1] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3454
                    [class] => IN
                    [type] => TXT
                    [txt] => 55d14914-636b-4a56-b349-fdb9f2c1eaca
                )
        )
)
```

### Retrieve A (address) records

```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::A);
print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => A
                    [ip] => 67.225.146.248
                )
        )
)
```


### Retrieve ALL records
```php
$records = DNS::getRecords('bluelibraries.com', RecordTypes::ALL, DnsHandlerTypes::DIG);
print_r($records);
```
```text
Array
(
    [0] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => NS
                    [target] => ns2.teestbluelibraries.com
                )
        )
    [1] => BlueLibraries\Dns\Records\Types\Txt\DomainVerification Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => TXT
                    [txt] => google-site-verification=errre
                )
        )
    [2] => BlueLibraries\Dns\Records\Types\NS Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => NS
                    [target] => tst3.bluelibraries.com
                )
        )
    [3] => BlueLibraries\Dns\Records\Types\TXT Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => TXT
                    [txt] => 55d34914-636b-4tes-b349-fdb9f2c1eaca
                )
        )
    [4] => BlueLibraries\Dns\Records\Types\A Object
        (
            [data:protected] => Array
                (
                    [host] => bluelibraries.com
                    [ttl] => 3600
                    [class] => IN
                    [type] => A
                    [ip] => 67.225.146.248
                )
        )
)
```

