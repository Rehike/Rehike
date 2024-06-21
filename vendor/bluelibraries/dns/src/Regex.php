<?php

namespace BlueLibraries\Dns;

class Regex
{
    public const DOMAIN_OR_SUBDOMAIN = '/^(([\w\d\_\-]+){1,63}\.)+(\w+){2,63}$/i';
    public const HOSTNAME_LENGTH = '/^.{3,253}$/';

    public const SPF_VALIDATION = '/^v=spf1 ([a-z0-9:.\/ ~\-_\+]+)/i';

    public const DKIM_SELECTOR_VALUE = '/^([\w\_]+)\._domainkey.*/';
    public const DKIM = '/^v=DKIM1;([a-z0-9; =]+)p=([a-zA-Z0-9\/+]+)/i';
    public const DKIM_HOSTNAME = '/([a-z0-9_.\-]+)\._domainkey/i';

    public const DMARC_HOSTNAME = '/^_dmarc\.([a-z0-9_.\-]+)$/i';
    public const DMARC = '/^v=DMARC1?;([a-z0-9;\\ =:@_.]+)$/i';

    public const DIG_COMMAND = '/dig \+nocmd( \+bufsize=1024)? \+noall \+noauthority \+answer \+nomultiline \+tries=\d+ \+time=\d+ ([a-z0-9.\-_]+) ([A-Z0-9-]{1,12})( @\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})?$/i';

    public const TLS_REPORTING = '/^v=TLSRPTv1; rua=mailto:([a-z.\-_@]+)((,mailto\:([a-z.\-_@]+))+)?$/i';
    public const TLS_REPORTING_HOSTNAME = '/^\_smtp\._tls\.([a-z0-9_.\-]+)$/i';

    public const MTA_STS_RECORD = '/^v=STSv1; id=([a-z0-9]+){1,32}$/i';
    public const MTA_STS_HOSTNAME = '/^\_mta\-sts\.([a-z0-9_.\-]+)$/i';

    public const TRIM_LENGTH_START = '/^(%s){1,%d}/';
    public const TRIM_LENGTH_END = '/(%s){1,%d}$/';

    public const SEPARATED_WORDS = '/\s+/';
    public const WORDS_SEPARATED_SPACE = '/[^\s]+/';
    public const TXT_VALUES = '/[^;? ]+\s?/i';
}
