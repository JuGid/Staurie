<?php

namespace Jugid\Staurie\Example\Npcs;

use Jugid\Staurie\Example\Items\Shield;
use Jugid\Staurie\Game\Npc;

class Cain extends Npc {

    public function name() : string {
        return 'Cain';
    }

    public function description() : string {
        return 'A stranger with a pretty face';
    }

    public function speak() : string|array {
        if($this->playerHasItem('Sword')) {
            return ['Please, do not hurt me !', 'I\'m famous here, I can help you !'];
        } else {
            $this->giveItem(new Shield());
            return 'As you have nothing to hit, I gave you this shield to protect yourself.';
        }
        
    }
}