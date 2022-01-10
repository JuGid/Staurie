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
        return ['staurie.initialize'];
    }

    final public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    final public function notify(string $event, array $arguments) {
        if(in_array($event, $this->getSystemEventNames())) {
            switch($event) {
                case 'staurie.initialize':
                    $this->config = $this->config ?? $this->defaultConfig();
                    $this->initialize();
                    break;
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

    final public function config(array $config) : void {
        $default = $this->defaultConfig();

        foreach($default as $name=>$value) {
            if(!isset($config[$name])) {
                $this->config[$name] = $value;
            } else {
                $this->config[$name] = $config[$name];
            }
        }
    }

    abstract public function getEventName() : array;
    abstract protected function action(string $event, array $arguments) : void;
}