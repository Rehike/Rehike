<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Parser;

use YukisCoffee\CoffeeTranslation\Parsing\{
    IStringParser,
    StringParserFactory
};

use YukisCoffee\CoffeeTranslation\Exception\ParserException;

use YukisCoffee\CoffeeTranslation\Lang\{
    Record\LanguageRecord,
    Record\RecordEntries,
    SourceInfo
};

/**
 * Parser for i18n files.
 * 
 * These files use a custom data serialization language, which is very similar
 * to a reduced dialect of YAML (mostly so YAML syntax highlighters can be used
 * for it).
 * 
 * This is designed to be an ultra fast parser, so it only parses through the
 * file linearly, and no tokenization or preprocessing is done on the input.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class RecordFileParser
{
    /**
     * The internal string parser.
     */
    private IStringParser $parser;

    /**
     * Information about the original file.
     */
    private SourceInfo $sourceFile;

    /**
     * The current line that the parser is working on.
     */
    private int $curLine = 0;

    /**
     * The location in the source file at which the current line starts.
     */
    private int $lineIndex = 0;

    /**
     * @see parse()
     * @param $file The contents of the serialized file to parse.
     */
    protected function __construct(SourceInfo $file)
    {
        $this->sourceFile = $file;
        $this->parser = StringParserFactory::getStringParserForFile($file);
        $this->curLine = 1;
    }

    /**
     * Parse an i18n record.
     */
    public static function parse(SourceInfo $file): LanguageRecord
    {
        $instance = new self($file);
        return $instance->_parse();
    }

    /**
     * Internal parse function.
     * 
     * This handles the parsing of all top-level constructs, including comments
     * and empty lines.
     */
    protected function _parse(): LanguageRecord
    {
        $parsedEntries = new RecordEntries;

        while (!$this->parser->isOutOfBounds())
        {
            // Parse any possible top-level token
            switch ($t = $this->parser->read())
            {
                case Tokens::TOKEN_COMMENT:
                    $this->skipOverComment();
                    break;
                
                case Tokens::TOKEN_CARRIAGE_RETURN:
                case Tokens::TOKEN_TAB:
                case Tokens::TOKEN_SPACE:
                case Tokens::TOKEN_COMMA:
                    $this->parser->next();
                    break;

                case Tokens::TOKEN_LINE_BREAK:
                    $this->parseLineBreak();
                    break;
                
                default:
                    // Top-level definitions may not be arrays, so numbers will
                    // be skipped anyways.
                    if (ParserUtils::isAlphaChar($t))
                    {
                        $this->parseDeclaration($parsedEntries);
                        break;
                    }

                    // Anything else is invalid, so I error.
                    throw new ParserException(sprintf(
                        "Unexpected character \"%s\" at %s",
                        $t,
                        $this->getFormattedErrorLocation()
                    ));
            }
        }

        return new LanguageRecord($parsedEntries);
    }

    /**
     * Gets the standard error message fragment containing the line count and
     * column number.
     */
    protected function getFormattedErrorLocation(): string
    {
        return sprintf(
            "line %d, column %d in file \"%s\"",
            $this->curLine,
            $this->parser->getCursor() - $this->lineIndex,
            $this->sourceFile->getName()
        );
    }

    /**
     * Determines if the currently read character is a line break token.
     */
    protected function isLineBreak(): bool
    {
        return $this->parser->read() == Tokens::TOKEN_LINE_BREAK;
    }

    /**
     * Reads the current character, expecting it to be a specific token.
     * 
     * If the function succeeds, then the cursor will be incremented by default.
     * Otherwise, an exception will be thrown.
     */
    protected function readExpectToken(string $token, bool $curse = true): string
    {
        $result = "";

        $tokenSize = strlen($token);

        for ($i = 0; $i < $tokenSize; $i++)
        {
            $result .= $this->parser->read($i, 1);
        }

        if ($result != $token)
        {
            throw new ParserException(sprintf(
                "Unexpected token \"%s\" at %s. Expected \"%s\".",
                $result,
                $this->getFormattedErrorLocation(),
                $token
            ));
        }

        if ($curse)
            $this->parser->skip($tokenSize);

        return $result;
    }

    /**
     * Reads the next alphanumeric sequence, without any spaces or other
     * characters. These are characters: A-Z, a-z, 0-9, and "_".
     * 
     * This method increments the cursor by default.
     */
    protected function readNextSymbol(bool $curse = true): string
    {
        $result = "";

        while (!$this->parser->isOutOfBounds())
        {
            $cur = $this->parser->read();

            if (ParserUtils::isValidSymbolChar($cur))
            {
                $result .= $cur;

                if ($curse)
                {
                    $this->parser->next();
                }
            }
            else
            {
                break;
            }
        }

        return $result;
    }

    /**
     * Parses the next sequences of characters as a number. For pattern
     * matching, the digits 0-9 are checked, as well as the . character
     * for the decimal point.
     * 
     * This method increments the cursor by default.
     */
    protected function parseNextNumber(bool $curse = true): float
    {
        $result = "";

        while (!$this->parser->isOutOfBounds())
        {
            $cur = $this->parser->read();

            if (ParserUtils::isNumberChar($cur) || '.' == $cur)
            {
                $result .= $cur;

                if ($curse)
                {
                    $this->parser->next();
                }
            }
            else
            {
                break;
            }
        }

        return (float)$result;
    }

    /**
     * Parses the next sequence of characters as a string. This includes all
     * characters between quote characters, as well as common escape sequences.
     * 
     * This method increments the cursor by default.
     */
    protected function parseNextString(string $quoteCharacter): string
    {
        if (!$this->parser->read() == $quoteCharacter)
        {
            throw new ParserException(sprintf(
                "Internal parser error at string around %s.",
                $this->getFormattedErrorLocation()
            ));
        }

        $this->parser->next();

        $result = "";

        while (!$this->parser->isOutOfBounds())
        {
            $cur = $this->parser->read();

            if (Tokens::TOKEN_ESCAPE == $cur)
            {
                $nextToken = $this->parser->read(1, 1);
                switch ($nextToken)
                {
                    case Tokens::TOKEN_DOUBLE_QUOTES:
                    case Tokens::TOKEN_SINGLE_QUOTES:
                    case Tokens::TOKEN_ESCAPE:
                        $result .= $nextToken;
                        $this->parser->skip(2); // Skip \ and next character
                        break;
                    
                    case "n":
                        $result .= "\n";
                        $this->parser->next();
                        break;
                    
                    case "t":
                        $result .= "\t";
                        $this->parser->next();
                        break;
                    
                    case "r":
                        $result .= "\r";
                        $this->parser->next();
                        break;

                    default:
                        // Skip over any other sequence and just print a normal
                        // backslash:
                        $result .= "\\";
                        $this->parser->skip(2);
                        break;

                    // A double quoted string must not span multiple lines:
                    case Tokens::TOKEN_CARRIAGE_RETURN:
                    case Tokens::TOKEN_LINE_BREAK:
                        throw new ParserException(sprintf(
                            "Unterminated quoted string literal at %s.",
                            $this->getFormattedErrorLocation()
                        ));
                }
            }
            else if ($quoteCharacter != $cur)
            {
                $result .= $cur;
                $this->parser->next();
            }
            else if ($quoteCharacter == $cur)
            {
                // Read all the way to the end of the quote, then break
                // the loop.
                $this->parser->next();
                break;
            }
        }

        return $result;
    }

    /**
     * Responsible for updating global state after a line break has been parsed.
     */
    protected function handleLineBreak(): void
    {
        // Currently, we are at "\n"
        $this->lineIndex = $this->parser->getCursor() + 1;
        $this->curLine++;
    }

    /**
     * Parses the next line break control character.
     */
    protected function parseLineBreak(): void
    {
        $this->parser->next();
        $this->handleLineBreak();
    }

    /**
     * Skips over the next comment.
     * 
     * Since this language only supports single-line comments, this also starts
     * parsing a line break.
     */
    protected function skipOverComment(): void
    {
        while (!$this->parser->isOutOfBounds() && !$this->isLineBreak())
            $this->parser->next();

        // Handle the line break character as well:
        if (!$this->parser->isOutOfBounds() && $this->isLineBreak())
            $this->parseLineBreak();
    }

    /**
     * Skips over the next sequence of whitespace characters.
     */
    protected function skipWhitespace(): void
    {
        $whitespaceTokens = [
            Tokens::TOKEN_SPACE,
            Tokens::TOKEN_TAB,
            Tokens::TOKEN_CARRIAGE_RETURN
        ];

        while (in_array($this->parser->read(), $whitespaceTokens) && !$this->parser->isOutOfBounds())
        {
            $this->parser->next();
        }
    }

    /**
     * Skips parsing to the next line in the source file.
     */
    protected function skipToNextLine(): void
    {
        while (!$this->parser->isOutOfBounds())
        {
            $ch = $this->parser->read();

            if (Tokens::TOKEN_LINE_BREAK == $ch)
            {
                $this->parseLineBreak();
                return;
            }

            $this->parser->next();
        }
    }

    /**
     * Gets the indentation of the current line or overflows to the next line.
     */
    protected function getIndentation(): int
    {
        $indent = 0;

        while (!$this->parser->isOutOfBounds())
        {
            $cur = $this->parser->read();

            switch ($cur)
            {
                case Tokens::TOKEN_TAB:
                case Tokens::TOKEN_SPACE:
                    $indent++;
                    $this->parser->next();
                    break;
                
                // We only support full-line comments, so there's no reason to
                // count these as anything at all.
                case Tokens::TOKEN_COMMENT:
                    $this->skipOverComment();
                    $indent = 0;
                    break;
                
                // Carriage return should always come before newline, so it is
                // just skipped over.
                case Tokens::TOKEN_CARRIAGE_RETURN:
                    $this->parser->next();
                    break;
                
                case Tokens::TOKEN_LINE_BREAK:
                    $this->parseLineBreak();
                    $indent = 0;
                    break;

                // Any other character should let us know to stop:
                default:
                    return $indent;
            }
        }

        return -1;
    }

    /**
     * Parses a key to value declaration, or a nested declaration map.
     */
    protected function parseDeclaration(
            RecordEntries $entries,
            int $indentation = 0
    ): bool
    {
        $name = $this->readNextSymbol(true);
        $value = null;
        $this->readExpectToken(Tokens::TOKEN_COLON);
        $this->skipWhitespace();

        switch ($t = $this->parser->read())
        {
            case Tokens::TOKEN_SINGLE_QUOTES:
            case Tokens::TOKEN_DOUBLE_QUOTES:
                $value = $this->parseNextString($t);
                break;
            
            case Tokens::TOKEN_MULTILINE_STRING:
                // Parse until next newline, then begin parsing the multiline
                // string:
                $this->skipToNextLine();
                $value = $this->parseMultilineString($indentation + 1);
                break;

            // We are dealing with a nested one if this is the case, since this
            // is the immediately following token to the colon.
            case Tokens::TOKEN_COMMENT:
                $this->skipOverComment();
                $alreadyHandledLineBreak = true;
                // fallthrough //

            case Tokens::TOKEN_LINE_BREAK:
                if (!isset($alreadyHandledLineBreak) || !$alreadyHandledLineBreak)
                {
                    $this->parseLineBreak();
                }

                $nextIndent = $this->getIndentation();

                // Set the value to a new set of record entries:
                $value = new RecordEntries;

                // Recurse:
                while ($this->parseDeclaration($value, $nextIndent));
                break;

            // If anything else is the following character, then we are working
            // with either an unquoted string or a number/boolean literal.
            default:
                $value = $this->parseLiteralValue();
                break;
        }

        $entries->{$name} = $value;

        // Handle recursion:
        $nextIndent = $this->getIndentation();
        if (
            $indentation != 0 && (
                $nextIndent == $indentation
            )
        )
        {
            // If there's a successive entry and we're already parsing a nested
            // declaration, then continue on.
            return true;
        }
        else
        {
            $this->parser->rewind($nextIndent);
        }

        return false;
    }

    /**
     * Parse the next multiline string starting from the cursor.
     * 
     * A multiline string is not quoted, and is indented from its parent.
     */
    protected function parseMultilineString(int $minimumIndent = 1): string
    {
        $strIndent = $this->getIndentation();
        $result = "";

        if ($strIndent < $minimumIndent)
        {
            throw new ParserException(sprintf(
                "Inadequate indentation level (probably negative) at %s.",
                $this->getFormattedErrorLocation()
            ));
        }

        while (!$this->parser->isOutOfBounds())
        {
            $ch = $this->parser->read();

            switch ($ch)
            {
                case Tokens::TOKEN_ESCAPE:
                    switch ($this->parser->read(1))
                    {
                        // Alternatively parse a new line if an escape sequence
                        // precedes it. This avoids any reformatting that would
                        // otherwise be performed.
                        case Tokens::TOKEN_CARRIAGE_RETURN:
                        case Tokens::TOKEN_LINE_BREAK:
                            $this->skipToNextLine();
                            $lineIndent = $this->getIndentation();

                            if ($lineIndent < $strIndent)
                            {
                                break 3; // the while loop
                            }
                            break;

                        case Tokens::TOKEN_ESCAPE:
                        default:
                            $result .= Tokens::TOKEN_ESCAPE;
                            break;
                    }
                    break;

                case Tokens::TOKEN_LINE_BREAK:
                    $this->parseLineBreak();
                    $lineIndent = $this->getIndentation();

                    if ($lineIndent >= $strIndent)
                    {
                        // String continues here. Typically, it is desirable to
                        // concatenate both strings with one line, so we trim
                        // the original string by default, and then add another
                        // space. This behavior can be disabled by putting a \
                        // character before the end of a line, if desired.
                        $result = rtrim($result);
                        $result .= ' ';
                    }
                    else
                    {
                        $this->parser->rewind($lineIndent);
                        break 2; // the while loop
                    }
                    break;

                // As usual, we just assume that a newline always follows a
                // carriage return, so we ignore it.
                case Tokens::TOKEN_CARRIAGE_RETURN:
                    $this->parser->next();
                    break;

                default:
                    $result .= $ch;
                    $this->parser->next();
                    break;
            }
        }

        return $result;
    }

    /**
     * Parses a literal value, which in this language, may be a numeric literal
     * (int or float), a boolean literal, or an unquoted string.
     */
    protected function parseLiteralValue(): string|bool|float
    {
        $buffer = "";

        while (!$this->parser->isOutOfBounds())
        {
            $ch = $this->parser->read();

            switch ($ch)
            {
                case Tokens::TOKEN_CARRIAGE_RETURN:
                    $this->parser->next();
                    break;

                case Tokens::TOKEN_LINE_BREAK:
                    $this->handleLineBreak();
                    break 2; // the while loop

                case Tokens::TOKEN_COMMENT:
                    $this->skipOverComment();
                    break 2; // the while loop

                default:
                    $buffer .= $ch;
                    $this->parser->next();
                    break;
            }
        }

        $trimmedBuffer = trim($buffer);

        if (ParserUtils::isNumberChar(str_replace(".", "", $trimmedBuffer)))
        {
            return (float)$buffer;
        }
        else
        {
            switch ($trimmedBuffer)
            {
                case Tokens::KEYWORD_TRUE:
                    return true;
                
                case Tokens::KEYWORD_FALSE:
                    return false;
            }
        }

        return $trimmedBuffer;
    }
}