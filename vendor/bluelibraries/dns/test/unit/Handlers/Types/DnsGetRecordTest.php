<?php

namespace BlueLibraries\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DnsGetRecordTest extends TestCase
{
    /**
     * @var DnsGetRecord|MockObject
     */
    protected $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(DnsGetRecord::class)
            ->onlyMethods(['getDnsRecord'])
            ->getMock();
    }

    public function testGetDnsDataEmptyHostName()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname, it must not be empty!');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_EMPTY);
        $this->subject->getDnsData('', RecordTypes::ALL);
    }

    public function testGetDnsDataInvalidHostNameLength()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname "fo" length. It must be 3 or more!');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_LENGTH_TOO_SMALL);
        $this->subject->getDnsData('fo', RecordTypes::ALL);
    }

    public function testGetDnsDataInvalidHostNameBadCharacters()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname "ana*are*mere.com" format! (characters "A-Za-z0-9.-", max length 63 chars allowed)');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_FORMAT_INVALID);
        $this->subject->getDnsData('ana*are*mere.com', RecordTypes::ALL);
    }

    public function testGetDnsDataInvalidHostNameBadSpecialCharacters()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname "an\t\naaremere.com" format! (characters "A-Za-z0-9.-", max length 63 chars allowed)');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_FORMAT_INVALID);
        $this->subject->getDnsData("an\t\naaremere.com", RecordTypes::ALL);
    }

    public function testGetDnsDataHostNameFormatExceededLength()
    {
        $hostName = str_repeat('a', 250) . '.com';
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname "' . $hostName . '" length! (min 3, max 253 characters allowed)');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_LENGTH_INVALID);
        $this->subject->getDnsData($hostName, RecordTypes::ALL);
    }

    public function testGetDnsDataHostTLDExtensionFormatExceededLength()
    {
        $hostName = 'a.' . str_repeat('b', 64);
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid hostname "' . $hostName . '" format! (characters "A-Za-z0-9.-", max length 63 chars allowed)');
        $this->expectExceptionCode(DnsHandlerException::HOSTNAME_FORMAT_INVALID);
        $this->subject->getDnsData($hostName, RecordTypes::ALL);
    }

    public function testGetDnsDataInvalidTypeId()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Invalid records typeId: 0 host "test.com" !');
        $this->expectExceptionCode(DnsHandlerException::TYPE_ID_INVALID);
        $this->subject->getDnsData('test.com', 0);
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataEmptyData()
    {
        $this->setValueInGetDnsRecord([]);
        $this->assertSame([], $this->subject->getDnsData('test.com', RecordTypes::ALL));
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataValidData()
    {
        $value = [
            [
                'host'  => 'test.com',
                'class' => 'IN',
                'ttl'   => 0,
                'type'  => 'A',
                'ip'    => '20.81.111.85',
            ]
        ];
        $this->setValueInGetDnsRecord($value);
        $this->assertSame($value, $this->subject->getDnsData('test.com', RecordTypes::ALL));
    }

    public function testGetTimeout()
    {
        $this->assertSame(5, $this->subject->getTimeout());
    }

    public function testSetTimeout()
    {
        $this->subject->setTimeout(100);
        $this->assertSame(100, $this->subject->getTimeout());
    }

    public function testSetTimeoutSameObject()
    {
        $this->assertSame($this->subject, $this->subject->setTimeout(4));
    }

    public function testGetRetries()
    {
        $this->assertSame(2, $this->subject->getRetries());
    }

    public function testSetRetries()
    {
        $this->subject->setRetries(9);
        $this->assertSame(9, $this->subject->getRetries());
    }

    public function testSetRetriesSameObject()
    {
        $this->assertSame($this->subject, $this->subject->setRetries(3));
    }

    protected function setValueInGetDnsRecord($value)
    {
        $this->subject->method('getDnsRecord')
            ->willReturn($value);
    }

    public function testGetDnsRawNotFoundResultValue()
    {
        $this->setValueInGetDnsRecord(false);
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawNotFoundResultMakesOnlyOneCall()
    {
        $this->setValueInGetDnsRecord(false);
        $this->subject->expects(
            $this->once()
        )->method('getDnsRecord');
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawEmptyResultMaxRetries()
    {
        $this->setValueInGetDnsRecord([]);
        $this->subject->expects(
            $this->exactly($this->subject->getRetries() + 1)
        )->method('getDnsRecord');
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawResult()
    {
        $value = [
            [
                'host'  => 'test.com',
                'class' => 'IN',
                'ttl'   => 0,
                'type'  => 'A',
                'ip'    => '20.81.111.85',
            ]
        ];
        $this->setValueInGetDnsRecord($value);
        $this->assertSame($value, $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRecordInvalidValueExpectsError()
    {
        $subject = new DnsGetRecord();
        $this->assertSame([], $subject->getDnsRawResult('', RecordTypes::TXT));
    }

    public function testSetNameserverNullValueThrowsException()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!');
        $this->expectExceptionCode(DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER);
        $this->subject->setNameserver(null);
    }

    public function testSetNameserverStringValueThrowsException()
    {
        $this->expectException(DnsHandlerException::class);
        $this->expectExceptionMessage('Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!');
        $this->expectExceptionCode(DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER);
        $this->subject->setNameserver('8.8.8.8');
    }

    public function testGetUpdatedRecordsData()
    {
        $this->assertSame(
            [
                ['host' => 'test.com', 'class' => 'IN', 'type' => 'TXT', 'txt' => 'text test'],
                ['host' => 'test.com', 'class' => 'IN', 'type' => 'NAPTR', 'order' => 2, 'flag' => 3],
            ],
            $this->subject->getUpdatedRecordsData(
                [
                    ['host' => 'test.com', 'class' => 'IN', 'type' => 'TXT', 'txt' => 'text test'],
                    ['host' => 'test.com', 'class' => 'IN', 'type' => 'NAPTR', 'order' => 2, 'flags' => 3],
                ]
            ));
    }

}
