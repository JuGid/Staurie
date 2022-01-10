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

    public function speak() : void {
        if($this->playerHasItem('Sword')) {
            echo 'Please, do not hurt me !', "\n";
        } else {
            echo 'Speak me i\'m famous', "\n";
        }
        
    }
}