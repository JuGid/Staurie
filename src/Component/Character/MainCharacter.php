<?php

namespace Jugid\Staurie\Component\Character;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\CoreFunctions\MainCharacterFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\SpeakFunction;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

abstract class MainCharacter extends AbstractComponent {

    private array $statistics = [];

    final public function name() : string {
        return 'character';
    }

    final public function getEventName() : array {
        $events = ['character.me'];

        if($this->container->isComponentRegistered(Map::class)) {
            array_push($events, 'character.speak', 'character.take', 'character.use', 'character.drop');
        }

        return $events;
    }

    final public function require() : array {
        return [PrettyPrinter::class];
    }

    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new MainCharacterFunction());

        if($this->container->isComponentRegistered(Map::class)) {
            $console->addFunction(new SpeakFunction());
        }

        $this->statistics = $this->config['statistics'];
    }

    final protected function action(string $event, array $arguments) : void {
        $pp = $this->container->getPrettyPrinter();
        switch($event) {
            case 'character.me':
                $pp->writeLn('Name : ' . $this->config['name']);
                $header = ['Attribute', 'Value'];
                $line = [];
                foreach($this->statistics as $name=>$value) {
                    $line[] = [ucfirst($name), $value];
                }
                $pp->writeTable($header, $line);
                break;
            case 'character.speak':
                $pp->writeLn('Speak to ' . $arguments['to']);
                break;
            case 'character.take':
                break;
            case 'character.use':
                break;
            case 'character.drop':
                break;
        }
    }

    final public function defaultConfig() : array {
        return [
            'name'=>'Unknown',
            'statistics'=>[
                'chance'=>0,
                'ability'=>0,
                'wisdom'=>0
            ]
        ];
    }
}