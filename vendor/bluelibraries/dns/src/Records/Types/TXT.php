<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class TXT extends AbstractRecord
{

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function getTypeId(): int
    {
        return RecordTypes::TXT;
    }

    public function getTxt(): ?string
    {
        return $this->data['txt'] ?? null;
    }

    public function setData(array $data): AbstractRecord
    {
        if (isset($data['entries'])) {
            unset($data['entries']);
        }
        return parent::setData($data);
    }

}
