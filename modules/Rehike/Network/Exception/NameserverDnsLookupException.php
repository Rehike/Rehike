<?php
namespace Rehike\Network\Exception;

use Exception;

/**
 * Used by the Nameserver module when an error is encountered during
 * DNS lookup.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NameserverDnsLookupException extends Exception
{
    public function __construct(string $uri, string $lookupServer)
    {
        parent::__construct(
            "Failed to get DNS records for $uri using server $lookupServer"
        );
    }
}