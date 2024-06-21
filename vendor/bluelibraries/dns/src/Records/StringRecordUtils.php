<?php

namespace BlueLibraries\Dns\Records;

use BlueLibraries\Dns\Regex;

class StringRecordUtils
{
    public static function lineToArray(string $line, ?int $limit = 0): array
    {
        if (empty($line)) {
            return [];
        }
        return explode(
            ' ',
            preg_replace(Regex::SEPARATED_WORDS, ' ', $line),
            $limit
        );
    }

    /**
     * @param $propertyName
     * @param $value
     * @return int|mixed|string
     */
    protected static function getFormattedPropertyValue($propertyName, $value)
    {
        if (
            in_array(
                $propertyName,
                ['host', 'mname', 'rname', 'target', 'signer-name', 'next-authoritative-name', 'replacement'
                ])) {
            $value = strtolower(rtrim($value, '.'));
        }

        if (in_array($propertyName, ['value', 'flag', 'services', 'regex'])) {
            $value = trim($value, '"');
        }

        if ($propertyName === 'ipv6') {
            $value = DnsUtils::ipV6Shortener($value);
        }

        if ($propertyName === 'type' && $value === 'SPF') {
            $value = 'TXT';
        }

        $value = DnsRecordProperties::isNumberProperty($propertyName)
            ? (is_numeric($value) ? $value + 0 : null)
            : $value;

        return DnsRecordProperties::isLoweredCaseProperty($propertyName)
            ? strtolower($value)
            : $value;
    }

    public static function getRawData(array $configData, string $rawLine): ?array
    {

        $array = self::lineToArray($rawLine, count($configData));

        $result = [];

        foreach ($array as $key => $value) {
            $propertyName = $configData[$key];
            $value = self::getFormattedPropertyValue($propertyName, $value);
            $result[$propertyName] = $value;
        }

        if (isset($result['txt'])) {
            $result['txt'] = DnsUtils::trim(DnsUtils::sanitizeTextLineSeparators($result['txt']), '"',1);
        }

        return $result;
    }

    public static function getPropertiesData(int $typeId): ?array
    {
        $properties = DnsRecordProperties::getProperties($typeId);
        if (empty($properties)) {
            return null;
        }
        return array_merge(DnsRecordProperties::getDefaultProperties(), $properties);
    }

    public static function normalizeRawResult(array $rawResult): array
    {
        if (empty($rawResult)) {
            return [];
        }

        $result = [];

        foreach ($rawResult as $rawLine) {

            if (strpos($rawLine, ';;') === 0) {
                return [];
            }

            $lineData = self::lineToArray($rawLine, 5);
            $type = $lineData[3] ?? null;

            if (is_null($type)) {
                continue;
            }

            $typeId = RecordTypes::getType($type);

            if (is_null($typeId)) {
                continue;
            }

            $configData = self::getPropertiesData($typeId);

            if (!empty($configData)) {
                $result[] = StringRecordUtils::getRawData($configData, $rawLine);
            }
        }

        return $result;
    }

}
