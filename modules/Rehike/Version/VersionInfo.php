<?php
namespace Rehike\Version;

/**
 * Rehike version information structure.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class VersionInfo
{
    public bool $supportsDotGit = false;
    public bool $isRelease = false;
    public int $buildNumber = 0;
    public ?int $time = null;
    public ?int $currentRevisionId = null;
    public ?string $previousHash = null;
    public ?string $currentHash = null;
    public ?string $subject = null;
    public ?string $body = null;
    public ?string $branch = null;
    public ?string $committerName = null;
    public ?string $committerEmail = null;
    public ?string $semanticVersion = null;
    public bool $expectRemoteGit = false;
    public ?array $remoteGit = null;
}