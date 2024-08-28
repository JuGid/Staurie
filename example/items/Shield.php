<?php

namespace Jugid\Staurie\Example\Items;

use Jugid\Staurie\Game\Item_Equippable;

class Shield extends Item_Equippable {

    public function name() : string {
        return 'Shield';
    }

    public function description(): string
    {
        return 'A simple shield';
    }

    public function body_part(): string { 
        return 'shield';
    }

    public function statistics(): array
    {
        return [
            'defense'=> 5
        ];
    }
}