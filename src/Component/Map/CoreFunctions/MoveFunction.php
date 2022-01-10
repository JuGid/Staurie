<?php

namespace Jugid\Staurie\Component\Map\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class MoveFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('map.move', ['direction'=>$args[0]]);
    }

    public function name() : string {
        return 'move';
    }

    public function description() : string {
        return 'Move to a specific position';
    }

    public function getArgs() : int|array {
        return ['north','south','west','east'];
    }
}