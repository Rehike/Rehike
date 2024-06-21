<?php

namespace BlueLibraries\Dns\Handlers\Raw;

use Exception;

class RawDataException extends Exception
{
    public const ERR_INVALID_CLASS_ID = 1;
    public const ERR_INVALID_ADDRESS = 2;
}
