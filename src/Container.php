<?php

namespace Jugid\Staurie;

use InvalidArgumentException;
use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\MainCharacter;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\GameState;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\ContainerInterface;

class Container implements ContainerInterface {

    public const CONTAINER_COMPONENTS = 'components';

    private array $container;

    private GameState $state;

    private EventDispatcher $dispatcher;

    public function __construct()
    {
        $this->container = [];
        $this->state = new GameState();
        $this->dispatcher = new EventDispatcher();
    }

    public function gets(string $type) : array {
        return $this->container[$type] ?? [];
    }

    public function get(string $type, string $name) : mixed {
        return $this->container[$type][$name] ?? null;
    }

    public function remove(string $type, Containerable $element) : void {
        unset($this->container[$type][$element->name()]);
    }

    public function register(string $type, string $class_name) : mixed {
        $element = new $class_name();

        if(!$element instanceof Containerable) {
            throw new InvalidArgumentException('Element of class ' . $class_name . ' should implements Containerable');
        }

        $element->setContainer($this);
        
        if(!isset($this->container[$type][$element->name()])) {
            $this->container[$type][$element->name()] = $element; 
        } else {
            echo 'Element ' , $element->name() , ' already exists in container. Skipped.' , "\n";
        }

        return $this->container[$type][$element->name()];
    }

    public function registerComponent(string $component_class, int $priority = 0) : mixed {
        $component = new $component_class();

        if(!$component instanceof AbstractComponent) {
            throw new InvalidArgumentException($component_class . 'does not extends ' . AbstractComponent::class);
        }

        $this->register(self::CONTAINER_COMPONENTS, $component_class);

        ($this->container[self::CONTAINER_COMPONENTS][$component->name()])->setPriority($priority);
        return $this->getComponent($component->name());
    }

    public function state() : GameState {
        return $this->state;
    }

    public function dispatcher() : EventDispatcher {
        return $this->dispatcher;
    }

    public function getComponents() : array {
        return $this->container[self::CONTAINER_COMPONENTS];
    }

    public function getComponent(string $component) : mixed {
        return $this->get(self::CONTAINER_COMPONENTS, $component) ?? null;
    }

    public function isComponentRegistered(string $class_name) : bool {
        foreach($this->container[self::CONTAINER_COMPONENTS] as $component) {
            if(get_class($component) === $class_name || $component instanceof $class_name) {
                return true;
            }
        }
        return false;
    }

    public function getPrettyPrinter() : ?PrettyPrinter {
        return $this->get(self::CONTAINER_COMPONENTS,'prettyprinter');
    }

    public function getCharacter() : ?MainCharacter {
        return $this->get(self::CONTAINER_COMPONENTS,'character');
    }

    public function getConsole() : ?Console {
        return $this->get(self::CONTAINER_COMPONENTS,'console');
    }

    public function getMap() : ?Map {
        return $this->get(self::CONTAINER_COMPONENTS, 'map');
    }

    public function getInventory() : ?Inventory {
        return $this->get(self::CONTAINER_COMPONENTS, 'inventory');
    }
}