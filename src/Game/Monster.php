<?php

namespace Jugid\Staurie\Game;

use Jugid\Staurie\Container;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Fightable;

abstract class Monster implements Containerable, Describable, Fightable {

    private Container $container;

    final public function setContainer(Container $container) : void {
        $this->container = $container;
    }

}