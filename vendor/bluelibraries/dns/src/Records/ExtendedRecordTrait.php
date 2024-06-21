<?php

namespace BlueLibraries\Dns\Records;

trait ExtendedRecordTrait
{

    public function getTypeName(): string
    {
        return $this->getExtendedTypeName();
    }

}
