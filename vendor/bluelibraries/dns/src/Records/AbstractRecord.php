<?php

namespace BlueLibraries\Dns\Records;

use JsonSerializable;

abstract class AbstractRecord implements RecordInterface, JsonSerializable
{

    protected array $data = [];

    public abstract function getTypeId(): int;

    public function getTypeName(): string
    {
        return RecordTypes::getName($this->getTypeId());
    }

    public function __construct(?array $data = [])
    {
        $this->setData($data);
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        if (!isset($this->data['host'])) {
            $this->data['host'] = '';
        } else {
            $this->data['host'] = strtolower(trim($this->data['host']));
        }

        if (!isset($this->data['ttl'])) {
            $this->data['ttl'] = 0;
        }

        if (!isset($this->data['class'])) {
            $this->data['class'] = 'IN';
        }

        if (!isset($this->data['type'])) {
            $this->data['type'] = RecordTypes::getName($this->getTypeId());
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getHost(): ?string
    {
        return $this->data['host'] ?? null;
    }

    public function getClass(): ?string
    {
        return $this->data['class'] ?? null;
    }

    public function getTtl(): ?int
    {
        return isset($this->data['ttl'])
            ? (int)$this->data['ttl']
            : null;
    }

    public function toString(string $separator = ' '): string
    {
        return implode(
            $separator,
            DnsRecordProperties::getFilteredProperties(
                $this->getTypeId(),
                $this->getParsedData($this->data)
            )
        );
    }

    public function __toString() {
        return $this->toString();
    }

    private function makeString(array $array): string
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = is_array($value) ? $this->makeString($value) : $value;
        }

        return implode('', $result);
    }

    public function getHash(): string
    {
        return md5($this->makeString($this->toBaseArray()));
    }

    public function toBaseArray(): array
    {
        $data = $this->toArray();
        $expiringKeys = DnsRecordProperties::getExcludedBaseProperties();

        foreach ($expiringKeys as $expiringKey) {
            if (isset($data[$expiringKey])) {
                unset($data[$expiringKey]);
            }
        }

        return $data;
    }

    private function getParsedData(array $data): array
    {
        $result = [];

        foreach ($data as $propertyName => $value) {
            $result[$propertyName] = $this->getParsedProperty($propertyName, $value);
        }

        return $result;
    }

    /**
     * @param $propertyName
     * @param $value
     * @return string
     */
    private function getParsedProperty($propertyName, $value): string
    {

        $result = DnsRecordProperties::isWrappedProperty($propertyName)
            ? '"' . DnsUtils::sanitizeRecordTxt($value) . '"'
            : $value;

        if (DnsRecordProperties::isUnWrappedDotValue($propertyName, $value)) {
            $result = trim($value, '"');
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}
