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
            array_push($events, 'character.speak');
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
        switch($event) {
            case 'character.me':
                $this->me();
                break;
            case 'character.speak':
                $this->speak($arguments['to']);
                break;
        }
    }

    private function me() {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeUnder('Details', 'green');
        $pp->writeLn('Name : ' . $this->config['name']);

        $this->printLevel();

        $pp->writeUnder("\nYour statistics", 'green');
        $header = ['Attribute', 'Value'];
        $line = [];
        foreach($this->statistics as $name=>$value) {
            $line[] = [ucfirst($name), $value];
        }
        $pp->writeTable($header, $line);
    }

    private function printLevel() : void {
        $this->container->dispatcher()->dispatch('level.ask');
    }

    private function speak(string $npc_name) {
        $pp = $this->container->getPrettyPrinter();
        $npc = $this->container->getMap()->getCurrentBlueprint()->getNpc($npc_name);

        if(null !== $npc) {
            $dialog = $npc->speak();
            if(is_array($dialog)) {
                foreach($dialog as $dial) {
                    $this->printNpcDialog($npc_name, $dial);
                }
                return;
            } 
            
            $this->printNpcDialog($npc_name, $dialog);
        } else {
            $pp->writeLn('You are probably talking to a ghost', 'red');
        }
    }

    private function printNpcDialog(string $npc_name, string $dial) {
        $pp = $this->container->getPrettyPrinter();
        $pp->write($npc_name . ' : ', 'green');
        $pp->writeScroll($dial, 20);
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