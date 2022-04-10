<?php

namespace Rehike;

abstract class AbstractModule {
    public function getApplication() {
        return '\Rehike\Application';
    }

    public function install() {
        $a = new static();
        
        // technically fires right before installation
        if (method_exists($a, 'onInstall')) {
            $a->onInstall();
        }
        
        return $a;
    }
}