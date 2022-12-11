<?php
namespace YukisCoffee\CoffeeRequest\Exception;

/**
 * Used by the Nameserver module when an error is encountered during
 * DNS lookup.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NameserverDnsLookupException extends BaseException
{
    public function __construct(string $uri, string $lookupServer)
    {
        parent::__construct(
            "Failed to get DNS records for $uri using server $lookupServer"
        );
    }
}