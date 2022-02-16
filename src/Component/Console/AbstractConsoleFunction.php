<?php

namespace Jugid\Staurie\Component\Console;

use Jugid\Staurie\Container;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Describable;

abstract class AbstractConsoleFunction implements Containerable, Describable {

    private Container $container;

    private int $nbArgs;

    private array $possibleArgs;

    final public function initialize() : void {
        $args = $this->getArgs();

        if(is_array($args)) {
            $this->possibleArgs = $args;
            $this->nbArgs = 1;
        } elseif(is_int($args)) {
            $this->possibleArgs = [];
            $this->nbArgs = $args;
        }
    }

    final public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    final public function getContainer() : Container {
        return $this->container;
    }

    final public function isTheFunctionINeed($name, array $args) : bool {
        if($this->name() !== $name) {
            return false;
        }

        if(!empty($this->possibleArgs)) {
            if(!isset($args[0])) {
                return false;
            }
            
            return in_array($args[0], $this->possibleArgs);
        }

        return true;
    }

    final public function getFunctionAsArray() : array {
        $args = is_array($this->getArgs()) ? implode(',', $this->getArgs()) : $this->getArgs();
        
        return [$this->name(), $this->description(), ($this->nbArgs > 0 ? '['. $args . ']' : '')];
    }

    final protected function ask(string $question, ?array $possibleOptions = null) {
        $question_complete = $question . ($possibleOptions !== null ? ' ('.implode('/', $possibleOptions).')' : '') . ' >> ';
        
        return readline($question_complete);
    }

    abstract public function action(array $args) : void;
    abstract public function getArgs() : int|array;
}