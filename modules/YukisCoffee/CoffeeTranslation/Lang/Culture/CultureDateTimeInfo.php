<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Culture;

/**
 * Declares the data structure for culture date/time information.
 * 
 * The defaults specified in this file are for en-US.
 * 
 * @see https://www.php.net/manual/en/datetime.format.php
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class CultureDateTimeInfo
{
    /**
     * The number of months in a year as is standard for the language.
     */
    public int $numberOfMonths = 12;

    /**
     * The number of days in a week as is standard for the language.
     */
    public int $daysInWeek = 7;

    /**
     * The standard date order of the language, specified with the shorthand
     * letters "y", "m", and "d".
     * 
     * For example, American English uses "mdy", British English uses "dmy",
     * and Japanese uses "ymd".
     */
    public string $standardDateOrder = "mdy";

    /**
     * The translation for the 12-hour time suffix "AM" in the language.
     */
    public string $am = "AM";
    
    /**
     * The translation for the 12-hour time suffix "PM" in the language.
     */
    public string $pm = "PM";

    /**
     * The expanded date, followed by the time.
     * 
     * For example: Tuesday, October 17, 2023 12:50 AM.
     */
    public string $expandedDateWithTime = "l, F j, Y g:i A";

    /**
     * The expanded date by itself.
     * 
     * For example: Tuesday, October 17, 2023.
     */
    public string $expandedDate = "l, F j, Y";

    /**
     * The full date, followed by the time.
     * 
     * For example: October 17, 2023 12:50 AM.
     */
    public string $dateWithTime = "F j, Y g:i A";

    /**
     * The full date by itself.
     * 
     * For example, October 17, 2023.
     */
    public string $date = "F j, Y";

    /**
     * The short date, followed by the time.
     * 
     * For example: 10/17/2023 12:50 AM.
     */
    public string $shortDateWithTime = "m/d/Y g:i A";

    /**
     * The short date by itself.
     * 
     * For example: 10/17/2023.
     */
    public string $shortDate = "m/d/Y";

    /**
     * The time by itself.
     * 
     * For example: 12:50 AM.
     */
    public string $time = "g:i A";

    /**
     * The index of the first day of week as is common in the language.
     * 
     * For example, if daysOfWeek[1] is Sunday, then this being 1 will set the
     * first day of week to Sunday.
     */
    public int $firstDayOfWeek = 1;

    /**
     * The names of each day of week.
     * 
     * For example: Sunday, Monday, Tuesday, etc.
     */
    public array $daysOfWeek = [
        1 => "Sunday",
        2 => "Monday",
        3 => "Tuesday",
        4 => "Wednesday",
        5 => "Thursday",
        6 => "Friday",
        7 => "Saturday"
    ];

    /**
     * The shorthand for the names of each day of week, if applicable.
     * 
     * For example: Sun, Mon, Tue, etc.
     */
    public ?array $shortDaysOfWeek = null;

    /**
     * The names of each month.
     * 
     * For example: January, February, March, etc.
     */
    public array $monthNames = [
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

    /**
     * The shorthand for the names of each month, if applicable.
     * 
     * For example: Jan, Feb, Mar, etc.
     */
    public ?array $shortMonthNames = null;
}