<?php

namespace Jugid\Staurie\Component\Money\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class MoneyFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $this->getContainer()->dispatcher()->dispatch('money.show');
    }

    public function name() : string {
        return 'money';
    }

    public function description() : string {
        return 'Show the amount of money you have';
    }

    public function getArgs() : int|array {
        return 0;
    }
}