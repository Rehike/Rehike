<?php

namespace BlueLibraries\Dns\Handlers;

class DnsHandlerTypes
{
    public const DNS_GET_RECORD = 'dnsGetRecord'; // PHP internal function
    public const DIG = 'dig'; // dig command if available on local machine
    public const TCP = 'tcp'; // direct TCP connection to a DNS server
    public const UDP = 'udp'; // direct UDP connection to a DNS server

    protected static array $all = [
        self::TCP,
        self::DNS_GET_RECORD,
        self::DIG,
        self::UDP,
    ];

    public static function getAll(): array
    {
        return self::$all;
    }

}
