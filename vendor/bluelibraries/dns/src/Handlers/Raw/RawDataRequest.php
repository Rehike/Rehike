<?php

namespace BlueLibraries\Dns\Handlers\Raw;

use BlueLibraries\Dns\Records\DnsUtils;

class RawDataRequest
{
    /**
     * See RawClassTypes.php
     */
    private int $classId = 1;

    private ?string $domain = null;
    private ?int $typeId = null;
    private ?int $timeout = null;
    private ?int $id = null;
    private bool $isRecursionDesired = false;
    private bool $useAuthoritativeAnswer = true;
    private bool $useTruncation = false;
    private bool $useRecursionIfAvailable = false;

    /**
     * @param string|null $domain
     * @param int|null $typeId
     * @param int|null $timeout
     */
    public function __construct(?string $domain = null, ?int $typeId = null, ?int $timeout = 30)
    {
        $this->domain = $domain;
        $this->typeId = $typeId;
        $this->timeout = $timeout;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string|null $domain
     * @return self
     */
    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    /**
     * @param int|null $typeId
     * @return self
     */
    public function setTypeId(?int $typeId): self
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * @param int|null $timeout
     * @return self
     */
    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getClassId(): int
    {
        return $this->classId;
    }

    /**
     * @param int $classId
     * @return self
     */
    public function setClassId(int $classId): self
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRecursionDesired(): bool
    {
        return $this->isRecursionDesired;
    }

    /**
     * @param bool $isRecursionDesired
     * @return self
     */
    public function setIsRecursionDesired(bool $isRecursionDesired): self
    {
        $this->isRecursionDesired = $isRecursionDesired;
        return $this;
    }

    /**
     * @return bool
     */
    public function useAuthoritativeAnswer(): bool
    {
        return $this->useAuthoritativeAnswer;
    }

    /**
     * @param bool $useAuthoritativeAnswer
     * @return self
     */
    public function setUseAuthoritativeAnswer(bool $useAuthoritativeAnswer): self
    {
        $this->useAuthoritativeAnswer = $useAuthoritativeAnswer;
        return $this;
    }

    /**
     * @return bool
     */
    public function useTruncation(): bool
    {
        return $this->useTruncation;
    }

    /**
     * @param bool $useTruncation
     * @return self
     */
    public function setUseTruncation(bool $useTruncation): self
    {
        $this->useTruncation = $useTruncation;
        return $this;
    }

    /**
     * @return bool
     */
    public function useRecursionIfAvailable(): bool
    {
        return $this->useRecursionIfAvailable;
    }

    /**
     * @param bool $useRecursionIfAvailable
     * @return self
     */
    public function setUseRecursionIfAvailable(bool $useRecursionIfAvailable): self
    {
        $this->useRecursionIfAvailable = $useRecursionIfAvailable;
        return $this;
    }

    /**
     * @return string
     * @throws RawDataException
     */
    public function generateHeader(): string
    {
        return $this->getBinaryId() .
            $this->getBinaryQR() .
            $this->getBinaryAuthoritativeAnswer() .
            $this->getBinaryUseTruncation() .
            $this->getBinaryRecursionDesired() .
            $this->getBinaryRecursionAvailable() .
            $this->getBinaryQuestion() .
            $this->getBinaryType() .
            $this->getBinaryClass() .
            $this->getTtl();
    }

    protected function getBinaryId(): string
    {
        $this->id = $this->id ?? rand(0, 65535);
        return pack('n', $this->id);
    }

    /**
     * @return string
     *
     */
    protected function getBinaryQR(): string
    {
        $flags = 0x0100 & 0x0300; // recursion & query spec mask
        $opcode = 0x0000;
        return pack('n', $opcode | $flags);
    }

    protected function getBinaryAuthoritativeAnswer()
    {
        return pack('n', (int)$this->useAuthoritativeAnswer);
    }

    protected function getBinaryUseTruncation()
    {
        return pack('n', (int)$this->useTruncation);
    }

    protected function getBinaryRecursionDesired()
    {
        return pack('n', (int)$this->isRecursionDesired);
    }

    protected function getBinaryRecursionAvailable()
    {
        return pack('n', (int)$this->useRecursionIfAvailable);
    }

    /**
     * @throws RawDataException
     */
    protected function getBinaryQuestion(): ?string
    {
        $labels = $this->getLabels($this->domain);

        if (empty($labels)) {
            return null;
        }

        return implode('',
                array_map(function ($item) {
                    return pack("C", strlen($item)) . $item;
                }, $labels)
            ) . pack('C', 0);
    }

    protected function getBinaryType(): string
    {
        return pack('n', $this->typeId);
    }

    /**
     * @throws RawDataException
     */
    protected function getBinaryClass(): string
    {
        if (!in_array($this->classId, RawClassTypes::getRawTypes())) {
            throw new RawDataException(
                'Invalid class Id, got:' . json_encode($this->classId),
                RawDataException::ERR_INVALID_CLASS_ID
            );
        }

        return pack('n', $this->classId);
    }

    protected function getTtl()
    {
        return pack('N', $this->timeout);
    }

    public function getBinaryHeaderLength(int $headerLength)
    {
        return pack("n", $headerLength);
    }

    protected function getLabelsFromIp(string $ip): array
    {
        return array_merge(['in-addr', 'arpa'], array_reverse(explode('.', $ip)));
    }

    /**
     * @param string $address
     * @return array
     * @throws RawDataException
     */
    protected function getLabels(string $address): array
    {
        if (empty($address) || $address === '.') {
            return [];
        }
        if (filter_var($address, FILTER_VALIDATE_IP) !== false) {
            return $this->getLabelsFromIp($address);
        }

        if (!DnsUtils::isValidDomainOrSubdomain($address)) {
            throw new RawDataException(
                'Invalid address, it must be an IP or domain, got:' . json_encode($address),
                RawDataException::ERR_INVALID_ADDRESS
            );
        }

        return explode('.', strtolower($address));
    }

}
