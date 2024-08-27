<?php

namespace Jugid\Staurie\Example\Items;

use Jugid\Staurie\Game\Item_Equippable;

class Sword extends Item_Equippable {

    public function name() : string {
        return 'Sword';
    }

    public function description(): string
    {
        return 'A simple sword';
    }

    public function body_part(): string { 
        return 'hand';
    }

    public function statistics(): array
    {
        return [
            'chance'=> 3,
            'wisdom'=> 1
        ];
    }
}