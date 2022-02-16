<?php

namespace Jugid\Staurie\Interface;

use Jugid\Staurie\Game\Position\Position;

interface Positionnable {
    public function position() : Position;
}