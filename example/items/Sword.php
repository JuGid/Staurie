<?php

namespace Jugid\Staurie\Example\Items;

use Jugid\Staurie\Game\Item;

class Sword extends Item {

    public function name() : string {
        return 'Sword';
    }

    public function description(): string
    {
        return 'A simple sword';
    }

    public function statistics(): array
    {
        return [
            'chance'=> 3,
            'wisdom'=> 1
        ];
    }
}