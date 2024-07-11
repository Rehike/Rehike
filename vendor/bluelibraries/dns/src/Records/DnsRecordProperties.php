<?php

namespace BlueLibraries\Dns\Records;

class DnsRecordProperties
{
    protected static array $defaultProperties = ['host', 'ttl', 'class', 'type'];

    protected static array $properties = [
        RecordTypes::A              => [
            'ip'
        ],
        RecordTypes::AAAA           => [
            'ipv6'
        ],
        RecordTypes::CAA            => [
            'flags',
            'tag',
            'value'
        ],
        RecordTypes::CNAME          => [
            'target'
        ],
        RecordTypes::SOA            => [
            'mname',
            'rname',
            'serial',
            'refresh',
            'retry',
            'expire',
            'minimum-ttl'
        ],
        RecordTypes::TXT            => [
            'txt',
        ],
        RecordTypes::DEPRECATED_SPF => [
            'txt',
        ],
        RecordTypes::NS             => [
            'target'
        ],
        RecordTypes::MX             => [
            'pri',
            'target'
        ],
        RecordTypes::PTR            => [
            'target'
        ],
        RecordTypes::SRV            => [
            'pri',
            'weight',
            'port',
            'target'
        ],
        RecordTypes::HINFO          => [
            'hardware',
            'os'
        ],
        RecordTypes::RRSIG          => [
            'type-covered',
            'algorithm', // int?
            'labels-number',
            'original-ttl',
            'signature-expiration',
            'signature-creation',
            'key-tag',
            'signer-name',
            'signature',
        ],

        RecordTypes::DNSKEY => [
            'flags',
            'protocol',
            'algorithm', // int?
            'public-key',
        ],

        RecordTypes::NSEC3PARAM => [
            'algorithm', // int?
            'flags',
            'iterations',
            'salt'
        ],

        RecordTypes::CDS => [
            'key-tag',
            'algorithm',
            'algorithm-digest',
            'digest',
        ],


        RecordTypes::DS => [
            'key-tag',
            'algorithm',
            'algorithm-digest',
            'digest',
        ],

        RecordTypes::CDNSKEY => [
            'flags',
            'protocol',
            'algorithm',
            'public-key'
        ],

        RecordTypes::NSEC => [
            'next-authoritative-name',
            'types',
        ],

        RecordTypes::HTTPS => [
            'separator',
            'original-length',
            'data',
        ],

        RecordTypes::NAPTR => [
            'order',
            'pref',
            'flag',
            'services',
            'regex',
            'replacement',
        ],

    ];

    protected static array $numberProperties = [
        'ttl',
        'minimum-ttl',
        'expire',
        'retry',
        'refresh',
        'port',
        'pri',
        'weight',
        'original-ttl',
        'signature-expiration',
        'signature-creation',
        'iterations',
        'flags',
        'algorithm',
        'key-tag',
        'algorithm-digest',
        'zone-key',
        'serial',
        'labels-number',
        'protocol',
        'original-length',
        'order',
        'pref',
    ];

    private static array $wrappedProperties = [
        'txt',
        'hardware',
        'os',
        'regex',
        'replacement',
        'flag',
        'services',
    ];

    private static array $loweredCaseProperties = [
        'host',
    ];

    private static array $unwrappedDotValues = [
        'regex',
        'replacement',
    ];

    protected static array $excludedBaseProperties = [
        'ttl',
        'entries',
    ];

    public static function getProperties(int $typeId): ?array
    {
        return self::$properties[$typeId] ?? null;
    }

    public static function getDefaultProperties(): array
    {
        return self::$defaultProperties;
    }

    public static function isNumberProperty(string $property): bool
    {
        return in_array($property, self::$numberProperties);
    }

    public static function isLoweredCaseProperty(string $property): bool
    {
        return in_array($property, self::$loweredCaseProperties);
    }

    public static function getRecordTypeProperties(int $typeId): array
    {
        return array_merge(self::$defaultProperties, static::getProperties($typeId) ?? []);
    }

    /**
     * @param int $typeId
     * @param array $data
     * @return array
     */
    public static function getFilteredProperties(int $typeId, array $data): array
    {
        return array_filter(
            self::getMappedProperties($data, $typeId),
            [DnsRecordProperties::class, 'filterExceptNumbers']
        );
    }

    public static function isUnWrappedDotValue($propertyName, $value): bool
    {
        return in_array($propertyName, self::$unwrappedDotValues) && $value === '.';
    }

    protected static function filterExceptNumbers($value): bool
    {
        return ($value !== null && $value !== false && $value !== '');
    }

    /**
     * @param array $data
     * @param int $typeId
     * @return array|string[]
     */
    private static function getMappedProperties(array $data, int $typeId): array
    {
        return array_map(
            function ($property) use ($data) {
                return $data[$property] ?? '';
            },
            DnsRecordProperties::getRecordTypeProperties($typeId)
        );
    }

    /**
     * @return array
     */
    public static function getExcludedBaseProperties(): array
    {
        return self::$excludedBaseProperties;
    }

    public static function isWrappedProperty(string $propertyName): bool
    {
        return in_array($propertyName, self::$wrappedProperties);
    }

}
