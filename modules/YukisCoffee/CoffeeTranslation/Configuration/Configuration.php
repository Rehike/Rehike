<?php
namespace YukisCoffee\CoffeeTranslation\Configuration;

/**
 * Stores global framework configuration.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class Configuration
{
    /**
     * The root directory of the language files.
     * 
     * If the router is not file-system-based, then this configuration property
     * can be ignored.
     */
    private string $rootDirectory = "";

    /**
     * The default file extension to be used by a file-system-based router.
     * 
     * If the router is not file-system-based, then this configuration property
     * can be ignored.
     */
    private string $defaultFileExtension = "i18n";

    /**
     * The file name to be used for locating the culture descriptor file.
     * 
     * If the router is not file-system-based, then this configuration property
     * can be ignored.
     */
    private string $cultureFileName = "_culture.i18n";

    /**
     * The default language ID to be used.
     * 
     * This is used as a final fallback language.
     */
    private string $defaultLanguageId = "en-US";

    /**
     * Lists preferred user languages.
     * 
     * These will be queried, in order, in searching for a given translation
     * set.
     * 
     * @var string[]
     */
    private array $preferredLanguageIds = [ "en-US" ];

    /**
     * Determines if the language API should throw an exception in event that
     * it cannot find a requested string ID.
     * 
     * This is mostly useful for debugging purposes.
     */
    private bool $exceptionOnFailure = false;

    /**
     * Gets the default language ID.
     */
    public function getDefaultLanguageId(): string
    {
        return $this->defaultLanguageId;
    }

    /**
     * Gets the user's preferred language IDs.
     */
    public function getPreferredLanguageIds(): array
    {
        return $this->preferredLanguageIds;
    }

    /**
     * Get the root directory for accessing language files.
     */
    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    /**
     * Get the default file extension for file-system-based routing.
     */
    public function getDefaultFileExtension(): string
    {
        return $this->defaultFileExtension;
    }

    /**
     * Get the default culture file name for file-system-based routing.
     */
    public function getCultureFileName(): string
    {
        return $this->cultureFileName;
    }

    /**
     * Determine if the user allows exceptions in the event that a string
     * cannot be found.
     */
    public function getExceptionOnFailureState(): bool
    {
        return $this->exceptionOnFailure;
    }

    /**
     * Set the default language ID.
     */
    public function setDefaultLanguageId(string $id): self
    {
        $this->defaultLanguageId = $id;

        return $this;
    }

    /**
     * Set the preferred language ID list.
     * 
     * @param string[] $ids
     */
    public function setPreferredLanguageIds(array $ids): self
    {
        $this->preferredLanguageIds = $ids;

        return $this;
    }

    /**
     * Adds a language ID to the preferred language IDs list.
     */
    public function addPreferredLanguageId(string $id): self
    {
        array_unshift($this->preferredLanguageIds, $id);

        return $this;
    }

    /**
     * Set the current user language ID.
     * 
     * This may be a single ID, or a comma-delinated list. This simply sets the
     * preferred language list.
     */
    public function setCurrentLanguageId(string $idList): self
    {
        $this->preferredLanguageIds = array_filter(
            explode(',', $idList),
            "trim" // trim whitespace function
        );

        return $this;
    }

    /**
     * Set the root directory for accessing language files.
     */
    public function setRootDirectory(string $dir): self
    {
        $this->rootDirectory = $dir;

        return $this;
    }

    /**
     * Set the default file extension for file-system-based routing.
     */
    public function setDefaultFileExtension(string $ext): self
    {
        $this->defaultFileExtension = $ext;

        return $this;
    }

    /**
     * Sets the culture file name for file-system-based routing.
     */
    public function setCultureFileName(string $newName): self
    {
        $this->cultureFileName = $newName;

        return $this;
    }

    /**
     * Enables or disables throwing exceptions on failure.
     */
    public function setExceptionOnFailure(bool $state): self
    {
        $this->exceptionOnFailure = $state;

        return $this;
    }
}