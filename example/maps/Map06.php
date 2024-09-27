<?php

namespace Jugid\Staurie\Example\Maps;

use Jugid\Staurie\Component\Map\Blueprint;
use Jugid\Staurie\Example\Items\Shield;
use Jugid\Staurie\Example\Monsters\Bouftou;
use Jugid\Staurie\Game\Position\Position;

class Map06 extends Blueprint {

    private Position $position;

    public function __construct()
    {
        $this->position = new Position(2,3);
    }

    public function name() : string {
        return 'Map 7';
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
        return [new Shield()];
    }

    public function monsters() : array {
        return [new Bouftou()];
    }
}