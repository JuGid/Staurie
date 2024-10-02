<?php

namespace Jugid\Staurie\Game;

use Jugid\Staurie\Container;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Fightable;
use Jugid\Staurie\Interface\Nameable;

abstract class Monster implements Containerable, Describable {

    protected Container $container;

    final public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    abstract function level() : int;
    abstract function health_points() : int;
    abstract function defense() : int;
    abstract function attack() : int;
    abstract function experience() : int;
    abstract function skills() : array;
}