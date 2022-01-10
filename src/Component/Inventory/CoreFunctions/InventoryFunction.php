<?php

namespace Jugid\Staurie\Component\Inventory\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;
use Jugid\Staurie\Component\Map\Map;

class InventoryFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        switch($args[0]) {
            case 'view':
                $this->getContainer()->dispatcher()->dispatch('inventory.view');
                break;
            case 'size':
                $this->getContainer()->dispatcher()->dispatch('inventory.size');
                break;
            case 'take':
                $this->getContainer()->dispatcher()->dispatch('inventory.take', ['item_name'=>$args[1]]);
                break;
            case 'drop':
                $this->getContainer()->dispatcher()->dispatch('inventory.drop', ['item_name'=>$args[1]]);
                break;
        }        
    }

    public function name() : string {
        return 'inventory';
    }

    public function description() : string {
        return 'Inventory function';
    }

    public function getArgs() : int|array {
        $args = ['view','size'];

        if($this->getContainer()->isComponentRegistered(Map::class)) {
            array_push($args, 'take', 'drop');
        }

        return $args;
    }
}