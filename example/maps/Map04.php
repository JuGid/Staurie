<?php

namespace Jugid\Staurie\Example\Maps;

use Jugid\Staurie\Component\Map\Blueprint;
use Jugid\Staurie\Game\Position\Position;

class Map04 extends Blueprint {

    private Position $position;

    public function __construct()
    {
        $this->position = new Position(1,0);
    }

    public function name() : string {
        return 'Timap';
    }

    public function description() : string {
        return 'This is la Map de Timothée';
    }

    public function position() : Position {
        return $this->position;
    }

    public function npcs() : array {
        return [];
    }

    public function items() : array {
        return [];
    }
}