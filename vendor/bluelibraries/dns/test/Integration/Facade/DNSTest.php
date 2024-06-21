<?php

namespace BlueLibraries\Dns\Test\Integration\Facade;

use BlueLibraries\Dns\Facade\DNS;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class DNSTest extends TestCase
{

    public static function getRecordsDataProvider(): array
    {
        return [
            ['', [], []],
            ['test.com', RecordTypes::TXT],
            ['google.com', [RecordTypes::A]],
            ['test.com', [RecordTypes::NS]],
        ];
    }

    /**
     * @param string $host
     * @param int|int[] $types
     * @return void
     * @throws DnsHandlerException
     * @throws DnsHandlerFactoryException
     * @throws RecordException
     * @dataProvider getRecordsDataProvider
     */
    public function testGetRecords(string $host, $types)
    {
        static::assertIsArray(DNS::getRecords($host, $types, DnsHandlerTypes::TCP, true, '8.8.8.8'));
    }

}
