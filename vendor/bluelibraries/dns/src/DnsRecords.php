<?php

namespace BlueLibraries\Dns;

use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerInterface;
use BlueLibraries\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\DnsUtils;
use BlueLibraries\Dns\Records\RecordException;
use BlueLibraries\Dns\Records\RecordFactory;
use BlueLibraries\Dns\Records\RecordInterface;

class DnsRecords
{
    private DnsHandlerInterface $handler;
    private RecordFactory $factory;

    /**
     * @param DnsHandlerInterface|null $handler
     * @param RecordFactory|null $factory
     */
    public function __construct(DnsHandlerInterface $handler = null, RecordFactory $factory = null)
    {
        if (is_null($handler)) {
            $handler = new TCP();
        }

        if (is_null($factory)) {
            $factory = new RecordFactory();
        }

        $this->handler = $handler;
        $this->factory = $factory;
    }

    /**
     * @return DnsHandlerInterface
     */
    public function getHandler(): DnsHandlerInterface
    {
        return $this->handler;
    }

    /**
     * @param DnsHandlerInterface $handler
     * @return DnsRecords
     */
    public function setHandler(DnsHandlerInterface $handler): DnsRecords
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return RecordFactory
     */
    public function getFactory(): RecordFactory
    {
        return $this->factory;
    }

    /**
     * @param RecordFactory $factory
     * @return DnsRecords
     */
    public function setFactory(RecordFactory $factory): DnsRecords
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @param string $host
     * @param int|array $type
     * @param bool $useExtendedRecords
     * @param bool $keepOrder
     * @param bool $removeDuplicates
     * @return array
     * @throws DnsHandlerException
     * @throws RecordException
     * @throws RawDataException
     */
    public function get(string $host, $type, bool $useExtendedRecords = true, bool $keepOrder = true, bool $removeDuplicates = true): array
    {
        if (is_int($type)) {
            return $this->getRecordDataForType($host, $type, $useExtendedRecords, $keepOrder);
        }

        $result = [];

        foreach ($type as $typeId) {
            $result = array_merge($result, $this->getRecordDataForType($host, $typeId, $useExtendedRecords, $keepOrder));
        }

        if ($removeDuplicates) {
            $result = DnsUtils::removeDuplicates($result);
        }

        return $result;
    }

    /**
     * @param string $host
     * @param $typeId
     * @param bool $useExtendedRecords
     * @param bool $keepOrder
     * @return array|RecordInterface[]
     * @throws DnsHandlerException
     * @throws RecordException
     * @throws RawDataException
     */
    private function getRecordDataForType(string $host, $typeId, bool $useExtendedRecords, bool $keepOrder): array
    {
        $recordsData = $this->handler->getDnsData($host, $typeId);

        if (empty($recordsData)) {
            return [];
        }

        $result = [];

        foreach ($recordsData as $recordData) {
            $record = $this->factory->create($recordData, $useExtendedRecords);
            if ($record->getTypeId() === $typeId) {
                $result[] = $record;
            }
        }

        if ($keepOrder) {
            $result = DnsUtils::sortRecords($result);
        }

        return $result;
    }

}
