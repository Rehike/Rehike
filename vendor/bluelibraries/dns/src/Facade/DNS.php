<?php

namespace BlueLibraries\Dns\Facade;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerFactory;
use BlueLibraries\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\Dns\Records\RecordException;

class DNS
{
    private static ?DnsHandlerFactory $dnsHandlerFactory = null;

    private static function getHandlerFactory(): DnsHandlerFactory
    {
        if (is_null(self::$dnsHandlerFactory)) {
            self::$dnsHandlerFactory = new DnsHandlerFactory();
        }
        return self::$dnsHandlerFactory;
    }

    /**
     * @param string $host
     * @param int|int[] $type
     * @param string|null $handlerType
     * @param bool|null $useExtendedRecords
     * @param string|null $nameserver
     * @return array
     * @throws DnsHandlerException
     * @throws DnsHandlerFactoryException
     * @throws RecordException
     * @throws RawDataException
     */
    public static function getRecords(
        string  $host,
                $type,
        ?string $handlerType = DnsHandlerTypes::TCP,
        ?bool   $useExtendedRecords = true,
        ?string $nameserver = null): array
    {
        $dnsHandler = self::getHandlerFactory()
            ->create($handlerType);

        if (!is_null($nameserver)) {
            $dnsHandler->setNameserver($nameserver);
        }

        return (new DnsRecords())
            ->setHandler(
                $dnsHandler
            )
            ->get($host, $type, $useExtendedRecords);
    }

}
