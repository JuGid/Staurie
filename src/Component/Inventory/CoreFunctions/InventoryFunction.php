<?php

namespace Jugid\Staurie\Component\Inventory\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class InventoryFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        if(!isset($args[0])) {
            return;
        }
        
        switch($args[0]) {
            case 'view':
                $this->getContainer()->dispatcher()->dispatch('inventory.view');
                break;
            case 'size':
                $this->getContainer()->dispatcher()->dispatch('inventory.size');
                break;
        }        
    }

    public function name() : string {
        return 'inventory';
    }

    public function description() : string {
        return 'Inventory functions';
    }

    public function getArgs() : int|array {
        return ['view','size'];
    }
}