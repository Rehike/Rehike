<?php
namespace Rehike\Util\Nameserver;

use Rehike\Exception\AbstractException;

/**
 * Used by the Nameserver module when an error is encountered during
 * DNS lookup.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class DnsLookupException extends AbstractException
{
    public function __construct(string $uri, string $lookupServer)
    {
        parent::__construct(
            "Failed to get DNS records for $uri using server $lookupServer"
        );
    }
}