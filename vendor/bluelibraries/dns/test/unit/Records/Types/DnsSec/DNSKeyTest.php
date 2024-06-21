<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\Types\DnsSec\DNSKey;
use PHPUnit\Framework\TestCase;

class DNSKeyTest extends TestCase
{

    protected DNSKey $subject;

    public function setUp(): void
    {
        $this->subject = new DNSKey([]);
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


    public function testGetFlags()
    {
        $this->assertNull($this->subject->getFlags());
    }

    public function testGetValueSetFlags()
    {
        $value = 257;
        $this->subject->setData(['flags' => $value]);
        $this->assertSame($value, $this->subject->getFlags());
    }

    public function testGetProtocol()
    {
        $this->assertNull($this->subject->getFlags());
    }

    public function testGetValueSetProtocol()
    {
        $value = 3;
        $this->subject->setData(['protocol' => $value]);
        $this->assertSame($value, $this->subject->getProtocol());
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

    public function testGetPublicKey()
    {
        $this->assertNull($this->subject->getPublicKey());
    }

    public function testGetValueSetPublicKey()
    {
        $value = 'LofZcndFN2aVd==';
        $this->subject->setData(['public-key' => $value]);
        $this->assertSame($value, $this->subject->getPublicKey());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN DNSKEY', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'       => 'test.com',
                'ttl'        => '3600',
                'value'      => 'value',
                'flags'      => 255,
                'protocol'   => 3,
                'algorithm'  => 12,
                'public-key' => 'public-key=='
            ]
        );
        $this->assertSame('test.com 3600 IN DNSKEY 255 3 12 public-key==', $this->subject->toString());
    }

}
