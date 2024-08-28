<?php

namespace Jugid\Staurie\Component\Character;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\CoreFunctions\EquipFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\MainCharacterFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\SpeakFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\UnequipFunction;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Game\Item_Equippable;
use Jugid\Staurie\Game\Npc;
use LogicException;

class MainCharacter extends AbstractComponent {

    private Statistics $statistics;

    private string $name;

    private string $gender;

    private array $equipment;

    final public function name() : string {
        return 'character';
    }

    final public function getEventName() : array {
        $events = ['character.me', 'character.new'];

        if($this->container->isComponentRegistered(Map::class)) {
            array_push($events, 'character.speak');
        }

        if($this->container->isComponentRegistered(Inventory::class)) {
            array_push($events, 'character.equip');
            array_push($events, 'character.unequip');
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

        if($this->container->isComponentRegistered(Inventory::class)) {
            $console->addFunction(new EquipFunction());
            $console->addFunction(new UnequipFunction());
        }

        $this->statistics = $this->config['statistics'];
        $this->name = $this->config['name'];
        $this->gender = $this->config['gender'];
        $this->equipment = $this->config['equipment'];
    }

    final protected function action(string $event, array $arguments) : void {
        $pp = $this->container->getPrettyPrinter();

        switch($event) {
            case 'character.speak':
                $this->speak($arguments['to']);
                break;
            case 'character.equip':
                $this->equip($arguments['item'], $arguments['body_part']);
                break;
            case 'character.unequip':
                $this->unequip($arguments['item'], $arguments['body_part']);
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

        $pp->writeUnder("\nYour equipment", 'green');
        $header = ['Body part', 'Name', 'Statistics'];
        $line = [];
        foreach($this->equipment as $body_part => $equipment) {
            $stats = array_map(function(string $type, string $value) {
                return "$type : $value";
            }, array_keys($equipment?->statistics() ?? []), array_values($equipment?->statistics() ?? []));

            $line[] = [$body_part, $equipment?->name() ?? '---',implode(', ', $stats)];
        }
        $pp->writeTable($header, $line);

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

    private function equip(string $item_name, string $body_part) {
        $pp = $this->container->getPrettyPrinter();
        $inventory = $this->container->getInventory();

        $item = $inventory->getItem($item_name);

        if($item === null) {
            $pp->writeLn('Item not found', null, 'red');
            return;
        }

        if(!in_array($body_part, array_keys($this->equipment))) {
            $format = 'Body part does not exist. Should be in %s';
            $pp->writeLn(sprintf($format, implode(',', array_keys($this->equipment))), null, 'red');
            return;
        }

        if(!$item instanceof Item_Equippable) {
            $pp->writeLn('This item is not equippable', null, 'red');
            return;
        }

        if($body_part !== $item->body_part()) {
            $pp->writeLn("This item cannot be on your $body_part", null, 'red');
            return;
        }

        $this->equipment[$body_part] = clone $item;

        foreach($item->statistics() as $type => $value) {
            $this->statistics->add($type, $value);
        }
        $inventory->removeItem($item_name);
        $pp->writeLn("Item $item_name is yours !");
    }

    private function unequip(string $item_name, string $body_part) {
        $pp = $this->container->getPrettyPrinter();
        $inventory = $this->container->getInventory();

        if(!in_array($body_part, array_keys($this->equipment))) {
            $format = 'Body part does not exist. Should be in %s';
            $pp->writeLn(sprintf($format, implode(',', array_keys($this->equipment))), null, 'red');
            return;
        }

        $item = $this->equipment[$body_part];

        if($item === null || $item->name() !== $item_name) {
            $pp->writeLn('Item not found', null, 'red');
            return;
        }

        $inventory->addItem(clone $item);

        foreach($item->statistics() as $type => $value) {
            $this->statistics->sub($type, $value);
        }

        $this->equipment[$body_part] = null;
        $pp->writeLn("This $item_name was not worthy !");
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
            'statistics'=>Statistics::default(),
            'equipment' => [
                'head' => null,
                'hand' => null,
                'shield' => null,
                'feet' => null,
                'shoulders' => null,
            ]
        ];
    }

    final public function hasEnoughStats(string $stat_name, int $value) : bool {
        if(!$this->statistics->has($stat_name)) {
            throw new LogicException("Stat $stat_name does not exist");
        }

        return $this->statistics->value($stat_name) >= $value;
    }
}