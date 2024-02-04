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
        $translator = new DateTimeStringTranslator($this->cultureInfo, $in);
        return $translator->getTranslatedString();
    }
}