<?php

namespace BlueLibraries\Dns\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\Types\TXT;

class DomainVerification extends TXT
{

    use ExtendedRecordTrait;

    public function getExtendedTypeName(): ?string
    {
        return ExtendedTxtRecords::DOMAIN_VERIFICATION;
    }

    public function getProvider(): ?string
    {
        return ExtendedTxtRecords::getSiteVerification($this->getTxt());
    }

    public function getValue(): string
    {
        return ExtendedTxtRecords::getSiteVerificationValue($this->getTxt());
    }

}
