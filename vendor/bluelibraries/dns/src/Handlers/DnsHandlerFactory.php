<?php

namespace BlueLibraries\Dns\Handlers;

use BlueLibraries\Dns\Handlers\Types\Dig;
use BlueLibraries\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Handlers\Types\UDP;

class DnsHandlerFactory
{
    /**
     * @throws DnsHandlerFactoryException
     */
    public function create(string $handlerType): DnsHandlerInterface
    {
        switch ($handlerType) {

            case DnsHandlerTypes::DNS_GET_RECORD:
                return new DnsGetRecord();

            case DnsHandlerTypes::DIG:
                return new Dig();

            case DnsHandlerTypes::TCP:
                return new TCP();

            case DnsHandlerTypes::UDP:
                return new UDP();

            default:
                throw new DnsHandlerFactoryException(
                    'Unable to build handler type: ' . json_encode($handlerType),
                    DnsHandlerFactoryException::ERR_UNABLE_TO_CREATE_HANDLER_TYPE
                );

        }
    }
}
