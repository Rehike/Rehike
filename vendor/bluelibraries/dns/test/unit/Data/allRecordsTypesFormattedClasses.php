<?php

use BlueLibraries\Dns\Records\Types\A;
use BlueLibraries\Dns\Records\Types\AAAA;
use BlueLibraries\Dns\Records\Types\CNAME;
use BlueLibraries\Dns\Records\Types\DnsSec\CDNSKey;
use BlueLibraries\Dns\Records\Types\DnsSec\CDS;
use BlueLibraries\Dns\Records\Types\DnsSec\DNSKey;
use BlueLibraries\Dns\Records\Types\DnsSec\DS;
use BlueLibraries\Dns\Records\Types\DnsSec\NSEC;
use BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM;
use BlueLibraries\Dns\Records\Types\DnsSec\RRSIG;
use BlueLibraries\Dns\Records\Types\HINFO;
use BlueLibraries\Dns\Records\Types\HTTPS;
use BlueLibraries\Dns\Records\Types\MX;
use BlueLibraries\Dns\Records\Types\NAPTR;
use BlueLibraries\Dns\Records\Types\NS;
use BlueLibraries\Dns\Records\Types\PTR;
use BlueLibraries\Dns\Records\Types\SOA;
use BlueLibraries\Dns\Records\Types\SRV;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Records\Types\Txt\DKIM;
use BlueLibraries\Dns\Records\Types\Txt\DMARC;
use BlueLibraries\Dns\Records\Types\Txt\MtaSts;
use BlueLibraries\Dns\Records\Types\Txt\SPF;
use BlueLibraries\Dns\Records\Types\Txt\TLSReporting;

return [

    [
        [
            'host'  => '',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => '',
        ],
        TXT::class,
        TXT::class,
    ],
    [
        [
            'host'  => '@',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=spf1 include:_spf.test.com',
        ],
        TXT::class,
        TXT::class,
    ],

    [
        [
            'host'  => '$%#$%4',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=spf1 include:_spf.test.com',
        ],
        TXT::class,
        TXT::class,
    ],

    [
        [
            'host'  => 'test.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'A',
            'ip'    => '20.81.111.85',
        ],
        A::class,
        A::class,
    ],
    [
        [
            'host'   => 'test.com',
            'class'  => 'IN',
            'ttl'    => 0,
            'type'   => 'NS',
            'target' => 'ns4-39.azure-dns.info',
        ],
        NS::class,
        NS::class,
    ],
    [
        [
            'host'   => 'microsoft.com',
            'class'  => 'IN',
            'ttl'    => 0,
            'type'   => 'CNAME',
            'target' => 'microsoft.com',
        ],
        CNAME::class,
        CNAME::class,
    ],
    [
        [
            'host'   => 'microsoft.com',
            'class'  => 'IN',
            'ttl'    => 0,
            'type'   => 'MX',
            'pri'    => 10,
            'target' => 'microsoft-com.mail.protection.outlook.com',
        ],
        MX::class,
        MX::class,
    ],
    [
        [
            'host'        => 'microsoft.com',
            'ttl'         => '3600',
            'class'       => 'IN',
            'type'        => 'SOA',
            'mname'       => 'ns1-39.azure-dns.com',
            'rname'       => 'azuredns-hostmaster.microsoft.com',
            'serial'      => '1',
            'refresh'     => '3600',
            'retry'       => '300',
            'expire'      => '2419200',
            'minimum-ttl' => '300',
        ],
        SOA::class,
        SOA::class,
    ],
    [
        [
            'host'  => 'microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'google-site-verification=M--CVfn_YwsV-2FGbCp_HFaEj23BmT0cTF4l8hXgpvMt7sebee51jrj7vm932k531hipa8RPDXjBzBS9tu7Pbysu7qCACrwXPoDV8ZtLfthTnC4y9VJFLd84it5sQlEITgSLJ4KOIA8pBZxmyvPujuUvhOg==fg2t0gov9424p2tdcuo94goe9jd365mktkey=3uc1cf82cpv750lzk70v9bvf2hubspot-developer-verification=OTQ5NGIwYWEtODNmZi00YWE1LTkyNmQtNDhjMDMxY2JjNDAxd365mktkey=QDa792dLCZhvaAOOCe2Hz6WTzmTssOp1snABhxWibhMxgoogle-site-verification=pjPOauSPcrfXOZS9jnPPa5axowcHGCDAl1_86dCqFpkdocusign=d5a3737c-c23c-4bd0-9095-d2ff621f2840d365mktkey=SxDf1EZxLvMwx6eEZUxzjFFgHoapF8DvtWEUjwq7ZTwxd365mktkey=6358r1b7e13hox60tl1uagv14facebook-domain-verification=fwzwhbbzwmg5fzgotc2go51olc3566google-site-verification=GfDnTUdATPsK1230J0mXbfsYw-3A9BVMVaKSd4DcKgIgoogle-site-verification=uFg3wr5PWsK8lV029RoXXBBUW0_E6qf1WEWVHhetkOYv=spf1 include:_spf-a.microsoft.com include:_spf-b.microsoft.com include:_spf-c.microsoft.com include:_spf-ssg-a.msft.net include:spf-a.hotmail.com include:_spf1-meo.microsoft.com -alld365mktkey=j2qHWq9BHdaa3ZXZH8x64daJZxEWsFa0dxDeilxDoYYx',
        ],
        TXT::class,
        TXT::class,
    ],
    [
        [
            'host'  => 'microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'AAAA',
            'ipv6'  => '::ffff:1451:6f55',
        ],
        AAAA::class,
        AAAA::class,
    ],
    [
        [
            'host'     => 'microsoft.com',
            'class'    => 'IN',
            'ttl'      => 0,
            'type'     => 'HINFO',
            'hardware' => 'HC-85',
            'os'       => 'Win 95'
        ],
        HINFO::class,
        HINFO::class,
    ],
    [
        [
            'host'                 => 'microsoft.com',
            'class'                => 'IN',
            'ttl'                  => 0,
            'type'                 => 'RRSIG',
            'type-covered'         => 'A',
            'algorithm'            => 1,
            'labels-number'        => 2,
            'original-ttl'         => 3600,
            'signature-expiration' => 169254,
            'signature-creation'   => 169253,
            'key-tag'              => 49890,
            'signer-name'          => 'test.com',
            'signature'            => '==signature==',
        ],
        RRSIG::class,
        RRSIG::class,
    ],
    [
        [
            'host'       => 'microsoft.com',
            'class'      => 'IN',
            'ttl'        => 0,
            'type'       => 'DNSKEY',
            'value'      => 'value',
            'flags'      => 255,
            'protocol'   => 3,
            'algorithm'  => 12,
            'public-key' => 'public-key=='
        ],
        DNSKey::class,
        DNSKey::class,
    ],
    [
        [
            'host'       => 'microsoft.com',
            'class'      => 'IN',
            'ttl'        => 0,
            'type'       => 'CDNSKEY',
            'value'      => 'value',
            'flags'      => 257,
            'protocol'   => 3,
            'algorithm'  => 12,
            'public-key' => 'sec-public-key=='
        ],
        CDNSKey::class,
        CDNSKey::class,
    ],
    [
        [
            'host'             => 'microsoft.com',
            'class'            => 'IN',
            'ttl'              => 0,
            'type'             => 'DS',
            'key-tag'          => 2371,
            'algorithm'        => 13,
            'algorithm-digest' => 3,
            'digest'           => '1F987CC6583E92DF0890718C42'
        ],
        DS::class,
        DS::class,
    ],
    [
        [
            'host'             => 'test.com',
            'class'            => 'IN',
            'ttl'              => 0,
            'type'             => 'CDS',
            'key-tag'          => 2371,
            'algorithm'        => 12,
            'algorithm-digest' => 2,
            'digest'           => '1F987CC6583E92DF0890718C42'
        ],
        CDS::class,
        CDS::class,
    ],
    [
        [
            'host'                    => 'test.com',
            'class'                   => 'IN',
            'ttl'                     => 0,
            'type'                    => 'NSEC',
            'next-authoritative-name' => 'auth.test.com',
            'types'                   => 'A AAAA NS SOA TXT',
        ],
        NSEC::class,
        NSEC::class,
    ],
    [
        [
            'host'       => 'microsoft.com',
            'class'      => 'IN',
            'ttl'        => 0,
            'type'       => 'NSEC3PARAM',
            'value'      => 'value',
            'algorithm'  => 12,
            'flags'      => 255,
            'iterations' => 3,
            'salt'       => 'salt==',
        ],
        NSEC3PARAM::class,
        NSEC3PARAM::class,
    ],
    [
        [
            'host'   => 'test.com',
            'class'  => 'IN',
            'ttl'    => 0,
            'type'   => 'SRV',
            'pri'    => 1,
            'port'   => 10,
            'target' => '192.168.0.1',
            'weight' => 9,
        ],
        SRV::class,
        SRV::class,
    ],
    [
        [
            'host'            => 'test.com',
            'class'           => 'IN',
            'ttl'             => 0,
            'type'            => 'TYPE65',
            'separator'       => '\#',
            'original-length' => 27,
            'data'            => '1000C0268330568332D3239AA',
        ],
        HTTPS::class,
        HTTPS::class,
    ],
    [
        [
            'host'        => 'test.com',
            'class'       => 'IN',
            'ttl'         => 0,
            'type'        => 'NAPTR',
            'order'       => 100,
            'pref'        => 10,
            'flag'        => 'U',
            'services'    => 'SIP+D2U',
            'regex'       => '!^.*$!sip:service@example.com!',
            'replacement' => '.',
        ],
        NAPTR::class,
        NAPTR::class,
    ],

    [
        [
            'host'  => 'microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=spf1 include:_spf.test.com;',
        ],
        TXT::class,
        SPF::class,
    ],

    [
        [
            'host'  => 'test._domainkey.microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=DKIM1; p=publickey;h=a; g=oo;;',
        ],
        TXT::class,
        DKIM::class,
    ],

    [
        [
            'host'  => '_dmarc.microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com',
        ],
        TXT::class,
        DMARC::class,
    ],

    [
        [
            'host'  => '_smtp._tls.microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=TLSRPTv1; rua=mailto:tlsrpt@example.com',
        ],
        TXT::class,
        TLSReporting::class,
    ],

    [
        [
            'host'  => '_mta-sts.microsoft.com',
            'class' => 'IN',
            'ttl'   => 0,
            'type'  => 'TXT',
            'txt'   => 'v=STSv1; id=test4321',
        ],
        TXT::class,
        MtaSts::class,
    ],

    [
        [
            'host'   => 'ptr.bluelibraries.com',
            'class'  => 'IN',
            'ttl'    => 0,
            'type'   => 'PTR',
            'target' => '192.168.0.1'
        ],
        PTR::class,
        PTR::class,
    ],

];
