<?php
namespace Rehike\ControllerV2;

use Rehike\Async\Promise;

/**
 * Interface for a class implementing an asynchronous GET controller.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IGetControllerAsync
{
    /**
     * Handles requests made using the GET method.
     */
    public function getAsync(): Promise;
}