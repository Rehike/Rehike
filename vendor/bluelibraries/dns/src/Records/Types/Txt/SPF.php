<?php

namespace BlueLibraries\Dns\Records\Types\Txt;

use BlueLibraries\Dns\Records\DnsUtils;
use BlueLibraries\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Regex;

/**
 * Sender Policy Framework
 */
class SPF extends TXT
{

    use ExtendedRecordTrait;

    public function __construct(?array $data= [])
    {

        if (!empty($data['txt'])) {
            $data['txt'] = DnsUtils::sanitizeTextLineSeparators($data['txt']);
        }
        parent::__construct($data);
    }

    private function getExtendedTypeName(): ?string
    {
        return ExtendedTxtRecords::SPF;
    }

    public function getHosts(): array
    {
        if (empty($this->getTxt())) {
            return [];
        }

        preg_match_all(Regex::WORDS_SEPARATED_SPACE, $this->getTxt(), $matches);

        $words = $matches[0];

        if ($words[0] !== 'v=spf1') {
            return [];
        }

        array_shift($words);

        return $words;
    }

}
