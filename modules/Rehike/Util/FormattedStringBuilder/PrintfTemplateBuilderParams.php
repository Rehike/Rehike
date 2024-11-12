<?php
namespace Rehike\Util\FormattedStringBuilder;

/**
 * Arguments for the printf template builder method.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PrintfTemplateBuilderParams
{
    public function __construct(
            public string $runText, 
            public int $runCreationFlags = 0, 
            public string $linkText = "", 
            public array $extraData = [] // reserved
    ) {}
}