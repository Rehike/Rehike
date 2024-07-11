<?php

namespace BlueLibraries\Dns\Records;

use DateTime;
use BlueLibraries\Dns\Regex;

class DnsUtils
{

    public static function isValidDomainOrSubdomain(string $domain): bool
    {
        if (empty($domain) || strlen($domain) < 4) {
            return false;
        }
        return preg_match(Regex::DOMAIN_OR_SUBDOMAIN, $domain) === 1;
    }

    public static function ipV6Shortener(string $ipv6): string
    {
        if (substr($ipv6, -2) != ':0') {
            return preg_replace("/:0{1,3}/", ":", $ipv6);
        }
        return $ipv6;
    }

    public static function sanitizeTextLineSeparators(string $text): string
    {
        return
            str_replace(
                '  ', ' ',
                str_replace('" "', '', $text)
            );
    }

    public static function sanitizeRecordTxt(string $txt): string
    {
        return str_replace('"', '\"', $txt);
    }

    public static function getBitsFromString($string): array
    {
        if (strlen($string) === 0) {
            return [];
        }

        $data = str_split($string);

        $result = '';

        foreach ($data as $value) {
            $decimal = (ord($value));
            $binary = decbin($decimal);
            $binary = str_pad($binary, 8, '0', STR_PAD_LEFT);
            $longBinary = $binary;
            $result .= $longBinary;
        }

        return str_split($result);
    }

    public static function getRecordsNamesFromBinary(array $binary, int $blockOffset): string
    {
        $result = [];

        foreach ($binary as $recordTypeId => $value) {
            if ((int)$value === 1) {
                $result[] = RecordTypes::getName($recordTypeId + $blockOffset);
            }
        }
        return implode(' ', $result);
    }

    public static function getHumanReadableDateTime($timestamp): int
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $result = $dateTime->format('YmdHis');
        return (int)$result;
    }

    public static function getSplitSignature(string $signature, int $bufferLength, string $separator = ' '): string
    {
        $signatureLen = strlen($signature);
        if ($bufferLength >= $signatureLen) {
            return $signature;
        }
        return trim(chunk_split($signature, $bufferLength, $separator));
    }

    public static function asciiString(string $string, $glue = ''): string
    {
        if (empty($string)) {
            return '';
        }

        $result = [];
        $stringData = str_split($string);

        foreach ($stringData as $key => $value) {
            $result[] = ord($value);
        }

        return implode($glue, $result);
    }

    /**
     * @param RecordInterface[] $array
     * @return void
     */
    public static function removeDuplicates(array $array): array
    {
        if (empty($array)) {
            return [];
        }

        $result = [];
        $foundHashes = [];

        foreach ($array as $record) {
            $recordHash = $record->getHash();
            if (!in_array($recordHash, $foundHashes)) {
                $result[] = $record;
                $foundHashes[] = $recordHash;
            }
        }

        return $result;
    }


    /**
     * @param RecordInterface[] $results
     * @return RecordInterface[]
     */
    public static function sortRecords(array $results): array
    {
        $result = [];

        foreach ($results as $record) {
            $result[$record->getHash()] = $record;
        }

        ksort($result);
        return array_values($result);
    }

    public static function trim(string $haystack, $needle, int $length = 1)
    {
        if (empty($haystack)) {
            return '';
        }

        if (empty($needle) || empty($length)) {
            return $haystack;
        }

        $result = preg_replace(
            sprintf(
                Regex::TRIM_LENGTH_START, $needle, $length),
            '',
            $haystack
        );
        return preg_replace(
            sprintf(Regex::TRIM_LENGTH_END, $needle, $length),
            '',
            $result
        );
    }

    public static function getConsecutiveLabels(
        string $text,
        int    &$i,
        int    $startsFrom = 0,
               $count = 1
    ): array
    {
        if (empty($text)) {
            return [];
        }

        $textLen = strlen($text);

        $foundCount = 0;

        $result = [];

        for ($i = $startsFrom; $i < $textLen; $i++) {
            $len = ord($text[$i]);
            if ($len === 0) {
                if ($foundCount >= $count) {
                    $i += 1;
                    break;
                }
            }

            $substr = substr($text, $i + 1, $len);

            if ($substr === chr(0) && $count === 1) {
                $substr = '\000';
            }

            $result[] = $substr;
            $i += $len;
            $foundCount++;
        }

        return $result;
    }

    public static function getBlocks(string $string): array
    {

        if (empty($string)) {
            return [];
        }

        $result = [];
        $stringLen = strlen($string);

        for ($i = 0; $i < $stringLen; $i++) {
            $item = substr($string, $i, 1);
            $len = ord($item);

            if ($len === 0) {
                break;
            }

            $result[] = substr($string, $i + 1, $len);
            $i += $len;
        }

        return $result;
    }

}
