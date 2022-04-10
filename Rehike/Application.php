<?php

namespace Rehike;

/**
 * Class implementing the base Rehike application
 * 
 * @static
 */
final class Application extends Base {
    /**
     * Production mode accessor
     * 
     * This property should be considered READ-ONLY
     * (only use get)
     * 
     * @var bool
     */
    protected static $isProd;

    /**
     * Module definitions
     */
    protected static $runtimeInfo;
    protected static $logger;

    /**
     * Insertion point
     */
    public static function main() {
        // include required global constants
        require 'Rehike/constants.php';
        self::setIsProd(REHIKE_PROD); // easy access from application and modules
        
        // include composer packages
        require 'vendor/autoload.php';

        self::initModules();
    }

    /**
     * Module initialiser
     */
    protected static function initModules() {
        self::setRuntimeInfo(new Modules\RuntimeInfo());
        self::setLogger(new Modules\Logger());
    }
}