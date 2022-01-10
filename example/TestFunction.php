<?php

namespace Jugid\Staurie\Example;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class TestFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        echo "Testing the function\n";
    }

    public function name() : string {
        return 'test';
    }

    public function description() : string {
        return 'User function to test console component';
    }

    public function getArgs() : int|array {
        return 0;
    }
}