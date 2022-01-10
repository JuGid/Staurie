<?php

namespace Jugid\Staurie\Game;

use Jugid\Staurie\Interface\Configurable;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Initializable;
use Jugid\Staurie\Interface\Nameable;

abstract class Item implements Nameable, Describable, Initializable {

    public function initialize(): void
    {
        
    }

    abstract public function statistics() : array;
}