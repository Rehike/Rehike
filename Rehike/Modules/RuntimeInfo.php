<?php

namespace Rehike\Modules;

class RuntimeInfo extends \Rehike\AbstractModule {
    public $runtimeType;
    public $serverAgent;
    public $phpHost;
    public $phpVersion;

    /**
     * @return void
     */
    public function __construct() {
        $this->serverAgent = $this->getServerAgent();
        $this->phpVersion = phpversion();
    }

    /**
     * Essentially just for checking if it's running on a proper
     * web server.
     * 
     * @return string
     */
    public function getRuntimeType() {
        if ($this->getServerAgent() != 'null') {
            return 'web';
        }
        return 'unknown';
    }

    /**
     * Return the user agent of the server software.
     * 
     * @return string
     */
    public function getServerAgent() {
        return @$_SERVER['SERVER_SOFTWARE'] ?? 'null';
    }
}