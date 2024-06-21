<?php

namespace BlueLibraries\Dns\Test\Unit\Handlers\Raw;

use BlueLibraries\Dns\Handlers\Raw\RawClassTypes;
use BlueLibraries\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\Dns\Records\DnsUtils;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class RawDataRequestTest extends TestCase
{

    private RawDataRequest $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new RawDataRequest();
    }

    public function testSetGetDomain()
    {
        $this->subject->setDomain('test.com');
        $this->assertSame('test.com', $this->subject->getDomain());
    }

    public function testSetGetTypeId()
    {
        $this->subject->setTypeId(RecordTypes::TXT);
        $this->assertSame(RecordTypes::TXT, $this->subject->getTypeId());
    }

    public function testSetGetTimeout()
    {
        $this->subject->setTimeout(5);
        $this->assertSame(5, $this->subject->getTimeout());
    }

    public function testSetGetId()
    {
        $this->subject->setId(14);
        $this->assertSame(14, $this->subject->getId());
    }

    public function testSetGetClassId()
    {
        $this->subject->setClassId(1);
        $this->assertSame(1, $this->subject->getClassId());
    }

    public function testSetGetIsRecursionDesired()
    {
        $this->subject->setIsRecursionDesired(true);
        $this->assertSame(true, $this->subject->isRecursionDesired());
    }

    public function testSetUseAuthoritativeAnswer()
    {
        $this->subject->setUseAuthoritativeAnswer(true);
        $this->assertSame(true, $this->subject->useAuthoritativeAnswer());
    }

    public function testSetUseTruncation()
    {
        $this->subject->setUseTruncation(false);
        $this->assertSame(false, $this->subject->useTruncation());
    }

    public function testSetUseRecursionIfAvailable()
    {
        $this->subject->setUseRecursionIfAvailable(true);
        $this->assertSame(true, $this->subject->useRecursionIfAvailable());
    }

    public static function generateHeaderDataProvider(): array
    {
        return [
            ['1.1.1.1', RecordTypes::A, RawClassTypes::IN, '01100100000071051104597100100114497114112971491491491490010100030'],
            ['test.com', RecordTypes::A, RawClassTypes::IN, '01100100000041161011151163991111090010100030'],
            ['test.com', RecordTypes::NS, RawClassTypes::IN, '01100100000041161011151163991111090020100030'],
            ['test.com', RecordTypes::TXT, RawClassTypes::IN, '011001000000411610111511639911110900160100030'],
            ['test.com', RecordTypes::RRSIG, RawClassTypes::IN, '011001000000411610111511639911110900460100030'],
            ['', RecordTypes::A, RawClassTypes::IN, '011001000000010100030'],
        ];
    }

    /**
     * @param string $domain
     * @param int $recordType
     * @param int $classTypeId
     * @param string $expected
     * @return void
     * @throws RawDataException
     * @dataProvider generateHeaderDataProvider
     */
    public function testGenerateHeader(string $domain, int $recordType, int $classTypeId, string $expected)
    {
        $this->subject->setDomain($domain);
        $this->subject->setTypeId($recordType);
        $this->subject->setClassId($classTypeId);
        $this->subject->setId(1);
        $this->assertSame($expected, DnsUtils::asciiString($this->subject->generateHeader()));
    }

    public function testGenerateHeaderInvalidDomain()
    {
        $this->expectException(RawDataException::class);
        $this->expectExceptionCode(RawDataException::ERR_INVALID_ADDRESS);
        $this->expectExceptionMessage('Invalid address, it must be an IP or domain, got:"test#"');
        $this->subject->setDomain('test#');
        $this->subject->setTypeId(RecordTypes::A);
        $this->subject->setClassId(RawClassTypes::IN);
        $this->subject->setId(1);
        $this->subject->generateHeader();
    }

    public function testGenerateHeaderInvalidClass()
    {
        $this->expectException(RawDataException::class);
        $this->expectExceptionCode(RawDataException::ERR_INVALID_CLASS_ID);
        $this->expectExceptionMessage('Invalid class Id, got:0');
        $this->subject->setDomain('test.com');
        $this->subject->setTypeId(RecordTypes::A);
        $this->subject->setClassId(0);
        $this->subject->setId(1);
        $this->subject->generateHeader();
    }

}
