<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\CAA;
use PHPUnit\Framework\TestCase;

class CAATest extends TestCase
{

    protected CAA $subject;

    public function setUp(): void
    {
        $this->subject = new CAA([]);
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

    public function testGetValue()
    {
        $this->assertNull($this->subject->getValue());
    }

    public function testGetValueSetValue()
    {
        $value = 'caa value';
        $this->subject->setData(['value' => $value]);
        $this->assertSame($value, $this->subject->getValue());
    }

    public function testGetFlag()
    {
        $this->assertNull($this->subject->getFlags());
    }

    public function testGetValueSetFlag()
    {
        $value = 1;
        $this->subject->setData(['flags' => $value]);
        $this->assertSame($value, $this->subject->getFlags());
    }

    public function testGetTag()
    {
        $this->assertNull($this->subject->getTag());
    }

    public function testGetTagSetValue()
    {
        $value = 'caa tag';
        $this->subject->setData(['tag' => $value]);
        $this->assertSame($value, $this->subject->getTag());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN CAA', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'  => 'test.com',
                'value' => 'value',
                'flags'  => 1,
                'tag'   => 'tag'
            ]
        );
        $this->assertSame('test.com 0 IN CAA 1 tag value', $this->subject->toString());
    }

}
