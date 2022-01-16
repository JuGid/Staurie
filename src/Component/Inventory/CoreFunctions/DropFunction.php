<?php

namespace Jugid\Staurie\Component\Inventory\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class DropFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('inventory.drop', ['item_name'=>$args[0]]);   
    }

    public function name() : string {
        return 'drop';
    }

    public function description() : string {
        return 'Drop an item from your inventory';
    }

    public function getArgs() : int|array {
        return 1;
    }
}