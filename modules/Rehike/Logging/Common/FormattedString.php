<?php
namespace Rehike\Logging\Common;

/**
 * A simple formatted string system for the logger.
 * 
 * @author The Rehike Maintainers
 */
class FormattedString
{
    /**
     * Contains arrays of each run of the formatted string.
     */
    private array $runs = [];

    /**
     * Get every run of the formatted string as necessary.
     */
    public function getRuns(): array
    {
        return $this->runs;
    }

    /**
     * Get the formatted string as raw text.
     */
    public function getRawText(): string
    {
        $out = "";

        foreach ($this->runs as $run)
        {
            $out .= $run["text"] ?? "";
        }

        return $out;
    }

    /**
     * Manually add a run with custom metadata to the formatted string.
     */
    public function addRun(string $text, array $metadata): void
    {
        $this->runs[] = $metadata + ["text" => $text];
    }

    /**
     * Add regular text to the formatted string.
     */
    public function addText(string $text): void
    {
        $this->runs[] = [
            "text" => $text
        ];
    }

    /**
     * Add text with a metadata tag to the run.
     * 
     * This can be used for displaying the text with certain markup effects.
     */
    public function addTaggedText(string $text, string $tag): void
    {
        $this->runs[] = [
            "text" => $text,
            "tag"  => $tag
        ];
    }

    /**
     * Add a navigation link to the formatted string.
     */
    public function addNavigationText(string $text, string $href): void
    {
        $this->runs[] = [
            "text" => $text,
            "endpoint" => $href
        ];
    }

    /**
     * Remove a run from the formatted string.
     * 
     * @return bool True on success or false on failure.
     */
    public function removeRun(int $index): bool
    {
        if ($index < 0 || $index > count($this->runs))
        {
            return false;
        }

        array_splice($this->runs, $index, 1);

        return true;
    }
}