<?php

namespace Jugid\Staurie\Component\Save;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Component\Save\CoreFunctions\SaveFunction;

class Save extends AbstractComponent {

    final public function name() : string {
        return 'save';
    }

    final public function getEventName() : array {
        return [];
    }

    final public function require() : array {
        return [Console::class, PrettyPrinter::class];
    }
    
    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new SaveFunction());
    }

    final public function defaultConfiguration() : array {
        return [
            'directory'=>__DIR__.'/../../../../saves/'
        ];
    }

    final protected function action(string $event, array $arguments) : void {
        
    }
}