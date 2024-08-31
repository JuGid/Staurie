<?php

namespace Jugid\Staurie\Component\Character\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class StatsFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('character.stats', ['type'=> $args[0], 'stat'=> $args[1]]);
    }

    public function name() : string {
        return 'stats';
    }

    public function description() : string {
        return 'Manage your stats';
    }

    public function getArgs() : int|array {
        return 2;
    }
}