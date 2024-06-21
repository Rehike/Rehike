<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\Types\DnsSec\NSEC;
use PHPUnit\Framework\TestCase;

class NSECTest extends TestCase
{

    protected NSEC $subject;

    public function setUp(): void
    {
        $this->subject = new NSEC([]);
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

    public function testGetNextAuthoritativeName()
    {
        $this->assertNull($this->subject->getNextAuthoritativeName());
    }

    public function testGetValueSetNextAuthoritativeName()
    {
        $value = 'test.com';
        $this->subject->setData(['next-authoritative-name' => $value]);
        $this->assertSame($value, $this->subject->getNextAuthoritativeName());
    }

    public function testGetTypes()
    {
        $this->assertNull($this->subject->getTypes());
    }

    public function testGetValueSetTypes()
    {
        $value = 'A AAAA NS SOA TXT';
        $this->subject->setData(['types' => $value]);
        $this->assertSame($value, $this->subject->getTypes());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN NSEC', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'                    => 'test.com',
                'ttl'                     => '3600',
                'next-authoritative-name' => 'auth.test.com',
                'types'                   => 'A AAAA NS SOA',

            ]
        );
        $this->assertSame('test.com 3600 IN NSEC auth.test.com A AAAA NS SOA', $this->subject->toString());
    }

}
