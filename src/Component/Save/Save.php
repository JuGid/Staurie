<?php

namespace Jugid\Staurie\Component\Save;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Component\Save\CoreFunctions\SaveFunction;

class Save extends AbstractComponent {

    public function name() : string {
        return 'save';
    }

    public function getEventName() : array {
        return ['save.save'];
    }

    public function require() : array {
        return [Console::class, PrettyPrinter::class];
    }
    
    public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new SaveFunction());
    }

    public function defaultConfig() : array {
        return [];
    }

    protected function action(string $event, array $arguments) : void {
        echo "Saving\n";
    }
}