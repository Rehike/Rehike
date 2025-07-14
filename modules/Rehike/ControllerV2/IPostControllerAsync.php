<?php
namespace Rehike\ControllerV2;

use Rehike\Async\Promise;

/**
 * Interface for a class implementing an asynchronous POST controller.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IPostControllerAsync
{
    /**
     * Handles requests made using the POST method.
     */
    public function postAsync(): Promise;
}