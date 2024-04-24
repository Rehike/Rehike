<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Culture;

/**
 * Used for translating the English date-time strings which PHP returns natively
 * to other languages.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class DateTimeStringTranslator
{
    private const ENGLISH_WEEKDAYS = [
        1 => "Sunday",
        2 => "Monday",
        3 => "Tuesday",
        4 => "Wednesday",
        5 => "Thursday",
        6 => "Friday",
        7 => "Saturday"
    ];

    private const ENGLISH_WEEKDAYS_SHORT = [
        1 => "Sun",
        2 => "Mon",
        3 => "Tue",
        4 => "Wed",
        5 => "Thu",
        6 => "Fri",
        7 => "Sat"
    ];

    private const ENGLISH_MONTHS = [
        1 => "January",
        2 => "February",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8 => "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December"
    ];

    private const ENGLISH_MONTHS_SHORT = [
        1 => "Jan",
        2 => "Feb",
        3 => "Mar",
        4 => "Apr",
        5 => "May",
        6 => "Jun",
        7 => "Jul",
        8 => "Aug",
        9 => "Sep",
        10 => "Oct",
        11 => "Nov",
        12 => "Dec"
    ];

    private const ENGLISH_AM_PM = [
        "AM", "PM", "am", "pm"
    ];

    /**
     * Stores a reference to the culture strings of user language.
     */
    private CultureInfo $cultureInfo;

    /**
     * Stores the input original string.
     */
    private string $originalString;

    /**
     * Stores the active/result string which we will be translating.
     */
    private string $result;

    /**
     * Stores string region information.
     * 
     * Note that this functions like a stack, whereby a region definition is
     * only terminated by a higher number providing contradictory information.
     * 
     * The list is iterated in reverse, and cursor positions are compared to the
     * closest key entry in this map.
     */
    private array $regionInfoMap = [
        0 => [
            "isDirty" => false
        ]
    ];

    public function __construct(CultureInfo $cultureInfo, string $originalString)
    {
        static $counter = 0;
        $this->cultureInfo = $cultureInfo;
        $this->originalString = $originalString;
        $this->result = $originalString;
    }

    /**
     * Performs the translation and gets the result.
     */
    public function getTranslatedString(): string
    {
        /*
         * Typically, such an algorithm would be implemented to use
         * maximal munch: the longest words with a given prefix are matched
         * first, and then each subsequent shorter word.
         * 
         * We basically do this here, but since we're working with a pre-defined
         * subset of English-language words, we don't have to implement any
         * sorting algorithms.
         */
        
        $dt = $this->cultureInfo->dateTimeInfo;

        foreach (self::ENGLISH_WEEKDAYS as $i => $day)
        {
            $translation = $dt->daysOfWeek[$i];
            $this->replaceSubstringIfNotDirty($day, $translation);
        }

        foreach (self::ENGLISH_WEEKDAYS_SHORT as $i => $day)
        {
            $translation = $dt->shortDaysOfWeek[$i];
            $this->replaceSubstringIfNotDirty($day, $translation);
        }

        foreach (self::ENGLISH_MONTHS as $i => $month)
        {
            $translation = $dt->monthNames[$i];
            $this->replaceSubstringIfNotDirty($month, $translation);
        }

        foreach (self::ENGLISH_MONTHS_SHORT as $i => $month)
        {
            $translation = $dt->shortMonthNames[$i];
            $this->replaceSubstringIfNotDirty($month, $translation);
        }

        foreach (self::ENGLISH_AM_PM as $i => $word)
        {
            $translation = match(strtolower($word)) {
                "am" => $dt->am,
                "pm" => $dt->pm
            };
            $this->replaceSubstringIfNotDirty($day, $word);
        }

        return $this->result;
    }

    private function &getRegionInfoForCursor(int $cursor): array
    {
        $result = [ "isBadRegion" => true ];
        
        foreach ((array_keys($this->regionInfoMap)) as $key)
        {
            if ($cursor >= $key)
            {
                $result = &$this->regionInfoMap[$key];
                $result["index"] = $key;
            }
        }

        return $result;
    }

    private function replaceSubstringIfNotDirty(string $target, string $replacement): void
    {
        $lookupIdx = strpos($this->result, $target);

        if ($lookupIdx === false)
            return;

        $regionInfo = &$this->getRegionInfoForCursor($lookupIdx);

        if (@$regionInfo["isBadRegion"] == true)
            return;

        if (@$regionInfo["isDirty"] == true)
            return;

        $newIdx = $regionInfo["index"] ?? null;

        if (null === $newIdx)
            return;

        $targetIdx = strpos($this->result, $target, $newIdx);

        if ($targetIdx === false)
            return;

        $this->result = substr_replace($this->result, $replacement, $targetIdx, strlen($target));

        if ($targetIdx > $newIdx)
        {
            $this->pushNewRegion(location: $targetIdx, dirty: true);
        }
        else
        {
            // The current region is dirty.
            $this->markRegionDirty($regionInfo);
        }
        $this->pushNewRegion($targetIdx + strlen($replacement));
    }

    private function markRegionDirty(array &$region): void
    {
        $region["isDirty"] = true;
    }

    private function pushNewRegion(int $location, bool $dirty = false): void
    {
        $this->regionInfoMap[$location] = [
            "isDirty" => $dirty
        ];
    }
}