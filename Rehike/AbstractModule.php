<?php

namespace Rehike;

abstract class AbstractModule {
    public function getApplication() {
        return '\Rehike\Application';
    }
}