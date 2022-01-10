<?php

namespace Jugid\Staurie\Component\Map;

use Jugid\Staurie\Container;
use Jugid\Staurie\Game\Item;
use Jugid\Staurie\Game\Npc;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Initializable;
use Jugid\Staurie\Interface\Positionnable;

abstract class Blueprint implements Containerable, Initializable, Describable, Positionnable {

    private Container $container;

    private array $npcs = [];

    private array $items = [];

    final public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    final public function initialize() : void {
        $npcs_class = $this->npcs();
        $items_class = $this->items();

        foreach($npcs_class as $class) {
            $npc = new $class();
            $npc->setContainer($this->container);
            $this->npcs[$npc->name()] = $npc;
        }

        foreach($items_class as $class) {
            $item = new $class();
            $this->items[$item->name()] = $item;
        }
    }

    public function getNpc(string $npc_name) : ?Npc{
        if(!$this->hasNpc($npc_name)) {
            return null;
        }

        return $this->npcs[$npc_name];
    }

    private function hasNpc(string $npc_name) : bool {
        return isset($this->npcs[$npc_name]);
    }

    public function takeItem(string $item_name) : ?Item{
        if(!$this->hasItem($item_name)) {
            return null;
        }
        
        $item = $this->items[$item_name];
        unset($this->items[$item_name]);
        return $item;
    }

    public function dropItem(Item $item) {
        $this->items[$item->name()] = $item;
    }

    private function hasItem(string $item_name) : bool {
        return isset($this->items[$item_name]);
    }

    public function getNpcs() : ?array {
        return $this->npcs;
    }

    public function getItems() : ?array {
        return $this->items;
    }

    abstract public function npcs() : array;
    abstract public function items() : array;

}