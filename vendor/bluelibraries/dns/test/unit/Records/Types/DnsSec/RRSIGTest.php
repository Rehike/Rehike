<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\Types\DnsSec\RRSIG;
use PHPUnit\Framework\TestCase;

class RRSIGTest extends TestCase
{

    protected RRSIG $subject;

    public function setUp(): void
    {
        $this->subject = new RRSIG([]);
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

    public function testGetTypeCovered()
    {
        $this->assertNull($this->subject->getTypeCovered());
    }

    public function testGetValueSetTypeCovered()
    {
        $value = 'A';
        $this->subject->setData(['type-covered' => $value]);
        $this->assertSame($value, $this->subject->getTypeCovered());
    }

    public function testGetAlgorithm()
    {
        $this->assertNull($this->subject->getAlgorithm());
    }

    public function testGetValueSetAlgorithm()
    {
        $value = 13;
        $this->subject->setData(['algorithm' => $value]);
        $this->assertSame($value, $this->subject->getAlgorithm());
    }

    public function testGetLabelsNumber()
    {
        $this->assertNull($this->subject->getLabelsNumber());
    }

    public function testGetValueSetLabelsNumber()
    {
        $value = 24;
        $this->subject->setData(['labels-number' => $value]);
        $this->assertSame($value, $this->subject->getLabelsNumber());
    }

    public function testGetOriginalTtl()
    {
        $this->assertNull($this->subject->getOriginalTtl());
    }

    public function testGetValueSetOriginalTtl()
    {
        $value = 24;
        $this->subject->setData(['original-ttl' => $value]);
        $this->assertSame($value, $this->subject->getOriginalTtl());
    }

    public function testGetExpiration()
    {
        $this->assertNull($this->subject->getExpiration());
    }

    public function testGetValueSetExpiration()
    {
        $value = 1820524;
        $this->subject->setData(['signature-expiration' => $value]);
        $this->assertSame($value, $this->subject->getExpiration());
    }

    public function testGetCreation()
    {
        $this->assertNull($this->subject->getCreation());
    }

    public function testGetValueSetCreation()
    {
        $value = 1810524;
        $this->subject->setData(['signature-creation' => $value]);
        $this->assertSame($value, $this->subject->getCreation());
    }

    public function testGetTag()
    {
        $this->assertNull($this->subject->getTag());
    }

    public function testGetValueSetTag()
    {
        $value = 20;
        $this->subject->setData(['key-tag' => $value]);
        $this->assertSame($value, $this->subject->getTag());
    }

    public function testGetSignerName()
    {
        $this->assertNull($this->subject->getSignerName());
    }

    public function testGetValueSetSignerName()
    {
        $value = 'test.com';
        $this->subject->setData(['signer-name' => $value]);
        $this->assertSame($value, $this->subject->getSignerName());
    }

    public function testGetSignature()
    {
        $this->assertNull($this->subject->getSignature());
    }

    public function testGetValueSetSignature()
    {
        $value = 'loremipsumdolorsitamet';
        $this->subject->setData(['signature' => $value]);
        $this->assertSame($value, $this->subject->getSignature());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN RRSIG', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'                 => 'test.com',
                'ttl'                  => '3600',
                'type-covered'         => 'A',
                'algorithm'            => 1,
                'labels-number'        => 2,
                'original-ttl'         => 3600,
                'signature-expiration' => 169254,
                'signature-creation'   => 169253,
                'key-tag'              => 49890,
                'signer-name'          => 'test.com',
                'signature'            => '==signature==',
            ]
        );
        $this->assertSame('test.com 3600 IN RRSIG A 1 2 3600 169254 169253 49890 test.com ==signature==', $this->subject->toString());
    }

}
