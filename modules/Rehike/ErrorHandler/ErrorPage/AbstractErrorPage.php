<?php
namespace Rehike\ErrorHandler\ErrorPage;

/**
 * Represents an abstract error page model.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractErrorPage
{
    /**
     * Get the title of the error page type.
     */
    abstract public function getTitle(): string;
}