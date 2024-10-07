<?php

namespace Jugid\Staurie\Component\Race;

use Jugid\Staurie\Component\Character\Statistics;

class Human extends AbstractRace {

    public function statistics(): Statistics { 
        return (new Statistics())
            ->addDefault('Healt', 100)
            ->addDefault('Force', 5)
            ->addDefault('Defense', 5)
        ;
    }

    public function name(): string { 
        return 'Human';
    }

    public function description(): string { 
        return 'Simple race that grow on Earth';
    }

}