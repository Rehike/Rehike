<?php
namespace Rehike\ControllerV2;

use Rehike\YtApp;

/**
 * Interface for a class implementing CV3-compatible POST controller.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IPostController
{
    /**
     * Handles requests made using the POST method.
     */
    public function post(): void;
}