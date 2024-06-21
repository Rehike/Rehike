<?php

namespace BlueLibraries\Dns\Handlers;

interface DnsHandlerInterface
{

    public function getType(): string;

    public function canUseIt(): bool;

    public function getDnsData(string $host, int $typeId): array;

    public function getRetries(): int;

    public function setRetries(int $retries): self;

    public function getTimeout(): int;

    public function setTimeout(int $timeout): self;

    /**
     * @throws DnsHandlerException
     */
    public function setNameserver(?string $nameserver): self;

}