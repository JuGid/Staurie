<?php

namespace Jugid\Staurie\Component\Character\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class SpeakFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('character.speak', ['to'=>$args[0]]);
    }

    public function name() : string {
        return 'speak';
    }

    public function description() : string {
        return 'Speak to a npc';
    }

    public function getArgs() : int|array {
        return 1;
    }
}