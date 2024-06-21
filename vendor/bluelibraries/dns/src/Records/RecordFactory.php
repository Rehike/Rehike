<?php

namespace BlueLibraries\Dns\Records;

use BlueLibraries\Dns\Records\Types\A;
use BlueLibraries\Dns\Records\Types\AAAA;
use BlueLibraries\Dns\Records\Types\CAA;
use BlueLibraries\Dns\Records\Types\CNAME;
use BlueLibraries\Dns\Records\Types\DnsSec\CDNSKey;
use BlueLibraries\Dns\Records\Types\DnsSec\CDS;
use BlueLibraries\Dns\Records\Types\DnsSec\DNSKey;
use BlueLibraries\Dns\Records\Types\DnsSec\DS;
use BlueLibraries\Dns\Records\Types\DnsSec\NSEC;
use BlueLibraries\Dns\Records\Types\DnsSec\NSEC3PARAM;
use BlueLibraries\Dns\Records\Types\DnsSec\RRSIG;
use BlueLibraries\Dns\Records\Types\HINFO;
use BlueLibraries\Dns\Records\Types\HTTPS;
use BlueLibraries\Dns\Records\Types\MX;
use BlueLibraries\Dns\Records\Types\NAPTR;
use BlueLibraries\Dns\Records\Types\NS;
use BlueLibraries\Dns\Records\Types\PTR;
use BlueLibraries\Dns\Records\Types\SOA;
use BlueLibraries\Dns\Records\Types\SRV;
use BlueLibraries\Dns\Records\Types\TXT;

class RecordFactory
{

    private ExtendedTxtRecords $extendedTxtRecords;

    public function __construct(ExtendedTxtRecords $extendedTxtRecords = null)
    {
        if (is_null($extendedTxtRecords)) {
            $extendedTxtRecords = new ExtendedTxtRecords();
        }
        $this->extendedTxtRecords = $extendedTxtRecords;
    }

    /**
     * @param array $recordData
     * @param bool $useExtendedRecords
     * @return RecordInterface
     * @throws RecordException
     */
    public function create(array $recordData, bool $useExtendedRecords)
    {

        if (
            !isset($recordData['type'])
            || is_null($type = RecordTypes::getType($recordData['type']))
        ) {
            throw new RecordException(
                'Invalid record type for recordData: ' .
                json_encode($recordData),
                RecordException::UNABLE_TO_CREATE_RECORD
            );
        }

        switch ($type) {

            case RecordTypes::A:
                return new A($recordData);

            case RecordTypes::NS:
                return new NS($recordData);

            case RecordTypes::CNAME:
                return new CNAME($recordData);

            case RecordTypes::SOA:
                return new SOA($recordData);

            case RecordTypes::MX:
                return new MX($recordData);

            case RecordTypes::CAA:
                return new CAA($recordData);

            case RecordTypes::HINFO:
                return new HINFO($recordData);

            case RecordTypes::RRSIG:
                return new RRSIG($recordData);

            case RecordTypes::DNSKEY:
                return new DNSKey($recordData);

            case RecordTypes::CDNSKEY:
                return new CDNSKey($recordData);

            case RecordTypes::NSEC3PARAM:
                return new NSEC3PARAM($recordData);

            case RecordTypes::DS:
                return new DS($recordData);

            case RecordTypes::CDS:
                return new CDS($recordData);

            case RecordTypes::NSEC:
                return new NSEC($recordData);

            case RecordTypes::SRV:
                return new SRV($recordData);

            case RecordTypes::TXT:
            case RecordTypes::DEPRECATED_SPF:

                $record = new TXT($recordData);

                if ($useExtendedRecords) {
                    $extendedRecord = $this->extendedTxtRecords->getExtendedTxtRecord($recordData);
                    return is_null($extendedRecord) ? $record : $extendedRecord;
                } else {
                    return $record;
                }

            case RecordTypes::AAAA:
                return new AAAA($recordData);

            case RecordTypes::HTTPS:
                return new HTTPS($recordData);

            case RecordTypes::PTR:
                return new PTR($recordData);

            case RecordTypes::NAPTR:
                return new NAPTR($recordData);

            default:
               return null;
        }

    }

}
