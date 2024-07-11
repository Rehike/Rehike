<?php

namespace BlueLibraries\Dns\Handlers\Raw;

class RawClassTypes
{

    public const IN = 1;
    public const CS = 2;
    public const CH = 3;
    public const HS = 4;

    private static array $rawClassTypes = [
        'IN' => self::IN, // Internet
        'CS' => self::CS, // CSNet -> obsolete
        'CH' => self::CH, // Chaos
        'HS' => self::HS, // Hesiod
    ];

    public static function getRawTypes(): array
    {
        return self::$rawClassTypes;
    }

    public static function getClassNameByRawType($rawClassId): ?string
    {
        foreach (self::$rawClassTypes as $key => $type) {
            if ($rawClassId === $type) {
                return $key;
            }
        }
        return null;
    }

}
