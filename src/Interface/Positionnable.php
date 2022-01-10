<?php

namespace Jugid\Staurie\Interface;

use Jugid\Staurie\Game\Position;

interface Positionnable {
    public function position() : Position;
}