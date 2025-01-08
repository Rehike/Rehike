<?php
namespace Rehike\ControllerV2;

/**
 * Base class for CV3 controllers.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class BaseController implements IController
{
    /**
     * Stores information about the current request.
     */
    private RequestMetadata $request;
    
    public function initializeController(RequestMetadata $request): void
    {
        $this->request = $request;
    }
    
    protected function getRequest(): RequestMetadata
    {
        return $this->request;
    }
}