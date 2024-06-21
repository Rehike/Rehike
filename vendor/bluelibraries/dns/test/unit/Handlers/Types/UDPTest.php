<?php

namespace BlueLibraries\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\UDP;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UDPTest extends TestCase
{

    protected UDP $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new UDP();
    }

    public function testSetPort()
    {
        $this->assertSame($this->subject, $this->subject->setPort(54));
    }

    public function testGtPort()
    {
        $this->assertSame(53, $this->subject->getPort());
    }

    public function testUnableTOWriteQuestionLengthToSocket()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_WRITE_TO_UDP_SOCKET);
        $this->expectExceptionMessage('Failed to write question to UDP socket');

        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->onlyMethods(['read', 'write', 'close', 'getSocket'])
            ->getMock();

        $subject->method('getSocket')
            ->willReturn(false);

        $subject->method('write')
            ->willReturn(null);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    public function testUnableTOReadSizeFromSocket()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_READ_DATA_BUFFER);
        $this->expectExceptionMessage('Failed to read data buffer');

        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->onlyMethods(['read', 'write', 'close', 'getSocket'])
            ->getMock();

        $subject->method('write')
            ->willReturn(1);
        $subject->method('getSocket')
            ->willReturn(false);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    public function testGetDnsDataNull()
    {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->onlyMethods(['read', 'write', 'close', 'query'])
            ->getMock();

        $subject->method('query')
            ->willReturn(null);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testQuerySocketNull() {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->onlyMethods(['getSocket'])
            ->getMock();

        $subject->method('getSocket')
            ->willReturn(null);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

}
