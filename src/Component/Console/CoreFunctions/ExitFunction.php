<?php

namespace Jugid\Staurie\Component\Console\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class ExitFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        if($this->ask('Do you want to save the game ?', ['Y','N']) === 'Y' ) {
            $this->getContainer()->dispatcher()->dispatch('save.save');
        }

        $this->getContainer()->state()->stop();
    }

    public function name() : string {
        return 'exit';
    }

    public function description() : string {
        return 'Exit the game';
    }

    public function getArgs() : int|array {
        return 0;
    }
}