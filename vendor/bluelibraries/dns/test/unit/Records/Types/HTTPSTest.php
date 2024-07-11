<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\HTTPS;
use PHPUnit\Framework\TestCase;

class HTTPSTest extends TestCase
{

    protected HTTPS $subject;

    public function setUp(): void
    {
        $this->subject = new HTTPS([]);
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

    public function testGetSeparator()
    {
        $this->assertNull($this->subject->getSeparator());
    }

    public function testGetSeparatorValue()
    {
        $value = '\#';
        $this->subject->setData(['separator' => $value]);
        $this->assertSame($value, $this->subject->getSeparator());
    }

    public function testGetOriginalLength()
    {
        $this->assertNull($this->subject->getOriginalLength());
    }

    public function testGetOriginalLengthValue()
    {
        $value = 34;
        $this->subject->setData(['original-length' => $value]);
        $this->assertSame($value, $this->subject->getOriginalLength());
    }

    public function testGetData()
    {
        $this->assertNull($this->subject->getData());
    }

    public function testGetDataValue()
    {
        $value = '1000C0268330568332D3239';
        $this->subject->setData(['data' => $value]);
        $this->assertSame($value, $this->subject->getData());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN TYPE65', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'            => 'test.com',
                'ttl'             => 3600,
                'separator'       => '\#',
                'original-length' => 27,
                'data'            => '1000C0268330568332D3239AA',
            ]
        );
        $this->assertSame('test.com 3600 IN TYPE65 \# 27 1000C0268330568332D3239AA', $this->subject->toString());
    }

}
