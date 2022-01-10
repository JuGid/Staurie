<?php

namespace Jugid\Staurie\Example\Npcs;

use Jugid\Staurie\Game\Npc;

class Cain extends Npc {

    public function name() : string {
        return 'Cain';
    }

    public function description() : string {
        return 'A stranger with a pretty face';
    }

    public function speak() : string {
        if($this->playerHasItem('Sword')) {
            return 'Please, do not hurt me !';
        } else {
            return 'Speak me i\'m famous';
        }
        
    }
}