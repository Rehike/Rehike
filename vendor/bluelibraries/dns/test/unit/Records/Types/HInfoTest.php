<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\HINFO;
use PHPUnit\Framework\TestCase;

class HInfoTest extends TestCase
{

    protected HINFO $subject;

    public function setUp(): void
    {
        $this->subject = new HINFO([]);
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

    public function testGetHardware()
    {
        $this->assertNull($this->subject->getHardware());
    }

    public function testGetHardwareValue()
    {
        $value = 'AMD K6 166 MHz';
        $this->subject->setData(['hardware' => $value]);
        $this->assertSame($value, $this->subject->getHardware());
    }

    public function testGetOperatingSystem()
    {
        $this->assertNull($this->subject->getOperatingSystem());
    }

    public function testGetOperatingSystemValue()
    {
        $value = 'Win 3.1';
        $this->subject->setData(['os' => $value]);
        $this->assertSame($value, $this->subject->getOperatingSystem());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN HINFO', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'   => 'test.com',
                'hardware' => 'Pentium 1',
                'os' => 'Win 95',
            ]
        );
        $this->assertSame('test.com 0 IN HINFO "Pentium 1" "Win 95"', $this->subject->toString());
    }

}
