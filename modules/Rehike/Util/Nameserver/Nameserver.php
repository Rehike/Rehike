<?php
namespace Rehike\Util\Nameserver;

use function shell_exec;
use function filter_var;
use function call_user_func;
use const FILTER_VALIDATE_IP;
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;

// Imports for dns_get_record() approach:
// Yes, they really did go with such bad names for the constants.
use function dns_get_record;
use const DNS_CNAME;
use const DNS_A    as DNS_IPV4;
use const DNS_AAAA as DNS_IPV6;

/**
 * Utilities for DNS overriding.
 * 
 * It's actually surprisingly difficult to pull this off. Remember
 * that not all PHP installs support shell_exec(), different
 * techniques are completely different, and IPv6 is technically not
 * even backwards compatible with IPv4. It's a huge mess.
 * 
 * Since IPv6 is largely backwards compatible with IPv4 and most
 * servers support IPv4, IPv6 support is currently limited.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Nameserver
{
    public const DEFAULT_NS = "1.1.1.1";

    private function __construct() {}
    
    /**
     * Perform a lookup from a standard URL. This is the main API method.
     */
    public static function get(
            string $uri,
            string $nameserver = self::DEFAULT_NS,
            int $port = -1
    ): NameserverInfo
    {
        // If port is -1, then it is handled as an auto value.
        if (-1 == $port)
        {
            $port = self::guessPort($uri);
        }

        // Only the hostname may be used in a lookup, never the full
        // URI.
        $hostname = self::getHostName($uri);

        return self::lookup($hostname, $port);
    }

    /**
     * Try a number of DNS lookup strategies and attempt to determine
     * the IP that a URI resolves to.
     */
    public static function lookup(
            string $uri,
            int $port,
            string $lookupServer = self::DEFAULT_NS
    ): NameserverInfo
    {
        $strategies = [
            "lookupNative",
            "lookupViaShell"
        ];

        foreach ($strategies as $strategy)
        {
            try
            {
                return call_user_func(
                    [self::class, $strategy], 
                    $uri, 
                    $port,
                    $lookupServer
                );
            }
            catch (DnsLookupException $e) {} // Consume exception
        }

        // If we got here, all strategies have failed, so throw another
        // exception.
        throw new DnsLookupException($uri, $lookupServer);
    }

    /**
     * Lookup the IP address of a server natively.
     * 
     * This uses PHP's native DNS library to perform the lookup. This
     * approach is preferred over the shell_exec method, however it is
     * not guaranteed to work identically.
     */
    public static function lookupNative(
            string $uri, 
            int $port,
            string $lookupServer
    ): NameserverInfo
    {
        // Passed by reference. This needs to be a variable
        // AND an array!
        $ns = [$lookupServer];

        $cname = dns_get_record($uri, DNS_CNAME, $ns);

        $requestUri = $uri;

        if (isset($cname[0]["target"]))
        {
            $requestUri = $cname[0]["target"];
        }
        else
        {
            throw new DnsLookupException($uri, $lookupServer);
        }

        $records = 
            dns_get_record($requestUri, DNS_IPV4, $ns) ??
            dns_get_record($requestUri, DNS_IPV6, $ns);

        if (!empty($records))
        {
            foreach ($records as $record) if (self::isValidIp(@$record["ip"]))
            {
                return new NameserverInfo("$uri:$port", $record["ip"]);
            }
        }
        else
        {
            throw new DnsLookupException($uri, $lookupServer);
        }
    }

    /**
     * Lookup the IP address of a server using a command on the OS.
     * 
     * This strategy relies on PHP being able to interface with the shell
     * and the operating system providing the nslookup command.
     * 
     * Since neither of these are guaranteed, this function should rarely
     * be used. 
     */
    public static function lookupViaShell(
            string $uri, 
            int $port,
            string $lookupServer
    ): NameserverInfo
    {
        $rawResult = shell_exec("nslookup $uri $lookupServer");
        $results = explode(" ", $rawResult);

        foreach ($results as $result)
        {
            // Strip whitespace since it will mess up the validation.
            $result = preg_replace("/\s+/", "", $result);

            if (self::isValidIp($result))
            {
                return new NameserverInfo("$uri:$port", $result);
            }
        }

        throw new DnsLookupException($uri, $lookupServer);
    }

    /**
     * Guess the port of a URI.
     * 
     * This is easy because the default ports of HTTP and HTTPS are,
     * respectively, 80 and 443. Any other port needs to be manually
     * specified in the URI.
     */
    public static function guessPort(string $uri): int
    {
        if (strpos($uri, "://") > 0)
        {
            $protocol = explode("://", $uri)[0];
        }
        else
        {
            return 80; // Default HTTP port
        }

        switch ($protocol)
        {
            case "https":
            case "wss":
                // Secure HTTP and WebSocket protocols both operate
                // by default on port 443.
                return 443;
            case "http":
            case "ws":
            default:
                // HTTP and WebSocket both operate by default on port 80.
                return 80;
        }
    }

    /**
     * Check if an IP address is valid.
     */
    public static function isValidIp(string $ip): bool
    {
        return (bool)(
            filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) //||
            //filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
        );
    }

    /**
     * Get the hostname of a URI.
     * 
     * This strips the protocol and path of a URL, leaving only the
     * hostname.
     * 
     * For example:
     *     https://www.google.com/search?q=hello
     * becomes:
     *     www.google.com
     */
    public static function getHostName(string $uri): string
    {
        return preg_replace("/([A-Za-z]+:\/\/)|(\/.*)/", "", $uri);
    }
}