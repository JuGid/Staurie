<?php

namespace Jugid\Staurie\Component\Character;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\CoreFunctions\MainCharacterFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\SpeakFunction;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Game\Npc;

class MainCharacter extends AbstractComponent {

    private Statistics $statistics;

    private string $name;

    private string $gender;

    final public function name() : string {
        return 'character';
    }

    final public function getEventName() : array {
        $events = ['character.me', 'character.new'];

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
        $this->name = $this->config['name'];
        $this->gender = $this->config['gender'];
    }

    final protected function action(string $event, array $arguments) : void {
        switch($event) {
            case 'character.speak':
                $this->speak($arguments['to']);
                break;
            default:
                $this->eventToAction($event);
                break;
        }
    }

    final protected function new() {
        $this->name = readline('Character name : ');
        $this->gender = readline('Character gender : ');

        $this->container->dispatcher()->dispatch('race.ask');
        $this->container->dispatcher()->dispatch('tribe.ask');

        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn('Welcome ' . $this->name, 'green');
    }

    final protected function me() {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeUnder('Details', 'green');
        $pp->writeLn('Name : ' . $this->name);
        $pp->writeLn('Gender : ' . $this->gender);

        $this->container->dispatcher()->dispatch('race.view');
        $this->container->dispatcher()->dispatch('tribe.view');
        $this->container->dispatcher()->dispatch('level.view');

        $pp->writeUnder("\nYour statistics", 'green');
        $header = ['Attribute', 'Value'];
        $line = [];

        foreach($this->statistics->asArray() as $name=>$value) {
            $line[] = [ucfirst($name), $value];
        }
        $pp->writeTable($header, $line);
    }

    private function speak(string $npc_name) {
        $pp = $this->container->getPrettyPrinter();
        $npc = $this->container->getMap()->getCurrentBlueprint()->getNpc($npc_name);

        if(null !== $npc && $npc instanceof Npc) {
            $dialog = $npc->speak();
            $this->printNpcDialog($npc_name, $dialog);
        } else {
            $pp->writeLn('You are probably talking to a ghost', 'red');
        }
    }

    private function printNpcDialog(string $npc_name, string|array $dialog) : void {
        if(is_string($dialog)) {
            $this->printNpcSingleDial($npc_name, $dialog);
            return;
        }

        foreach($dialog as $dial) {
            $this->printNpcSingleDial($npc_name, $dial);
        }
    }

    private function printNpcSingleDial(string $npc_name, string $dial) : void {
        $pp = $this->container->getPrettyPrinter();
        $pp->write($npc_name . ' : ', 'green');
        $pp->writeScroll($dial, 20);
    }

    final public function defaultConfiguration() : array {
        return [
            'name'=>'Unknown',
            'gender'=>'Unknown',
            'statistics'=>Statistics::default()
        ];
    }
}