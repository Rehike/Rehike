<?php
namespace Rehike;

use YukisCoffee\CoffeeRequest\Enum\NetworkResult;

/**
 * Controller for the no internet page, which displays when the server is
 * unable to connect to the internet.
 *
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class NoInternetPage
{
    public static function render(): void
    {
        while (ob_get_level() > 0)
        {
            ob_end_clean();
        }

        include "includes/fatal_templates/no_internet.html.php";
    }

    public static function isNoInternetResult(int $networkResult): bool
    {
        switch ($networkResult)
        {
            case NetworkResult::E_COULDNT_RESOLVE_HOST:
            case NetworkResult::E_COULDNT_RESOLVE_PROXY:
            case NetworkResult::E_COULDNT_CONNECT:
                return true;
        }

        return false;
    }
}