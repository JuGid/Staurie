<?php

namespace Jugid\Staurie\Component\Console\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class HelpFunction extends AbstractConsoleFunction {

    public function action(array $args) : void {
        $console = $this->getContainer()->getConsole();
        $functions = $console->getFunctionsDefinition();
        $prettyprinter = $this->getContainer()->getPrettyPrinter();

        usort($functions, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });

        if(null !== $prettyprinter) {
            $prettyprinter->writeTable(['Function', 'Description', 'Options'], $functions);
        } else {
            foreach($functions as $fn) {
                echo implode(" - ", $fn) . "\n";
            }
        }
    }

    public function name() : string {
        return 'help';
    }

    public function description() : string {
        return 'Print help of console';
    }

    public function getArgs() : int|array {
        return 0;
    }
}