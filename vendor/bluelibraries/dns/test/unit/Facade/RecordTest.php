<?php

namespace BlueLibraries\Dns\Test\Unit\Facade;

use BlueLibraries\Dns\Facade\Record;
use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordInterface;
use BlueLibraries\Dns\Records\Types\DnsSec\RRSIG;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Records\Types\Txt\SPF;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{

    public static function fromStringDataProvider(): array
    {
        return [
            [
                '',
                null,
            ],
            [
                'txt',
                null,
            ],
            [
                'test 100',
                null,
            ],
            [
                'test.com 3600 IN TXT text',
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                'test.com 3600 IN TXT "text"',
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                'test.com 3600 IN TXT "te" "xt"',
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                'test.com 3600 IN TXT v=spf1 include:_spf.test.com',
                new SPF(
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'txt'   => 'v=spf1 include:_spf.test.com',
                        'class' => 'IN',
                        'type'  => 'TXT',
                    ]
                )
            ],
            [
                'test.com 3600 IN SPF "v=spf1 include:_spf.test.com"',
                new SPF(
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'txt'   => 'v=spf1 include:_spf.test.com',
                        'class' => 'IN',
                        'type'  => 'TXT',
                    ]
                )
            ],
            [
                'test.com 3600 IN RRSIG A 1 2 3600 169254 169253 49890 test.com ==signature==',
                new RRSIG(
                    [
                        'host'                 => 'test.com',
                        'ttl'                  => '3600',
                        'type-covered'         => 'A',
                        'algorithm'            => 1,
                        'labels-number'        => 2,
                        'original-ttl'         => 3600,
                        'signature-expiration' => 169254,
                        'signature-creation'   => 169253,
                        'key-tag'              => 49890,
                        'signer-name'          => 'test.com',
                        'signature'            => '==signature==',
                    ]
                )
            ],
        ];
    }

    /**
     * @param string $string
     * @param RecordInterface|null $expected
     * @return void
     * @throws RecordException
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $string, $expected)
    {
        self::assertEquals($expected, Record::fromString($string));
    }

    public static function fromNormalizedArrayDataProvider(): array
    {
        return [
            [
                [],
                null
            ],
            [
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => 'TXT',
                    'txt'  => 'text'
                ],
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'type' => 'TXT',
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => 'TXT',
                    'txt'  => 'text'
                ],
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'type' => 'TXT',
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => 'TXT',
                    'txt'  => 'text'
                ],
                new TXT(
                    [
                        'host' => 'test.com',
                        'ttl'  => 3600,
                        'type' => 'TXT',
                        'txt'  => 'text'
                    ]
                )
            ],
            [
                [
                    'host'  => 'test.com',
                    'ttl'   => 3600,
                    'txt'   => 'v=spf1 include:_spf.test.com',
                    'class' => 'IN',
                    'type'  => 'TXT',
                ],
                new SPF(
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'type'  => 'TXT',
                        'txt'   => 'v=spf1 include:_spf.test.com',
                        'class' => 'IN',
                    ]
                )
            ],
            [
                [
                    'host'  => 'test.com',
                    'ttl'   => 3600,
                    'txt'   => 'v=spf1 include:_spf.test.com',
                    'class' => 'IN',
                    'type'  => 'TXT',
                ],
                new SPF(
                    [
                        'host'  => 'test.com',
                        'ttl'   => 3600,
                        'txt'   => 'v=spf1 include:_spf.test.com',
                        'class' => 'IN',
                        'type'  => 'TXT',
                    ]
                )
            ],
            [
                [
                    'host'                 => 'test.com',
                    'ttl'                  => '3600',
                    'type'                 => 'RRSIG',
                    'type-covered'         => 'A',
                    'algorithm'            => 1,
                    'labels-number'        => 2,
                    'original-ttl'         => 3600,
                    'signature-expiration' => 169254,
                    'signature-creation'   => 169253,
                    'key-tag'              => 49890,
                    'signer-name'          => 'test.com',
                    'signature'            => '==signature==',
                ],
                new RRSIG(
                    [
                        'host'                 => 'test.com',
                        'ttl'                  => '3600',
                        'type-covered'         => 'A',
                        'algorithm'            => 1,
                        'labels-number'        => 2,
                        'original-ttl'         => 3600,
                        'signature-expiration' => 169254,
                        'signature-creation'   => 169253,
                        'key-tag'              => 49890,
                        'signer-name'          => 'test.com',
                        'signature'            => '==signature==',
                    ]
                )
            ],
        ];
    }

    /**
     * @param array $array
     * @param RecordInterface|null $expected
     * @return void
     * @throws RecordException
     * @dataProvider fromNormalizedArrayDataProvider
     */
    public function testFromNormalizedArray(array $array, ?RecordInterface $expected)
    {
        self::assertEquals($expected, Record::fromNormalizedArray($array));
    }

}
