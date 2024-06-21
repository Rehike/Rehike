<?php

namespace BlueLibraries\Dns\Test\Unit\Records;

use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordFactory;
use BlueLibraries\Dns\Records\RecordInterface;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class RecordFactoryTest extends TestCase
{

    protected RecordFactory $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new RecordFactory();
    }

    public static function allRecordTypesFormattedClassesDataProvider(): array
    {
        return require dirname(__FILE__) . "/../Data/allRecordsTypesFormattedClasses.php";
    }

    /**
     * @return void
     * @dataProvider allRecordTypesFormattedClassesDataProvider
     * @throws RecordException
     */
    public function testCreateDefaultRecords(array $data, string $class, string $classExtended)
    {
        $record = $this->subject->create($data, false);
        $this->assertSame(get_class($record), $class);
        $this->assertSame($data, $record->toArray());
    }

    /**
     * @return void
     * @dataProvider allRecordTypesFormattedClassesDataProvider
     * @throws RecordException
     */
    public function testCreateExtendedRecords(array $data, string $class, string $classExtended)
    {
        $record = $this->subject->create($data, true);
        $this->assertSame(get_class($record), $classExtended);
        $this->assertSame($data, $record->toArray());
    }

    public function testCreateMissingRecordTypeThrowsException()
    {
        $this->expectException(RecordException::class);
        $this->expectExceptionCode(RecordException::UNABLE_TO_CREATE_RECORD);
        $this->expectExceptionMessage('Invalid record type for recordData: []');
        $this->subject->create([], false);
    }

    public function testCreateInvalidRecordTypeThrowsException()
    {
        $this->expectException(RecordException::class);
        $this->expectExceptionCode(RecordException::UNABLE_TO_CREATE_RECORD);
        $this->expectExceptionMessage('Invalid record type for recordData: {"type":"INVALID"}');
        $this->subject->create(['type' => 'INVALID'], false);
    }

    public static function implementedRecordTypesDataProvider(): array
    {
        return [
            ['A'],
            ['NS'],
            ['CNAME'],
            ['SOA'],
            ['PTR'],
            ['HINFO'],
            ['MX'],
            ['TXT'],
            ['AAAA'],
            ['SRV'],
            ['NAPTR'],
            ['DS'],
            ['RRSIG'],
            ['NSEC'],
            ['DNSKEY'],
            ['NSEC3PARAM'],
            ['CDS'],
            ['CDNSKEY'],
            ['TYPE65'],
            ['CAA'],
            ['SPF'],
        ];
    }

    /**
     * @param $typeName
     * @return void
     * @dataProvider implementedRecordTypesDataProvider
     * @throws RecordException
     */
    public function testImplementedRecordCreation($typeName)
    {
        $this->assertInstanceOf(
            RecordInterface::class,
            $this->subject->create(
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => $typeName,
                ],
                true
            )
        );
    }

    public static function notImplementedRecordTypesDataProvider(): array
    {
        return
            array_map(
                function ($item) {
                    return [$item];
                },
                array_diff(
                    RecordTypes::getTypesNamesList(),
                    array_map(
                        function ($item) {
                            return $item[0];
                        },
                        static::implementedRecordTypesDataProvider()
                    )));
    }

    /**
     * @param string $typeName
     * @return void
     * @throws RecordException
     * @dataProvider notImplementedRecordTypesDataProvider
     */
    public function testNotImplementedRecordCreation(string $typeName)
    {
        $this->assertNull(
            $this->subject->create(
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => $typeName,
                ],
                true
            ), "typeName:" . $typeName);
    }

}
