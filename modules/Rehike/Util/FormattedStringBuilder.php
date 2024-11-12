<?php
namespace Rehike\Util;

use Rehike\FormattedString;

use Rehike\Util\FormattedStringBuilder\{
    PrintfTemplateBuilderParams,
    RunBuilder
};

/**
 * Builder for formatted strings like InnerTube.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class FormattedStringBuilder
{    
    // Flags for run creation
    public const RUN_AS_LINK        = 0b0001;
    public const RUN_DISPLAY_BOLD   = 0b0010;
    public const RUN_DISPLAY_ITALIC = 0b0100;
    
    protected array $runs = [];
    
    /**
     * Build the formatted string.
     */
    public function build(): FormattedString
    {
        $out = new FormattedString(FormattedString::FORMATTED_STRING_FORMATTED);
        $out->runs = $this->runs;
        
        return $out;
    }
    
    /**
     * Create a RunBuilder.
     */
    public function createRunBuilder(): RunBuilder
    {
        return new RunBuilder();
    }
    
    /**
     * Create a run and add it to our list.
     */
    public function createAndAddRun(
            string $runText, 
            int $runCreationFlags = 0, 
            string $linkText = "", 
            array $extraData = [] // reserved
    ): static
    {
        $builder = $this->createRunBuilder();
        
        $builder->setText($runText);
        
        if ($runCreationFlags & self::RUN_AS_LINK)
        {
            $builder->setEndpointFromUrl($linkText);
        }
        
        if ($runCreationFlags & self::RUN_DISPLAY_BOLD)
        {
            $builder->setBold(true);
        }
        
        if ($runCreationFlags & self::RUN_DISPLAY_ITALIC)
        {
            $builder->setItalic(true);
        }
        
        $this->addRunFromBuilder($builder);
        
        return $this;
    }
    
    /**
     * Add a run from a RunBuilder.
     */
    public function addRunFromBuilder(RunBuilder $builder): static
    {
        $run = $builder->build();
        $this->runs[] = $run;
        
        return $this;
    }
    
    /**
     * Parse from printf-style templates.
     * 
     * We use this for i18n typically.
     * 
     * @param string[]|array[] $templates
     */
    public function parseFromPrintfTemplates(
            PrintfTemplateBuilderParams $main,
            PrintfTemplateBuilderParams ...$others
    ): static
    {
        if (strpos($main->runText, "%") === false)
        {
            $this->createAndAddRun(
                $main->runText,
                $main->runCreationFlags,
                $main->linkText,
                $main->extraData
            );
            
            return $this;
        }
        
        // Parse all the other strings first:
        $childParser = new FormattedStringBuilder();
        
        foreach ($others as $other)
        {
            $childParser->createAndAddRun(
                $other->runText,
                $other->runCreationFlags,
                $other->linkText,
                $other->extraData
            );
        }
        
        $parsedOthers = $childParser->build()->runs;
        unset($childParser);
        
        // Explode while keeping %s delimiter for easy parsing.
        $PART_REGEX = "/(%(\d\$)?s)/";
        $parts = preg_split($PART_REGEX, $main->runText, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $curPart = 0;
        foreach ($parts as $i => $part)
        {
            if (empty($part))
            {
                continue;
            }
            else if ($part[0] == "%" && preg_match($PART_REGEX, $main->runText, $matches))
            {
                $otherIndex = $curPart++;
                
                if ($matches[2])
                {
                    $otherIndex = (int)$matches[2];
                    --$curPart;
                }
                
                $this->runs[] = $parsedOthers[$otherIndex];
            }
            else
            {
                $this->createAndAddRun(
                    $part,
                    $main->runCreationFlags,
                    $main->linkText,
                    $main->extraData
                );
            }
        }
        
        return $this;
    }
}