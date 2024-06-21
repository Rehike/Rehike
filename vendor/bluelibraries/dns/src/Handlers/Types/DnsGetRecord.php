<?php

namespace BlueLibraries\Dns\Handlers\Types;

use BlueLibraries\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Records\RecordTypes;

class DnsGetRecord extends AbstractDnsHandler
{

    private static array $internalPHPTypes = [
        RecordTypes::A     => DNS_A,
        RecordTypes::CNAME => DNS_CNAME,
        RecordTypes::HINFO => DNS_HINFO,
        RecordTypes::CAA   => DNS_CAA,
        RecordTypes::MX    => DNS_MX,
        RecordTypes::NS    => DNS_NS,
        RecordTypes::PTR   => DNS_PTR,
        RecordTypes::SOA   => DNS_SOA,
        RecordTypes::TXT   => DNS_TXT,
        RecordTypes::AAAA  => DNS_AAAA,
        RecordTypes::SRV   => DNS_SRV,
        RecordTypes::NAPTR => DNS_NAPTR,
        RecordTypes::A6    => DNS_A6,
        RecordTypes::ALL   => DNS_ALL,
    ];

    private static function getInternalTypeId(int $typeId): ?int
    {
        return static::$internalPHPTypes[$typeId] ?? null;
    }

    public function getType(): string
    {
        return DnsHandlerTypes::DNS_GET_RECORD;
    }

    public function canUseIt(): bool
    {
        return function_exists('dns_get_record');
    }

    /**
     * @throws DnsHandlerException
     */
    public function getDnsData(string $host, int $typeId): array
    {
        $this->validateParams($host, $typeId);
        $this->validatePHPInternalTypeId($typeId);

        $internalTypeId = static::getInternalTypeId($typeId);

        return $this->getDnsRawResult($host, $internalTypeId);
    }

    public function getDnsRawResult(string $host, int $type): array
    {
        $startProcess = time();
        for ($i = 0; $i <= $this->retries; $i++) {
            if (
                ($result = $this->getDnsRecord($host, $type)) !== []
                || ((time() - $startProcess) >= $this->timeout)
            ) {
                return is_array($result) ? $result : [];
            }
        }
        return [];
    }

    /**
     * @param string $host
     * @param int $type
     * @return array|bool
     */
    protected function getDnsRecord(string $host, int $type)
    {
        return empty($host) ? false : $this->getUpdatedRecordsData(dns_get_record($host, $type));
    }

    public function getUpdatedRecordsData($records): array
    {
        if (!is_array($records) || empty($records)) {
            return $records;
        }

        $records = $this->splitTXTEntries($records);

        foreach ($records as $key => $record) {
            if ($record['type'] === 'NAPTR') {
                $records[$key] = $this->fixNAPTRFlags($record);
            }
        }
        return $records;
    }

    private function fixNAPTRFlags(array $record): array
    {
        $result = [];
        foreach ($record as $key => $value) {
            if ($key === 'flags') {
                $result['flag'] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @throws DnsHandlerException
     */
    public function setNameserver(?string $nameserver): self
    {
        throw new DnsHandlerException(
            'Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!',
            DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER
        );
    }

    /**
     * @param int $typeId
     * @return void
     * @throws DnsHandlerException
     */
    private function validatePHPInternalTypeId(int $typeId): void
    {
        if (!isset(self::$internalPHPTypes[$typeId])) {
            $recordTypeName = RecordTypes::getName($typeId);
            throw new DnsHandlerException(
                'DNS record type ' . json_encode($recordTypeName) .
                ' , please use a different DNS Handler (TCP is recommended)!',
                DnsHandlerException::TYPE_ID_NOT_SUPPORTED
            );
        }
    }

    private function splitTXTEntries(array $records): array
    {
        if (empty($records)) {
            return $records;
        }

        $result = [];

        foreach ($records as $record) {

            if ($record['type'] !== 'TXT') {
                $result[] = $record;
                continue;
            }
            $entries = $record['entries'] ?? [];
            if (!empty($entries)) {
                foreach ($entries as $entry) {
                    $subEntry = $record;
                    unset($subEntry['entries']);
                    $subEntry['txt'] = $entry;
                    $result[] = $subEntry;
                }
            } else {
                $result[] = $record;
            }
        }
        return $result;
    }

}
