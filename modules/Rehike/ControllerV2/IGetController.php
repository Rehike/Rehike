<?php
namespace Rehike\ControllerV2;

use Rehike\YtApp;

/**
 * Interface for a class implementing CV3-compatible GET controller.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IGetController
{
    /**
     * Handles requests made using the GET method.
     */
    public function get(): void;
}