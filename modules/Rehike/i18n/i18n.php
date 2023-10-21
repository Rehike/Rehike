<?php
namespace Rehike\i18n;

use YukisCoffee\CoffeeTranslation\CoffeeTranslation;

// Hack for IDE hover behavior.
if(false){class i18n extends CoffeeTranslation {}}

class_alias(CoffeeTranslation::class, "Rehike\\i18n\\i18n");