<?php

namespace Jugid\Staurie\Component\Character;

use LogicException;

class Statistics {

    private array $statistics = [];

    public static function default() {
        $default_stats = new Statistics();
        return $default_stats->addDefault('chance', 0)
                             ->addDefault('ability', 0)
                             ->addDefault('wisdom', 0)
                             ->addDefault('defense', 0);
    }

    public function addDefault(string $name, int $default_value) : self {
        $this->statistics[$name] = $default_value;
        return $this;
    }

    public function add(string $name, int $value) : self {
        if(!$this->has($name)) {
            $this->statistics[$name] = 0;
        }

        $this->statistics[$name] += $value;
        return $this;
    }

    public function sub(string $name, int $value) : self {
        if(!$this->has($name)) {
            $this->statistics[$name] = 0;
        }
        
        $this->statistics[$name] -= $value;
        return $this;
    }

    public function has(string $name) : bool {
        return isset($this->statistics[$name]);
    }

    public function value(string $name) : int {
        if(!$this->has($name)) {
            throw new LogicException("Value for $name is not possible.");
        }

        return $this->statistics[$name];
    }

    public function asArray() : array {
        return $this->statistics;
    }
}