<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Culture;

use YukisCoffee\CoffeeTranslation\{
    DateTimeFormats
};

use function PHPSTORM_META\map;

/**
 * Declares translatable date/time APIs.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class DateTimeApi
{
    private CultureInfo $cultureInfo;

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

    public function __construct(CultureInfo $lang)
    {
        $this->cultureInfo = $lang;
    }

    /**
     * Format a date/time string.
     * 
     * @param DateTimeFormats $format
     */
    public function formatDateTime(
            int $format = DateTimeFormats::DATE,
            int $timestamp
    ): string
    {
        $dt = $this->cultureInfo->dateTimeInfo;

        $format = match($format) {
            DateTimeFormats::DATE => $dt->date,
            DateTimeFormats::DATE_WITH_TIME => $dt->dateWithTime,
            DateTimeFormats::SHORT_DATE => $dt->shortDate,
            DateTimeFormats::SHORT_DATE_WITH_TIME => $dt->shortDateWithTime,
            DateTimeFormats::EXPANDED_DATE => $dt->expandedDate,
            DateTimeFormats::EXPANDED_DATE_WITH_TIME => $dt->expandedDateWithTime,
            DateTimeFormats::TIME => $dt->time
        };

        return self::translateString(
            date(
                $this->cultureInfo->dateTimeInfo->expandedDateWithTime,
                $timestamp
            )
        );
    }

    private function translateString(string $in): string
    {
        $dt = $this->cultureInfo->dateTimeInfo;

        /**
         * TODO (kirasicecreamm): Prevent work from being overdone :P
         * 
         * I didn't think this through, since a conflict would be unlikely, but
         * in the case of English specifically, there can be a few oddities.
         * 
         * "Thursday" -> "Thurrsday"
         * 
         * This is because the full weekdays are replaced with versions from the
         * culture file: "Thursday" -> "Thursday". Effectively no change is made.
         * 
         * Then, because this is a naive system, it overrides this, and replaces
         * the short week days. In the error case that brought this to my
         * attention:
         * 
         * "Thu" -> "Thur"
         * 
         * This results in the final string being displayed incorrectly as
         * "Thurrsday".
         * 
         * This can be easily fixed with a more complicated replacement system
         * whereby a region of text can be marked as "dirty", and ignored by
         * subsequent modifications, ensuring that the replacement of substrings
         * doesn't affect those that we already modified.
         */
        foreach (self::ENGLISH_WEEKDAYS as $i => $day)
        {
            $translation = $dt->daysOfWeek[$i];
            $in = @str_replace($day, $translation, $in);
        }

        foreach (self::ENGLISH_WEEKDAYS_SHORT as $i => $day)
        {
            $translation = $dt->shortDaysOfWeek[$i];
            $in = @str_replace($day, $translation, $in);
        }

        foreach (self::ENGLISH_MONTHS as $i => $month)
        {
            $translation = $dt->monthNames[$i];
            $in = @str_replace($month, $translation, $in);
        }

        foreach (self::ENGLISH_MONTHS_SHORT as $i => $month)
        {
            $translation = $dt->shortMonthNames[$i];
            $in = @str_replace($month, $translation, $in);
        }

        foreach (self::ENGLISH_AM_PM as $i => $word)
        {
            $translation = match(strtolower($word)) {
                "am" => $dt->am,
                "pm" => $dt->pm
            };
            $in = @str_replace($word, $translation, $in);
        }

        return $in;
    }
}