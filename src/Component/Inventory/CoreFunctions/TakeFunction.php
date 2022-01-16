<?php

namespace Jugid\Staurie\Component\Inventory\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class TakeFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('inventory.take', ['item_name'=>$args[0]]); 
    }

    public function name() : string {
        return 'take';
    }

    public function description() : string {
        return 'Take an item from the map';
    }

    public function getArgs() : int|array {
        return 1;
    }
}