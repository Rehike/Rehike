<?php

namespace BlueLibraries\Dns\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Regex;

class MtaSts extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    public const VERSION = 'v';
    public const ID = 'id';

    private string $txtRegex = Regex::MTA_STS_RECORD;

    private function getExtendedTypeName(): ?string
    {
        return ExtendedTxtRecords::MTA_STS_REPORTING;
    }

    public function getVersion(): ?string
    {
        return $this->getParsedValue(self::VERSION);
    }

    public function getId(): ?string
    {
        return $this->getParsedValue(self::ID);
    }

}
