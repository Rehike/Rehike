<?php

namespace BlueLibraries\Dns\Test\Unit\Records;

use BlueLibraries\Dns\Records\RecordTypes;
use BlueLibraries\Dns\Records\StringRecordUtils;
use PHPUnit\Framework\TestCase;

class StringRecordUtilsTest extends TestCase
{

    public function testLineToArrayEmptyLine()
    {
        static::assertSame([], StringRecordUtils::lineToArray('', 1));
    }

    public function testLineToArraySingleLine()
    {
        static::assertSame(['Ana are mere'], StringRecordUtils::lineToArray("Ana are \n mere", 1));
    }

    public function testLineToArrayMultilineLastLineKeepOtherData()
    {
        static::assertSame(['Ana', 'are mere'], StringRecordUtils::lineToArray("Ana are \n mere", 2));
    }

    public function testLineToArrayMultiline()
    {
        static::assertSame(['Ana', 'are', 'mere'], StringRecordUtils::lineToArray("Ana are \n mere", 10));
    }

    public function testGetPropertiesDataNoDefinedProperties()
    {
        static::assertNull(StringRecordUtils::getPropertiesData(0));
    }


    public static function getPropertiesDataProvider(): array
    {
        return [
            [RecordTypes::A, ['ip'],],
            [RecordTypes::AAAA, ['ipv6'],],
            [RecordTypes::CAA, ['flags', 'tag', 'value'],],
            [RecordTypes::CNAME, ['target'],],
            [RecordTypes::SOA, ['mname', 'rname', 'serial', 'refresh', 'retry', 'expire', 'minimum-ttl'],],
            [RecordTypes::TXT, ['txt'],],
            [RecordTypes::NS, ['target'],],
            [RecordTypes::MX, ['pri', 'target'],],
            [RecordTypes::PTR, ['target'],],
            [RecordTypes::SRV, ['pri', 'weight', 'port', 'target'],],
        ];
    }

    /**
     * @param int $recordTypeId
     * @param array $additionalData
     * @return void
     * @dataProvider getPropertiesDataProvider
     */
    public function testGetPropertiesDataValid(int $recordTypeId, array $additionalData)
    {
        $finalData = array_merge(['host', 'ttl', 'class', 'type'], $additionalData);
        static::assertSame($finalData, StringRecordUtils::getPropertiesData($recordTypeId));
    }

    public static function normalizeRawResultDataProvider(): array
    {
        return [
            [
                [],
                []
            ],
            [
                [
                    ';;test',
                    'test.com 3600 IN TXT "v=spf1 include:_spf.test.com"',
                ],
                []
            ],
            [
                [
                    'test.com 3600 IN TST A',
                ],
                []
            ],
            [
                [
                    'test.com 3600 IN SPF v=spf1 include:_legacy.test.com',
                ],
                [
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'class' => 'IN',
                        'type'  => 'TXT',
                        'txt'   => 'v=spf1 include:_legacy.test.com',
                    ]
                ]
            ],
            [
                [
                    'test.com 3600 IN NAPTR 1 1 "" "123" "regular" .',
                ],
                [
                    [
                        'host'        => 'test.com',
                        'ttl'         => 3600,
                        'class'       => 'IN',
                        'type'        => 'NAPTR',
                        'order'       => 1,
                        'pref'        => 1,
                        'flag'        => '',
                        'services'    => '123',
                        'regex'       => 'regular',
                        'replacement' => '',
                    ],
                ]
            ],
            [
                [
                    'test.com 3600 IN TXT "v=spf1 include:_spf.test.com"',
                    'test.com 3600 IN TXT v=spf1 include:_spf.test.com',
                ], [
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'class' => 'IN',
                        'type'  => 'TXT',
                        'txt'   => 'v=spf1 include:_spf.test.com',
                    ],
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'class' => 'IN',
                        'type'  => 'TXT',
                        'txt'   => 'v=spf1 include:_spf.test.com',
                    ]
                ]
            ]
        ];
    }

    /**
     * @param array $result
     * @param array $expectedData
     * @return void
     * @dataProvider normalizeRawResultDataProvider
     */
    public function testNormalizeRawResult(array $result, array $expectedData)
    {
        self::assertSame(
            $expectedData,
            StringRecordUtils::normalizeRawResult(
                $result
            )
        );
    }

}
