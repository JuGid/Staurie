<?php

namespace Jugid\Staurie\Component\Save\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class SaveFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('staurie.save');
        $pp = $this->getContainer()->getPrettyPrinter();
        $pp->writeLn('Save completed !', 'green');
    }

    public function name() : string {
        return 'save';
    }

    public function description() : string {
        return 'Save the game state';
    }

    public function getArgs() : int|array {
        return 0;
    }
}