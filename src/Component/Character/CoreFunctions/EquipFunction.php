<?php

namespace Jugid\Staurie\Component\Character\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class EquipFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('character.equip', [
            'item'=>$args[0], 
            'body_part'=>$args[1]
        ]);
    }

    public function name() : string {
        return 'equip';
    }

    public function description() : string {
        return 'Equip an item from the inventory onto the body of your character';
    }

    public function getArgs() : int|array {
        return 2;
    }
}