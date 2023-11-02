<?php
namespace Rehike;

/**
 * Defines various properties regarding the page view. These are configurable
 * states shared between HTML and SPF views.
 * 
 * These were originally variables declared in the Twig templates themselves,
 * but we needed to restructure the system when rewriting SPF view support, so
 * this is now distinct (and thus readily accessible in PHP code).
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class ViewProperties
{
    /**
     * HTML element class denoting the page type name.
     * 
     * This is used on the #page element.
     */
    public string $pageClassName = "";

    /**
     * The JS page name, if applicable. If null, then implementations should
     * fall back to pageClassName.
     * 
     * This is used for page JS configuration, but can usually be the same as
     * the page type class. Sometimes it differs (i.e. for index).
     */
    public ?string $jsPageName = null;

    /**
     * Enables or disables appbar functionality entirely.
     * 
     * If the appbar is disabled, then pages will be similar to 2013 Hitchhiker
     * (see core_legacy.twig).
     */
    public bool $appbarEnabled = false;

    /**
     * Configures the default visibility of the guide.
     */
    public bool $guideDefaultVisibility = false;

    /**
     * Configures the default appbar (secondary header) visibility.
     */
    public bool $appbarDefaultVisibility = false;

    /**
     * Configures CSS/JS responsive UI snap scaling.
     */
    public bool $enableSnapScaling = false;

    /**
     * If true, disables centred page content.
     */
    public bool $leftAlignPage = false;

    /**
     * Disables flex-width snapping behaviour.
     */
    public bool $flexWidthSnapDisabled = false;

    /**
     * Other page classes to use, which will be specified after $pageClassName.
     */
    public string $pageClasses = "";
}