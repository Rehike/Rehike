<?php
namespace Rehike\Page;

class AbstractPage
{
    public $page;

    public function __construct()
    {
        $this->page = (object)[];
    }

    public function __call($function, $arguments)
    {
        // Hack to allow chain calling static methods
        // of Yt (supposed to be parent)
        \Rehike\Yt::{$function}(...$arguments);
        return $this;
    }

    public static function __callStatic($a,$b)
    {
        throw new \Rehike\Exception\RehikePageException("Page may not be called statically.");
    }

    protected function _registerRewriter($rewriter){}

    public function _buildPage(){}

    public function _postRenderCallback(){}
}