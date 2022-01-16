<?php

namespace Jugid\Staurie\Component\Money;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Money\CoreFunctions\MoneyFunction;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

class Money extends AbstractComponent {

    private string $amount;

    final public function name() : string {
        return 'money';
    }

    final public function getEventName() : array {
        return ['money.show', 'money.earn','money.lose'];
    }

    final public function require() : array {
        return [Console::class, PrettyPrinter::class];
    }
    
    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new MoneyFunction());
        $this->amount = $this->config['start_with'];
    }

    final public function defaultConfiguration() : array {
        return [
            'name'=>'gold',
            'start_with'=> 0
        ];
    }

    final protected function action(string $event, array $arguments) : void {
        switch($event) {
            case 'money.earn':
                break;
            case 'money.lose':
                break;
            default:
                $this->eventToAction($event);
                break;
        }
    }

    final protected function show() {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeTable(
            ['Amount'],
            [[$this->amount . ' ' . $this->config['name']]]
        );
    }
}