<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM;
use PHPUnit\Framework\TestCase;

class NSEC3ParamTest extends TestCase
{

    protected NSEC3PARAM $subject;

    public function setUp(): void
    {
        $this->subject = new NSEC3PARAM([]);
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

    public function testGetIterations()
    {
        $this->assertNull($this->subject->getIterations());
    }

    public function testGetValueSetIterations()
    {
        $value = 3;
        $this->subject->setData(['iterations' => $value]);
        $this->assertSame($value, $this->subject->getIterations());
    }

    public function testGetSalt()
    {
        $this->assertNull($this->subject->getSalt());
    }

    public function testGetValueSetSalt()
    {
        $value = 'LofZcndFN2aVsd==';
        $this->subject->setData(['salt' => $value]);
        $this->assertSame($value, $this->subject->getSalt());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN NSEC3PARAM', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'       => 'test.com',
                'ttl'        => '3600',
                'value'      => 'value',
                'algorithm'  => 12,
                'flags'      => 255,
                'iterations' => 3,
                'salt'       => 'salt==',
            ]
        );
        $this->assertSame('test.com 3600 IN NSEC3PARAM 12 255 3 salt==', $this->subject->toString());
    }

}
