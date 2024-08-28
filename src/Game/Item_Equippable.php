<?php

namespace Jugid\Staurie\Game;

abstract class Item_Equippable extends Item {

    abstract public function body_part() : string;
    
}