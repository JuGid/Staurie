<?php

namespace Jugid\Staurie\Example\Maps;

use Jugid\Staurie\Component\Map\Blueprint;
use Jugid\Staurie\Game\Position\Position;

class Map02 extends Blueprint {

    private Position $position;

    public function __construct()
    {
        $this->position = new Position(0,1);
    }

    public function name() : string {
        return 'Mountain';
    }

    public function description() : string {
        return 'You\'re in front of a mountain. A big mountain. Good luck.';
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