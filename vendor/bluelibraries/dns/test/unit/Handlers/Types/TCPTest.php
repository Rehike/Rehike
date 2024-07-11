<?php

namespace BlueLibraries\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TCPTest extends TestCase
{
    /**
     * @var TCP
     */
    protected TCP $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new TCP();
    }

    public function testSetPort()
    {
        $this->assertSame($this->subject, $this->subject->setPort(54));
    }

    public function testUnableTOWriteQuestionLengthToSocket()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET);
        $this->expectExceptionMessage('Failed to write question length to TCP socket');

        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('write')
            ->willReturn(null);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    public function testUnableTOReadSizeFromSocket()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_READ_SIZE_FROM_TCP_SOCKET);
        $this->expectExceptionMessage('Failed to read size from TCP socket');

        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('write')
            ->willReturn(1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testUnableToWriteQuestionToSocketNoRetries()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_TO_TCP_SOCKET);
        $this->expectExceptionMessage('Failed to write question to TCP socket');

        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturn('test');

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(1, null);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testUnableTOWriteSocketWithRetries()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionCode(DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET);
        $this->expectExceptionMessage('Failed to write question length to TCP socket');

        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls('test', null);

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(2, 2);

        $subject->setRetries(1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testUnableTOWriteSocketWithValidAnswerAfterRetry()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls(chr(0) . chr(42),
                base64_decode('hnKBgAABAAEAAAAABGFzdXMDY29tAAABAAHADAABAAEAADDvAARnCgTY'
                )
            );

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(0, 2, 2, 2, 0, 2);

        $subject->setRetries(2);

        $this->assertSame([
            [
                'host'  => 'asus.com',
                'ttl'   => 12527,
                'class' => 'IN',
                'type'  => 'A',
                'ip'    => '103.10.4.216',
            ]
        ],
            $subject->getDnsData('bluelibraries.com', RecordTypes::TXT)
        );
    }


    public function testGetDnsDataNull()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->onlyMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls('test', null);

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(2, 2);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

}
