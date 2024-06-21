<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\CNAME;
use PHPUnit\Framework\TestCase;

class CNAMETest extends TestCase
{
    protected CNAME $subject;

    public function setUp(): void
    {
        $this->subject = new CNAME([]);
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
        $this->assertNull($this->subject->getTarget());
    }

    public function testGetTargetValue()
    {
        $value = 'test.target.com';
        $this->subject->setData(['target' => $value]);
        $this->assertSame($value, $this->subject->getTarget());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN CNAME', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host' => 'test.com',
                'target' => 'target.test.com'
            ]
        );
        $this->assertSame('test.com 0 IN CNAME target.test.com', $this->subject->toString());
    }

}
