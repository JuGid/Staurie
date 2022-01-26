<?php

namespace Jugid\Staurie\Component\Race;

use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Nameable;

abstract class AbstractRace implements Nameable, Describable{
    abstract public function statistics() : array;
}