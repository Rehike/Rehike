<?php
namespace Rehike;

/**
 * A class representing a formatted string, similar to InnerTube's formatted
 * strings.
 * 
 * @author The Rehike Maintainers
 */
class FormattedString
{
    public const FORMATTED_STRING_SIMPLE = 0;
    public const FORMATTED_STRING_FORMATTED = 1;

    /**
     * @var FormattedStringRun[]
     */
    public array $runs;

    public string $simpleText;

    /**
     * @param $type FORMATTED_STRING_SIMPLE or FORMATTED_STRING_FORMATTED
     */
    public function __construct(int $type = self::FORMATTED_STRING_FORMATTED)
    {
        switch ($type)
        {
            case self::FORMATTED_STRING_SIMPLE:
                unset($this->runs);
                $this->simpleText = "";
                return;

            case self::FORMATTED_STRING_FORMATTED:
                unset($this->simpleText);
                $this->runs = [];
                return;
        }

        throw new \Exception("Invalid argument to FormattedString::__construct");
    }

    private const PARSER_STATE_NONE = 0x0;   // 0b00
    private const PARSER_STATE_ITALIC = 0x1; // 0b01
    private const PARSER_STATE_BOLD = 0x2;   // 0b10

    /**
     * Create a formatted string from a template.
     * 
     * The templates use syntax similar to markdown, that is:
     * 
     * **bold**
     * *italic*
     * ***bold and italic***
     * \*not bold\*
     */
    public static function fromTemplate(string $template): FormattedString
    {
        $out = new FormattedString;

        $lastLastChar = '';
        $lastChar = '';
        $parserState = self::PARSER_STATE_NONE;
        $asteriskCounter = 0;
        $currentNode = new FormattedStringRun;
        $committedNotes = [];
        $parsingAsterisks = false;
        $endOfParser = false;
        for ($i = 0, $len = strlen($template); $i < $len; $i++)
        {
            $cur = $template[$i];

            $hasAlreadyCommitted = false;

            if (0 == ord($template))
            {
                $currentNode->text .= $template[$i];
                continue;
            }
            else if ('*' == $cur)
            {
                $parsingAsterisks = true;

                if ($lastChar != '\\' && $lastLastChar != '\\')
                {
                    $asteriskCounter++;
                }
                else
                {
                    $asteriskCounter = 0;
                    $parsingAsterisks = false;
                }

                if ($i == $len - 1)
                {
                    $endOfParser = true;
                    goto END_OF_PARSER_HACK;
                }
            }
            else if ($parsingAsterisks) // finishing
            {
                // sorry I got lazy...
                END_OF_PARSER_HACK:

                $parsingAsterisks = false;
                if ($asteriskCounter > 0)
                {
                    $initialParserState = $parserState;

                    // We hardcode 1 and 2, and 3 occurs naturally. No other
                    // cases are possible at the moment.
                    if ($asteriskCounter & 1)
                    {
                        $parserState ^= self::PARSER_STATE_ITALIC;
                    }

                    if ($asteriskCounter & 2)
                    {
                        $parserState ^= self::PARSER_STATE_BOLD;
                    }

                    // We commit the changes leftward of the asterisks, so we
                    // always use the initial parser state.
                    if ($initialParserState & self::PARSER_STATE_ITALIC)
                        $currentNode->italic = true;

                    if ($initialParserState & self::PARSER_STATE_BOLD)
                        $currentNode->bold = true;

                    if (!empty($currentNode->text))
                    {
                        $committedNotes[] = $currentNode;
                        $currentNode = new FormattedStringRun;
                        $hasAlreadyCommitted = true;
                    }
                }

                if ($endOfParser)
                    break;

                $i--;
                $asteriskCounter = 0;
                continue;
            }
            else
            {
                // Add to the current node's text:
                $currentNode->text .= $template[$i];
            }

            // Commit if we are at the end and it hasn't already been committed.
            if (!$hasAlreadyCommitted && $i == $len - 1)
                $committedNotes[] = $currentNode;

            $lastLastChar = $lastChar;
            $lastChar = $cur;
        }

        $out->runs = $committedNotes;

        return $out;
    }
}

/**
 * @internal
 */
class FormattedStringRun
{
    public string $text = "";
    public bool $bold = false;
    public bool $italic = false;
}