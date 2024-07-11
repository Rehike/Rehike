<?php

namespace BlueLibraries\Dns\Records;

class RecordTypes
{

    public const ALL = 255;
    public const A = 1;
    public const NS = 2;

    /** @deprecated Use MX instead */
    const MD = 3;
    /** @deprecated Use MX instead */
    const MF = 4;

    public const CNAME = 5;
    public const SOA = 6;

    /** @meta Experimental */
    public const MB = 7;
    public const MG = 8;
    public const MR = 9;

    /** @meta Experimental */
    public const NULL = 10;

    public const WKS = 11;
    public const PTR = 12;
    public const HINFO = 13;
    public const MINFO = 14;
    public const MX = 15;
    public const TXT = 16;
    public const RP = 17;
    public const AFSDB = 18;
    public const X25 = 19;
    public const ISDN = 20;
    public const RT = 21;
    public const NSAP = 22;
    public const NSAP_PTR = 23;
    public const SIG = 24;
    public const KEY = 25;
    public const PX = 26;
    public const GPOS = 27;
    public const AAAA = 28;
    public const LOC = 29;

    /**
     * @deprecated OBSOLETE
     */
    public const NXT = 30;

    public const EID = 31;
    public const NIMLOC = 32;
    public const SRV = 33;
    public const ATMA = 34;
    public const NAPTR = 35;
    public const KX = 36;
    public const CERT = 37;

    /**
     * @deprecated OBSOLETE, use AAAA instead
     */
    public const A6 = 38;
    public const DNAME = 39;
    public const SINK = 40;
    public const OPT = 41;
    public const APL = 42;
    public const DS = 43;
    public const SSHFP = 44;
    public const IPSECKEY = 45;
    public const RRSIG = 46;
    public const NSEC = 47;
    public const DNSKEY = 48;
    public const DHCID = 49;
    public const NSEC3 = 50;
    public const NSEC3PARAM = 51;
    public const TLSA = 52;
    public const SMIMEA = 53;
    // 54 is unassigned at this moment
    public const HIP = 55;
    public const NINFO = 56;
    public const RKEY = 57;
    public const TALINK = 58;
    public const CDS = 59;
    public const CDNSKEY = 60;
    public const OPENPGPKEY = 61;
    public const CSYNC = 62;
    public const ZONEMD = 63;
    public const SVCB = 64;
    public const HTTPS = 65;
    public const TKEY = 249;
    public const TSIG = 250;
    public const IXFR = 251;
    public const AXFR = 252;
    public const MAILB = 253;
    public const MAILA = 254;
    public const URI = 256;
    public const CAA = 257;
    public const AVC = 258;
    public const DOA = 259;
    public const AMTRELAY = 260;
    public const TA = 32768;
    public const DLV = 32769;

    /**
     * @deprecated OBSOLETE, use SPF instead (TXT record)
     */
    public const DEPRECATED_SPF = 99;

    /**
     * @deprecated, not a standard, yet
     */
    public const TYPE_65 = -1;

    private static array $all = [
        self::ALL         => 'ANY',
        self::A           => 'A',
        self::NS          => 'NS',
        self::MD          => 'MD',
        self::MF          => 'MF',
        self::CNAME       => 'CNAME',
        self::SOA         => 'SOA',
        self::MB          => 'MB',
        self::MG          => 'MG',
        self::MR          => 'MR',
        self::NULL        => 'NULL',
        self::WKS         => 'WKS',
        self::PTR         => 'PTR',
        self::HINFO       => 'HINFO',
        self::MINFO       => 'MINFO',
        self::MX          => 'MX',
        self::TXT         => 'TXT',
        self::RP          => 'RP',
        self::AFSDB       => 'AFSDB',
        self::X25         => 'X25',
        self::ISDN        => 'ISDN',
        self::RT          => 'RT',
        self::NSAP        => 'NSAP',
        self::NSAP_PTR    => 'NSAP-PTR',
        self::SIG         => 'SIG',
        self::KEY         => 'KEY',
        self::PX          => 'PX',
        self::GPOS        => 'GPOS',
        self::AAAA        => 'AAAA',
        self::LOC         => 'LOC',
        self::NXT         => 'NXT',
        self::EID         => 'EID',
        self::NIMLOC      => 'NIMLOC',
        self::SRV         => 'SRV',
        self::ATMA        => 'ATMA',
        self::NAPTR       => 'NAPTR',
        self::KX          => 'KX',
        self::CERT        => 'CERT',
        self::A6          => 'A6',
        self::DNAME       => 'DNAME',
        self::SINK        => 'SINK',
        self::OPT        => 'OPT',
        self::APL        => 'APL',
        self::DS         => 'DS',
        self::SSHFP      => 'SSHFP',
        self::IPSECKEY   => 'IPSECKEY',
        self::RRSIG      => 'RRSIG',
        self::NSEC       => 'NSEC',
        self::DNSKEY     => 'DNSKEY',
        self::DHCID      => 'DHCID',
        self::NSEC3      => 'NSEC3',
        self::NSEC3PARAM => 'NSEC3PARAM',
        self::TLSA       => 'TLSA',
        self::SMIMEA     => 'SMIMEA',
        self::HIP        => 'HIP',
        self::NINFO      => 'NINFO',
        self::RKEY       => 'RKEY',
        self::TALINK     => 'TALINK',
        self::CDS        => 'CDS',
        self::CDNSKEY    => 'CDNSKEY',
        self::OPENPGPKEY => 'OPENPGPKEY',
        self::CSYNC      => 'CSYNC',
        self::ZONEMD      => 'ZONEMD',
        self::SVCB        => 'TYPE64',
        self::HTTPS       => 'TYPE65',
        self::TKEY        => 'TKEY',
        self::TSIG        => 'TSIG',
        self::IXFR        => 'IXFR',
        self::AXFR        => 'AXFR',
        self::MAILB       => 'MAILB',
        self::MAILA       => 'MAILA',
        self::URI         => 'URI',
        self::CAA         => 'CAA',
        self::AVC         => 'AVC',
        self::DOA         => 'DOA',
        self::AMTRELAY    => 'AMTRELAY',
        self::TA          => 'TA',
        self::DLV         => 'DLV',

        self::DEPRECATED_SPF => 'SPF',

    ];

    private static array $types = [];

    public static function getName(int $type): ?string
    {
        return static::$all[$type] ?? null;
    }

    public static function getType(string $name): ?int
    {
        self::initTypesIfNeeded();
        return self::$types[$name] ?? null;
    }

    public static function isValidTypeId(int $typeId): bool
    {
        return isset(self::$all[$typeId]);
    }

    /**
     * @return void
     */
    private static function initTypesIfNeeded(): void
    {
        if (empty(static::$types)) {
            static::$types = array_flip(static::$all);
        }
    }

    public static function getTypesNamesList(): array
    {
        return array_values(self::$all);
    }

}
