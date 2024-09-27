<?php

namespace Jugid\Staurie\Component\Inventory;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\MainCharacter;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Inventory\CoreFunctions\DropFunction;
use Jugid\Staurie\Component\Inventory\CoreFunctions\InventoryFunction;
use Jugid\Staurie\Component\Inventory\CoreFunctions\TakeFunction;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Game\Item;

class Inventory extends AbstractComponent {

    public array $inventory = [];

    public int $size;

    final public function getEventName() : array {
        $events = ['inventory.view','inventory.size', 'inventory.give'];

        if($this->container->isComponentRegistered(Map::class)) {
            array_push($events, 'inventory.take','inventory.drop');
        }

        return $events;
    }

    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new InventoryFunction());

        if($this->container->isComponentRegistered(Map::class)) {
            $console->addFunction(new DropFunction());
            $console->addFunction(new TakeFunction());
        }
        
        $this->size = $this->config['inventory_size'];
    }

    final protected function action(string $event, array $arguments) : void {
        switch($event) {
            case 'inventory.take':
                $this->take($arguments['item_name']);
                break;
            case 'inventory.drop':
                $this->drop($arguments['item_name']);
                break;
            case 'inventory.give':
                $this->give($arguments['item']);
                break;
            default:
                $this->eventToAction($event);
                break;
        }
    }

    private function take(string $item_name) : void {
        $map = $this->container->getMap();
        $item = $map->getCurrentBlueprint()->takeItem($item_name);
        $pp = $this->container->getPrettyPrinter();

        if($item === null || $this->size <= count($this->inventory)) {
            $pp->writeLn('Item ' . $item_name . ' not takable or inventory is full', null, 'red');
            return;
        }

        $this->inventory[] = $item;
        $pp->writeLn('Item ' . $item->name() . ' added', null, 'green');
    }

    private function drop(string $item_name) : void {
        $pp = $this->container->getPrettyPrinter();

        if(!$this->hasItem($item_name)) {
            $pp->writeLn('Item ' . $item_name . ' is not in your inventory', null, 'red');
            return;
        }

        $map = $this->container->getMap();
        $map->getCurrentBlueprint()->dropItem($this->getItem($item_name));
        $this->removeItem($item_name);
        $pp->writeLn('Item dropped', null, 'green');
    }

    private function give(Item $item) {
        $pp = $this->container->getPrettyPrinter();

        $this->inventory[] = $item;
        $pp->writeLn($item->name() . ' added to your inventory', null, 'green');
    }

    final protected function view() : void {
        $pp = $this->container->getPrettyPrinter();
        $items = [];

        foreach($this->inventory as $item) {
            $stats = [];
            foreach($item->statistics() as $stat=>$value) {
                $stats[] = $stat.' : '.$value;
            }

            $items[] = [$item->name(), $item->description(), implode(', ', $stats) ];
        }

        if(!empty($items)) {
            $pp->writeUnder('Your inventory', 'green');
            $pp->writeTable(['Name', 'Description', 'Statistics'], $items);
        } else {
            $pp->writeLn('Inventory is empty', null, 'red');
        }
    }

    final protected function size() : void {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn("Inventory size : ". count($this->inventory) .'/'.$this->size);
    }

    public function getItem(string $name) : ?Item {
        foreach($this->inventory as $item) {
            if($item->name() === $name) {
                return $item;
            }
        }
        return null;
    }

    public function addItem(Item $item) : void {
        $this->inventory[] = $item;
    }

    public function removeItem(string $name) : void {
        foreach($this->inventory as $index => $item) {
            if($item->name() === $name) {
                unset($this->inventory[$index]);
            }
        }
    }

    public function hasItem(string $name) : bool {
        foreach($this->inventory as $item) {
            if($item->name() === $name) {
                return true;
            }
        }
        return false;
    }

    final public function name() : string {
        return 'inventory';
    }

    final public function defaultConfiguration() : array {
        return [
            'inventory_size'=>20,
            'stackable'=>true
        ];
    }

    final public function require() : array {
        return [Console::class, PrettyPrinter::class];
    }

}