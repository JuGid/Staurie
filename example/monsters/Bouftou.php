<?php

namespace Jugid\Staurie\Example\Monsters;

use Jugid\Staurie\Game\Monster;

class Bouftou extends Monster {

    public function name() : string {
        return 'Bouftou';
    }

    public function description(): string { 
        return 'THIS, IS, THE, ORIGINAL, GOAT';
    }

    public function level() : int {
        return 1;
    }

    public function health_points(): int { 
        return 30;
    }

    public function defense(): int { 
        return 2;
    }

    public function experience(): int { 
        return 12;
    }

    public function skills(): array { 
        return [
            'Charge' => 20,
        ];
    }
}