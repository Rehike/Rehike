<?php
namespace Rehike\ControllerV2;

/**
 * Interface for an object implementing a CV3 controller.
 * 
 * You should probably inherit from BaseController instead.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IController
{
    public function initializeController(RequestMetadata $request): void;
}