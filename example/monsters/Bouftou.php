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
        return 20;
    }

    public function defense(): int { 
        return 2;
    }

    public function attack() : int {
        return 5;
    }

    public function experience(): int { 
        return 11;
    }

    public function skills(): array { 
        return [
            'Charge' => 20,
        ];
    }
}