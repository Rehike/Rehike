<?php

namespace BlueLibraries\Dns\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Regex;

class TLSReporting extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    public const VERSION = 'v';
    public const RUA = 'rua';

    private string $txtRegex = Regex::TLS_REPORTING;

    private function getExtendedTypeName(): ?string
    {
        return ExtendedTxtRecords::TLS_REPORTING;
    }

    public function getVersion(): ?string
    {
        return $this->getParsedValue(self::VERSION);
    }

    public function getRua(): ?string
    {
        return $this->getParsedValue(self::RUA);
    }

}
