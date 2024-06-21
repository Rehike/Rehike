<?php

namespace BlueLibraries\Dns\Test\Integration\Handlers;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerFactory;
use BlueLibraries\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordInterface;
use BlueLibraries\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class DnsHandlersTest extends TestCase
{

    private DnsHandlerFactory $handlerFactory;

    /**
     * @var DnsRecords[]
     */
    private array $subjects = [];

    public static array $recordTypesFound = [
        'short-text'    => [],
        'long-text'     => [],
        'tcp'           => [],
        'udp'           => [],
        'duplicates'    => [],
        'empty-results' => [],
    ];

    /**
     * @throws DnsHandlerException
     * @throws DnsHandlerFactoryException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->handlerFactory = new DnsHandlerFactory();

        foreach (DnsHandlerTypes::getAll() as $handlerType) {
            $dnsHandler = $this->handlerFactory->create($handlerType);

            if (!$dnsHandler->canUseIt()) {
                continue;
            }

            if ($dnsHandler->getType() !== DnsHandlerTypes::DNS_GET_RECORD) {
                $dnsHandler
                    ->setNameserver('8.8.8.8')
                    ->setTimeout(10)
                    ->setRetries(3);
            }
            $this->subjects[$handlerType] = new DnsRecords(
                $dnsHandler
            );
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

// The following code should be enabled only for debug purposes
//        foreach (self::$recordTypesFound as $value) {
//            if (!empty($value)) {
//                print_r($value);
//            }
//        }
    }

    /**
     * @param array $results
     * @param array|null $recordTypesFound
     * @return bool
     */
    private function allArraysAreEquals(array $results, ?array &$recordTypesFound = []): bool
    {
        if (empty($results)) {
            return true;
        }

        $key = key($results);
        $recordsCount = count($results[$key]);
        reset($results);
        $lastType = null;
        $finalResult = true;
        $recordTypesFound = [];

        /**
         * @var RecordInterface[] $lastRecords
         */
        foreach ($results as $type => $records) {
            for ($i = 0; $i < $recordsCount; $i++) {
                if (isset($lastRecords[$i])) {
                    $record = $records[$i];
                    $lastRecord = $lastRecords[$i];
                    $lastRecordHash = $lastRecord->getHash();
                    $recordHash = $record->getHash();
                    $lastRecordBaseData = $lastRecord->toBaseArray();
                    $recordBaseData = $record->toBaseArray();
                    $this->assertSame($lastRecordBaseData, $recordBaseData, print_r([$lastType, $type], true));
                    $finalResult = $finalResult && ($lastRecordHash === $recordHash);
                } else {
                    $recordTypesFound [] = $records[$i]->getTypeName();
                }
            }
            $lastRecords = $records;
            $lastType = $type;
        }

        return $finalResult;
    }

    /**
     * @return array
     * 512 bytes UDP limit allows only short text records
     */
    private function getRecordsTypesForTestingShortValues(): array
    {
        return [
            RecordTypes::A,
            RecordTypes::NS,

            RecordTypes::CNAME,
            RecordTypes::SOA,
            RecordTypes::PTR,
            RecordTypes::HINFO,
            RecordTypes::MX,
            RecordTypes::TXT,

            RecordTypes::KEY,
            RecordTypes::AAAA,

            RecordTypes::SRV,

            RecordTypes::CERT,

            RecordTypes::IPSECKEY,
            RecordTypes::RRSIG,
            RecordTypes::NSEC,
            RecordTypes::DNSKEY,
            RecordTypes::DHCID,
            RecordTypes::NSEC3,
            RecordTypes::NSEC3PARAM,

            RecordTypes::CDNSKEY,
            RecordTypes::OPENPGPKEY,

            RecordTypes::CAA,
        ];
    }

    private function getRecordsTypesForTestingLongValues(): array
    {
        return [
                RecordTypes::A,
                RecordTypes::NS,
                RecordTypes::MD,
                RecordTypes::MF,
                RecordTypes::CNAME,
                RecordTypes::SOA,
                RecordTypes::MB,
                RecordTypes::MG,
                RecordTypes::MR,
                RecordTypes::NULL,
                RecordTypes::WKS,
                RecordTypes::PTR,
                RecordTypes::HINFO,
                RecordTypes::MINFO,
                RecordTypes::MX,
                RecordTypes::TXT,
                RecordTypes::RP,
                RecordTypes::AFSDB,
                RecordTypes::X25,
                RecordTypes::ISDN,
                RecordTypes::RT,
                RecordTypes::NSAP,
                RecordTypes::NSAP_PTR,
                RecordTypes::SIG,
                RecordTypes::KEY,
                RecordTypes::PX,
                RecordTypes::GPOS,
                RecordTypes::AAAA,
                RecordTypes::LOC,
                RecordTypes::NXT,
                RecordTypes::EID,
                RecordTypes::NIMLOC,
                RecordTypes::SRV,
                RecordTypes::ATMA,
                RecordTypes::NAPTR,
                RecordTypes::KX,
                RecordTypes::CERT,
                RecordTypes::A6,
                RecordTypes::DNAME,
                RecordTypes::SINK,
                RecordTypes::OPT,
                RecordTypes::APL,
                RecordTypes::DS,
                RecordTypes::SSHFP,
                RecordTypes::IPSECKEY,
                RecordTypes::RRSIG,
                RecordTypes::NSEC,
                RecordTypes::DNSKEY,
                RecordTypes::DHCID,
                RecordTypes::NSEC3,
                RecordTypes::NSEC3PARAM,
                RecordTypes::TLSA,
                RecordTypes::SMIMEA,
                RecordTypes::HIP,
                RecordTypes::NINFO,
                RecordTypes::RKEY,
                RecordTypes::TALINK,
                RecordTypes::CDS,
                RecordTypes::CDNSKEY,
                RecordTypes::OPENPGPKEY,
                RecordTypes::CSYNC,
                RecordTypes::ZONEMD,
                RecordTypes::SVCB,
                RecordTypes::HTTPS,
                RecordTypes::TKEY,
                RecordTypes::TSIG,
                RecordTypes::MAILB,
                RecordTypes::MAILA,
                RecordTypes::URI,
                RecordTypes::CAA,
                RecordTypes::AVC,
                RecordTypes::DOA,
                RecordTypes::AMTRELAY,
                RecordTypes::TA,
                RecordTypes::DLV,

                RecordTypes::DEPRECATED_SPF,
        ];
    }

    public static function recordsTypesShortDataProvider(): array
    {
        return [
            ['www.test.com'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesShortDataProvider
     */
    public function testRecordsTypesShortData(string $domain)
    {
        $testKey = 'short-text';
        $results = [];
        $recordTypes = $this->getRecordsTypesForTestingShortValues();

        foreach ($this->subjects as $handlerType => $subject) {
            try {
                $results[$handlerType] = $subject->get($domain, $recordTypes);
            } catch (DnsHandlerException $exception) {
                if ($exception->getCode() !== DnsHandlerException::TYPE_ID_NOT_SUPPORTED) {
                    throw $exception;
                }
            }
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

    public static function recordsTypesLongDataProvider(): array
    {
        return [
            ['metallica.com'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesLongDataProvider
     */
    public function testRecordsTypesLongData(string $domain)
    {
        $testKey = 'long-text';
        $results = [];
        $recordTypes = $this->getRecordsTypesForTestingLongValues();

        foreach ($this->subjects as $handlerType => $subject) {

            if (
                $handlerType === DnsHandlerTypes::UDP
            ) {
                continue;
            }

            try {
                $results[$handlerType] = $subject->get($domain, $recordTypes, true);
            } catch (DnsHandlerException $exception) {
                if ($exception->getCode() !== DnsHandlerException::TYPE_ID_NOT_SUPPORTED) {
                    throw $exception;
                }
            }
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

    public static function recordsTypesTCPandDIGDataProvider(): array
    {
        return [
            ['coliva.ro'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesTCPandDIGDataProvider
     */
    public function testTCPandDIGTypes(string $domain)
    {
        $testKey = 'long-text';
        $results = [];
        $recordTypes = $this->getRecordsTypesForTestingLongValues();

        foreach ($this->subjects as $handlerType => $subject) {
            if (
                $handlerType === DnsHandlerTypes::UDP
                || $handlerType === DnsHandlerTypes::DNS_GET_RECORD
            ) {
                continue;
            }
            try {
                $results[$handlerType] = $subject->get($domain, $recordTypes, true, true, false);
            } catch (DnsHandlerException $exception) {
                if ($exception->getCode() !== DnsHandlerException::TYPE_ID_NOT_SUPPORTED) {
                    throw $exception;
                }
            }
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

    public static function recordsTypesTCPOnlyDataProvider(): array
    {
        return [
            ['asus.com'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesTCPOnlyDataProvider
     */
    public function testRecordsTypesTCPOnly(string $domain)
    {
        $testKey = 'tcp';
        $results = [];
        $recordTypes = $this->getRecordsTypesForTestingLongValues();

        foreach ($this->subjects as $handlerType => $subject) {

            if ($handlerType !== DnsHandlerTypes::TCP) {
                continue;
            }

            $results[$handlerType] = $subject->get($domain, $recordTypes, true, false, false);
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

    public static function recordsTypesDuplicatesProvider(): array
    {
        return [
            ['test.com'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesDuplicatesProvider
     */
    public function testRecordsTypesDuplicatesData(string $domain)
    {
        $testKey = 'duplicates';
        $results = [];
        $recordTypes = [RecordTypes::A, RecordTypes::A];

        foreach ($this->subjects as $handlerType => $subject) {
            try {
                $results[$handlerType] = $subject->get($domain, $recordTypes, true);
            } catch (DnsHandlerException $exception) {
                if ($exception->getCode() !== DnsHandlerException::TYPE_ID_NOT_SUPPORTED) {
                    throw $exception;
                }
            }
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

    public static function recordsTypesEmptyResultsProvider(): array
    {
        return [
            ['test.com'],
        ];
    }

    /**
     * @param string $domain
     * @return void
     * @throws DnsHandlerException
     * @throws RecordException
     * @dataProvider recordsTypesEmptyResultsProvider
     */
    public function testRecordsTypesEmptyResultsData(string $domain)
    {
        $testKey = 'empty-results';
        $recordTypes = [RecordTypes::NAPTR, RecordTypes::NAPTR];
        $results = [];

        foreach ($this->subjects as $handlerType => $subject) {
            try {
                $results[$handlerType] = $subject->get($domain, $recordTypes, true);
            } catch (DnsHandlerException $exception) {
                if ($exception->getCode() !== DnsHandlerException::TYPE_ID_NOT_SUPPORTED) {
                    throw $exception;
                }
            }
        }

        $this->assertTrue($this->allArraysAreEquals($results, $recordTypesFound));

        self::$recordTypesFound[$testKey] = array_values(
            array_unique(
                array_merge(self::$recordTypesFound[$testKey], $recordTypesFound)
            )
        );
    }

}
