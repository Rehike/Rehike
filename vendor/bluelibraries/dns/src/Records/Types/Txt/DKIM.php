<?php

namespace BlueLibraries\Dns\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Regex;

class DKIM extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    public const VERSION = 'v';
    public const KEY_TYPE = 'k';
    public const PUBLIC_KEY = 'p';
    public const HASH_TYPE = 'h';
    public const GROUP = 'g';
    public const NOTES = 'n';
    public const QUERY = 'q';
    public const SERVICE_TYPE = 's';
    public const TESTING_TYPE = 't';

    private string $txtRegex = Regex::DKIM;

    private function getExtendedTypeName(): ?string
    {
        return ExtendedTxtRecords::DKIM;
    }

    public function getPublicKey(): ?string
    {
        return $this->getParsedValue(self::PUBLIC_KEY);
    }

    public function getVersion(): ?string
    {
        return $this->getParsedValue(self::VERSION);
    }

    public function getKeyType(): ?string
    {
        return $this->getParsedValue(self::KEY_TYPE);
    }

    public function getHashType(): ?string
    {
        return $this->getParsedValue(self::HASH_TYPE);
    }

    public function getGroup(): ?string
    {
        return $this->getParsedValue(self::GROUP);
    }

    public function getNotes(): ?string
    {
        return $this->getParsedValue(self::NOTES);
    }

    public function getQuery(): ?string
    {
        return $this->getParsedValue(self::QUERY);
    }

    public function getServiceType(): ?string
    {
        return $this->getParsedValue(self::SERVICE_TYPE);
    }

    public function getTestingType(): ?string
    {
        return $this->getParsedValue(self::TESTING_TYPE);
    }

    public function getSelector(): ?string
    {
        if (empty($this->getHost())) {
            return null;
        }

        $result = preg_match(Regex::DKIM_SELECTOR_VALUE, $this->getHost(), $matches);

        if ($result !== 1) {
            return null;
        }

        return $matches[1] ?? null;
    }

}
