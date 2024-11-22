<?php
namespace Rehike\Spf;

/**
 * Provides page-specific configuration data for SPF responses.
 * 
 * This is exposed via yt.spfConfig.
 * 
 * @author The Rehike Maintainers
 */
class SpfConfig
{
    /**
     * The URL to be displayed for the page being navigated to.
     * 
     * If unspecified, then SPF will default to the canonical path, dropping
     * the SPF URL parameter. This will be the case for most YouTube pages.
     */
    public ?string $url = null;
    
    /**
     * Custom data to be included in the response.
     */
    public ?object $data = null;

    /**
     * Exports Rebug data for the templater.
     * 
     * This is pre-serialized as a Twig hack.
     */
    public ?string $rebugData = null;
}