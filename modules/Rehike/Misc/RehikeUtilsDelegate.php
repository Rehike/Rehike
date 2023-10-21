<?php
namespace Rehike\Misc;

use Rehike\Util\{
    Base64,
    CasingUtils,
    ResourceUtils,
    ParsingUtils
};

/**
 * Defines the `rehike` variable exposed to Twig-land.
 * 
 * This class implements all alises to other utility classes. The parent
 * class handles unique properties and all methods.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeUtilsDelegate extends RehikeUtilsDelegateBase
{
    public Base64 $base64;
    public CasingUtils $casing;
    public ResourceUtils $resource;
    public ParsingUtils $parsing;
    public RehikeUtilsI18nDelegate $i18n;

    public function __construct()
    {
        parent::__construct();

        // When abandoning support for PHP 8.0, these may be coalesced into
        // the constructor arguments.
        $this->casing = new CasingUtils();
        $this->base64 = new Base64();
        $this->resource = new ResourceUtils();
        $this->parsing = new ParsingUtils();
        $this->i18n = new RehikeUtilsI18nDelegate();
    }
}