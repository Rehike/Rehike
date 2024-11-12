<?php
namespace Rehike\SignInV2\Parser;

/**
 * 
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class AccountHeaderInfo
{
    public function __construct(
        public string $email,
        public ?string $displayName = null,
        public bool $isDefault = false,
    )
    {}
}