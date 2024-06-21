<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types;

use BlueLibraries\Dns\Records\Types\SOA;
use PHPUnit\Framework\TestCase;

class SOATest extends TestCase
{

    protected SOA $subject;

    public function setUp(): void
    {
        $this->subject = new SOA([]);
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

    public function testGetGetMasterNameServer()
    {
        $this->assertNull($this->subject->getMasterNameServer());
    }

    public function testGetMasterNameServerValue()
    {
        $value = 'master.server.com';
        $this->subject->setData(['mname' => $value]);
        $this->assertSame($value, $this->subject->getMasterNameServer());
    }

    public function testGetRawEmailName()
    {
        $this->assertNull($this->subject->getRawEmailName());
    }

    public function testGetRawEmailNameValue()
    {
        $value = 'office.master.server.com';
        $this->subject->setData(['rname' => $value]);
        $this->assertSame($value, $this->subject->getRawEmailName());
    }

    public function testGetSerial()
    {
        $this->assertNull($this->subject->getSerial());
    }

    public function testGetSerialValue()
    {
        $value = '123456789';
        $this->subject->setData(['serial' => $value]);
        $this->assertSame((int)$value, $this->subject->getSerial());
    }

    public function testGetRefresh()
    {
        $this->assertNull($this->subject->getRefresh());
    }

    public function testGetRefreshValue()
    {
        $value = '18';
        $this->subject->setData(['refresh' => $value]);
        $this->assertSame((int)$value, $this->subject->getRefresh());
    }

    public function testGetRetry()
    {
        $this->assertNull($this->subject->getRefresh());
    }

    public function testGetRetryValue()
    {
        $value = '18';
        $this->subject->setData(['retry' => $value]);
        $this->assertSame((int)$value, $this->subject->getRetry());
    }

    public function testGetExpire()
    {
        $this->assertNull($this->subject->getExpire());
    }

    public function testGetExpireValue()
    {
        $value = '1822';
        $this->subject->setData(['expire' => $value]);
        $this->assertSame((int)$value, $this->subject->getExpire());
    }


    public function testGetMinimumTtl()
    {
        $this->assertNull($this->subject->getMinimumTtl());
    }

    public function testGetMinimumTtlValue()
    {
        $value = strval(time());
        $this->subject->setData(['minimum-ttl' => $value]);
        $this->assertSame((int)$value, $this->subject->getMinimumTtl());
    }

    public function testGetAdministratorEmailAddressMissingRnameValue()
    {
        $this->assertNull($this->subject->getAdministratorEmailAddress());
    }

    public static function getAdministratorEmailAddressDataProvider(): array
    {
        return [
            ['', null],
            [0, null],
            ['a.', null],
            ['aa.x', null],
            ['aa.xa', null],
            ['aa.x.a', 'aa@x.a'],
            ['admin.test.com', 'admin@test.com'],
            ['admin.test.mama.com', 'admin.test@mama.com'],
            ['admin.test.mama.omida.com', 'admin.test.mama@omida.com'],
        ];
    }

    /**
     * @param string $rawEmail
     * @param string|null $expected
     * @return void
     * @dataProvider getAdministratorEmailAddressDataProvider
     */
    public function testGetAdministratorEmailAddress($rawEmail, $expected)
    {
        $this->subject->setData(['rname' => $rawEmail]);
        $this->assertSame($expected, $this->subject->getAdministratorEmailAddress());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN SOA', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'        => 'test.com',
                'serial'      => 123456789,
                'retry'       => 10,
                'mname'       => 'test.com',
                'refresh'     => 3600,
                'minimum-ttl' => 1200,
                'rname'       => 'admin.test.com',
                'expire'      => 1800,
            ]
        );
        $this->assertSame('test.com 0 IN SOA test.com admin.test.com 123456789 3600 10 1800 1200', $this->subject->toString());
    }

}
