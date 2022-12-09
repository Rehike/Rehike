<?php
namespace Rehike\Util\Nameserver;

/**
 * Structure storing information used to override the nameserver.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NameserverInfo
{
    public string $domain;
    public string $ipAddress;

    public function __construct(string $domain, string $ipAddress)
    {
        $this->domain = $domain;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Serialize into a common nameserver resolution string.
     * 
     * Example:
     *      www.example.com:443:127.0.0.1
     */
    public function serialize(): string
    {
        return "$this->domain:$this->ipAddress";
    }
}