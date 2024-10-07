<?php

namespace Jugid\Staurie\Component\Race;

use Jugid\Staurie\Component\Character\Statistics;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Nameable;

abstract class AbstractRace implements Nameable, Describable{
    abstract public function statistics() : Statistics;
}