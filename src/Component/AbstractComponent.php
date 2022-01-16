<?php

namespace Jugid\Staurie\Component;

use Jugid\Staurie\Container;
use Jugid\Staurie\Interface\Configurable;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Initializable;
use Jugid\Staurie\Interface\ListenerInterface;

abstract class AbstractComponent implements ListenerInterface, Containerable, Configurable, Initializable {

    protected Container $container;
    private int $priority;
    protected array $config;

    public function require() : array {
        return [];
    }

    final public function getSystemEventNames() : array {
        return ['staurie.initialize', 'staurie.save', 'staurie.load'];
    }

    final public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    final public function notify(string $event, array $arguments) {
        if(in_array($event, $this->getSystemEventNames())) {
            switch($event) {
                case 'staurie.initialize':
                    $this->config = $this->config ?? $this->defaultConfiguration();
                    $this->initialize();
                    break;
                case 'staurie.save':
                    echo "Saving ". $this->name() ."\n";
            }
        } elseif(in_array($event, $this->getEventName())) {
            $this->action($event, $arguments);
        }
    }

    final public function setPriority(int $priority) {
        $this->priority = $priority;
    }

    final public function getPriority() : int {
        return $this->priority;
    }

    final public function configuration(array $config) : void {
        $default = $this->defaultConfiguration();
        $this->config = array_merge($default, $config);
    }

    /**
     * Return the last event part, exploded by $separator, and call it.
     */
    final protected function eventToAction(string $event, string $separator = '.') {
        $event_explode = explode($separator, $event);
        $action = end($event_explode);
        $this->$action();
    }

    abstract public function getEventName() : array;
    abstract protected function action(string $event, array $arguments) : void;
}