<?php

namespace BlueLibraries\Dns\Facade;

use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordFactory;
use BlueLibraries\Dns\Records\RecordInterface;
use BlueLibraries\Dns\Records\StringRecordUtils;

class Record
{

    protected static ?RecordFactory $factory = null;

    private static function getRecordFactory(): RecordFactory
    {
        return is_null(self::$factory)
            ? self::$factory = new RecordFactory() : self::$factory;
    }

    /**
     * @throws RecordException
     */
    public static function fromString(string $string, bool $asExtendedRecord = true): ?RecordInterface
    {
        if (empty($string)) {
            return null;
        }
        $recordData = StringRecordUtils::normalizeRawResult(
            StringRecordUtils::lineToArray($string)
        );

        if (empty($recordData)) {
            return null;
        }

        return self::getRecordFactory()->create(
            $recordData[0],
            $asExtendedRecord
        );
    }

    /**
     * @throws RecordException
     */
    public static function fromNormalizedArray(array $array, bool $asExtendedRecord = true): ?RecordInterface
    {
        if (empty($array)) {
            return null;
        }
        return self::getRecordFactory()->create(
            $array,
            $asExtendedRecord
        );
    }

}
