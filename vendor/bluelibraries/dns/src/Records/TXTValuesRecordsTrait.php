<?php

namespace BlueLibraries\Dns\Records;

use BlueLibraries\Dns\Regex;

trait TXTValuesRecordsTrait
{

    private array $parsedValues = [];

    public function parseValues(): bool
    {
        $txt = trim($this->getTxt());

        if (empty($txt)) {
            return false;
        }

        if ($this->isParsedValue()) {
            return true;
        }

        $value = DnsUtils::sanitizeTextLineSeparators($txt);
        preg_match_all(Regex::TXT_VALUES, $value, $matches);

        $result = [];

        foreach ($matches[0] as $match) {
            $matchData = explode('=', $match);
            if (!isset($matchData[1])) {
                return false;
            }
            $result[strtolower($matchData[0])] = trim($matchData[1]);
        }

        $this->parsedValues = $result;
        $this->parsedValues['internalHash'] = $this->getValueHash();

        if (!preg_match($this->txtRegex, $txt)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function getValueHash(): string
    {
        return md5($this->getTxt());
    }

    private function isParsedValue(): bool
    {
        $hash = $this->parsedValues['internalHash'] ?? null;

        if (is_null($hash)) {
            return false;
        }

        return $hash === $this->getValueHash();
    }

    private function getParsedValue(string $key): ?string
    {
        $this->parseValues();
        return $this->parsedValues[$key] ?? null;
    }

    private function getIntegerParsedValue(string $key): ?int
    {
        $result = $this->getParsedValue($key);
        return is_null($result)
            ? $result
            : (int)$result;
    }

}
