<?php

namespace BlueLibraries\Dns\Test\Unit\Records;

use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class RecordTypesTest extends TestCase
{

    public static function testGetNameInvalid()
    {
        static::assertNull(RecordTypes::getName(0));
    }

    public static function validTypesDataProvider(): array
    {
        return [
            [RecordTypes::A],
            [RecordTypes::CNAME],
            [RecordTypes::HINFO],
            [RecordTypes::CAA],
            [RecordTypes::MX],
            [RecordTypes::NS],
            [RecordTypes::PTR],
            [RecordTypes::SOA],
            [RecordTypes::TXT],
            [RecordTypes::AAAA],
            [RecordTypes::SRV],
            [RecordTypes::NAPTR],
            [RecordTypes::A6],
            [RecordTypes::ALL],
        ];
    }

    /**
     * @return void
     * @dataProvider validTypesDataProvider
     */
    public static function testGetNameValid(int $typeId)
    {
        static::assertIsString(RecordTypes::getName($typeId));
    }

    public static function testGetTypesNamesList()
    {
        static::assertIsArray(RecordTypes::getTypesNamesList());
    }

}

