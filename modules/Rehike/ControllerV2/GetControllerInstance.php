<?php
namespace Rehike\ControllerV2;

/**
 * Implements a wrapper for working with imported controllers.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GetControllerInstance
{
    private string $controllerName;
    private object $boundController;
    
    /** 
     * Reference to shorten code. 
     * 
     * @var string 
     */
    const wrap = "_cv2WrappedCallControllerMethod";

    public function __construct(string $name, object $binding)
    {
        $this->controllerName = &$name;
        $this->boundController = &$binding;
    }

    /**
     * Wrap a method call.
     * 
     * This allows the calling of static methods through this,
     * and also throws an exception if the method does not exist.
     * 
     * @param string $name
     * @param mixed[] $args
     */
    protected function _cv2WrappedCallControllerMethod(string $name, array $args): mixed
    {
        // Check if the method exists in the bound controller
        if (method_exists($this->boundController, $name))
        {
            // Check if the method is static or instantiated
            if ( (new \ReflectionMethod($this->boundController, $name))->isStatic() )
            {
                return $this->boundController::$name(
                    Core::$state, Core::$template, new RequestMetadata(), ...$args
                );
            }
            else
            {
                return $this->boundController->{$name}(
                    Core::$state, Core::$template, new RequestMetadata(), ...$args
                );
            }
        }
        else
        {
            throw new Exception\MethodDoesNotExistException(
                "Called method \"{$name}\" does not exist on bound controller " .
                "\"{$this->controllerName}\"."
            );
        }
    }

    /**
     * Redirect any call to this object to the wrapper.
     * 
     * @param string $name
     * @param mixed[] $args
     * @return mixed
     */
    public function __call(string $name, array $args): mixed
    {
        return $this->{self::wrap}($name, $args);
    }
}