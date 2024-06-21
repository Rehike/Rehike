<?php

namespace BlueLibraries\Dns\Test\Unit\Handlers;

use BlueLibraries\Dns\Handlers\DnsHandlerFactory;
use BlueLibraries\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Handlers\Types\Dig;
use BlueLibraries\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Handlers\Types\UDP;
use PHPUnit\Framework\TestCase;

class DnsHandlerFactoryTest extends TestCase
{

    private DnsHandlerFactory $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new DnsHandlerFactory();
    }

    public static function validHandlersDataProvider(): array
    {
        return [
            [DnsHandlerTypes::DNS_GET_RECORD, DnsGetRecord::class],
            [DnsHandlerTypes::DIG, Dig::class],
            [DnsHandlerTypes::TCP, TCP::class],
            [DnsHandlerTypes::UDP, UDP::class],
        ];
    }

    /**
     * @param string $handlerType
     * @param string $expectedClass
     * @dataProvider validHandlersDataProvider
     * @return void
     * @throws DnsHandlerFactoryException
     */
    public function testCreateValidHandlers(string $handlerType, string $expectedClass)
    {
        $this->assertSame($expectedClass, get_class($this->subject->create($handlerType)));
    }

    public function testCreateInvalidHandler()
    {
        $this->expectException(DnsHandlerFactoryException::class);
        $this->expectExceptionCode(DnsHandlerFactoryException::ERR_UNABLE_TO_CREATE_HANDLER_TYPE);
        $this->expectExceptionMessage('Unable to build handler type: "test"');
        $this->subject->create('test');
    }

}
