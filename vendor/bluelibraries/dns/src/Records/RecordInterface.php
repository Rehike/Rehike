<?php

namespace BlueLibraries\Dns\Records;

interface RecordInterface
{

    public function setData(array $data): self;

    public function getTypeId(): int;

    public function getTypeName(): string;

    public function getHost(): ?string;

    public function getClass(): ?string;

    public function getTtl(): ?int;

    public function toArray(): array;

    public function toBaseArray(): array;

    public function toString(string $separator = ' '): string;

    public function getHash(): string;

}
