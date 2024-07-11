<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\Types\Txt\TLSReporting;
use PHPUnit\Framework\TestCase;

class TLSReportingTest extends TestCase
{

    protected TLSReporting $subject;

    public function setUp(): void
    {
        $this->subject = new TLSReporting([]);
        parent::setUp();
    }

    public function testGetTypeId()
    {
        $this->assertIsInt($this->subject->getTypeId());
    }

    public function testSetDataReturnsSameModel()
    {
        $this->assertSame(get_class($this->subject), get_class($this->subject->setData([])));
    }

    public function testGetHostDefaultNull()
    {
        $this->assertSame('', $this->subject->getHost());
    }

    public function testGetHostValue()
    {
        $value = 'test' . time() . '.com';
        $this->subject->setData(['host' => $value]);
        $this->assertSame($value, $this->subject->getHost());
    }

    public function testGetClass()
    {
        $this->assertSame('IN', $this->subject->getClass());
    }

    public function testGetClassValue()
    {
        $value = 'IN';
        $this->subject->setData(['class' => $value]);
        $this->assertSame($value, $this->subject->getClass());
    }

    public function testGetTtl()
    {
        $this->assertSame(0, $this->subject->getTtl());
    }

    public function testGetTtlValue()
    {
        $value = strval(time());
        $this->subject->setData(['ttl' => $value]);
        $this->assertSame((int)$value, $this->subject->getTtl());
    }

    public function testGetTxt()
    {
        $this->assertNull($this->subject->getTxt());
    }

    public function testGetIpValue()
    {
        $value = 'random text here';
        $this->subject->setData(['txt' => $value]);
        $this->assertSame($value, $this->subject->getTxt());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN TXT', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'ttl'  => 7200,
                'host' => 'test.com',
                'txt'  => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 IN TXT "text here"', $this->subject->toString());
    }

    public function testToStringCompleteWithChaosClass()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'CH',
                'host'  => 'test.com',
                'txt'   => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 CH TXT "text here"', $this->subject->toString());
    }

    public function testGetEmptyText()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => ''
            ]
        );
        $this->assertSame('test.com 7200 IN TXT ""', $this->subject->toString());
    }

    public function testGetExtendedTypeName()
    {
        $this->assertSame(ExtendedTxtRecords::TLS_REPORTING, $this->subject->getTypeName());
    }

    public static function parseValuesDataProvider(): array
    {
        return [
            ['', false],
            ['p', false],
            ['v=DMARC1; ', false],
            ['p=none', false],
            ['v=TLSRPTv1; ', false],
            ['v=TLSRPTv1; rua=', false],
            ['v=TLSRPTv;rua=mailto:test.com ', false],
            ['v=TLSRPTv1; rua=mailto:test.com', true]
        ];
    }

    /**
     * @param $txt
     * @param $expected
     * @dataProvider parseValuesDataProvider
     * @return void
     */
    public function testParseValues($txt, $expected)
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_smtp._tls.test.com',
                'txt'   => $txt
            ]
        );

        $this->assertSame($expected, $this->subject->parseValues());
    }

    public static function valuesDataProvider(): array
    {
        return [
            ['', []],
            ['p=reject; ', ['p' => 'reject']],
            ['v=TLSRPTv1; ', ['v' => 'TLSRPTv1']],
            ['v=TLSRPTv1; p=none', ['v' => 'TLSRPTv1', 'p' => 'none']],
            [
                'v=TLSRPTv1; rua=mailto:postmaster@test.com ',
                [
                    'v'   => 'TLSRPTv1',
                    'rua' => 'mailto:postmaster@test.com',
                ]],
        ];
    }

    private function getKeyValues(): array
    {
        return ['v', 'rua'];
    }

    /**
     * @param string $txt
     * @param array $expected
     * @dataProvider valuesDataProvider
     * @return void
     */
    public function testValues(string $txt, array $expected)
    {

        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_smtp._tls.test.com',
                'txt'   => $txt
            ]
        );

        $keyValues = $this->getKeyValues();

        foreach ($keyValues as $key) {
            $expectedValue = $expected[$key] ?? null;

            switch ($key) {

                case TLSReporting::VERSION:
                    $this->assertSame($expectedValue, $this->subject->getVersion());
                    break;

                case TLSReporting::RUA:
                    $this->assertSame($expectedValue, $this->subject->getRua());
                    break;

            }
        }
    }

}
