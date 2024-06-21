<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\A;
use PHPUnit\Framework\TestCase;

class ATest extends TestCase
{

    protected A $subject;

    public function setUp(): void
    {
        $this->subject = new A([]);
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

    public function testGetTarget()
    {
        $this->assertNull($this->subject->getIp());
    }

    public function testGetIpValue()
    {
        $value = '192.168.0.1';
        $this->subject->setData(['ip' => $value]);
        $this->assertSame($value, $this->subject->getIp());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN A', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host' => 'test.com',
                'ip'   => '192.168.0.1'
            ]
        );
        $this->assertSame('test.com 0 IN A 192.168.0.1', $this->subject->toString());
    }

    public function testJson()
    {
        $this->assertSame(json_encode($this->subject->toArray()), json_encode($this->subject));
    }

}
