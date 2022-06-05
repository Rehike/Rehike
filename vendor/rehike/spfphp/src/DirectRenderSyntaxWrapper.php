<?php
namespace SpfPhp;

/**
 * Wrap a DirectRender getCallback response.
 * 
 * This is to prevent a doubling of the call token ()
 * like in:
 * 
 *     DirectRender::getCallback("my_function")($xtags)
 * 
 * which now becomes:
 * 
 *     DirectRender::getCallback("my_function")->call($xtags)
 * 
 * In other words, this only exists for code clarity and readability.
 */
class DirectRenderSyntaxWrapper
{
    /** @var Callable */
    private $boundCallback;

    /**
     * Call the bound callback.
     * 
     * @param string[] $xtags to be provided to the callback.
     * @return mixed|string
     */
    public function call($xtags)
    {
        $cb = &$this->boundCallback;

        if (is_callable($cb))
        {
            return $cb($xtags);
        }
        else
        {
            return "";
        }
    }

    /**
     * Alias of call() method, to be used when accessing as a function
     * directly (i.e. as you might do with a reference to the class rather than
     * in a chain)
     * 
     * @see call
     */
    public function __invoke($xtags)
    {
        return $this->call($xtags);
    }

    public function __construct($binding)
    {
        $this->boundCallback = $binding;
    }
}