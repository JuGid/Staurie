<?php

namespace Jugid\Staurie\Example\Maps;

use Jugid\Staurie\Component\Map\Blueprint;
use Jugid\Staurie\Game\Position\Position;

class Map03 extends Blueprint {

    private Position $position;

    public function __construct()
    {
        $this->position = new Position(1,0);
    }

    public function name() : string {
        return 'Flatland';
    }

    public function description() : string {
        return 'Oh ! This is so flat !';
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

    public function monsters() : array {
        return [];
    }
}