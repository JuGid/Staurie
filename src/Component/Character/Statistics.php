<?php

namespace Jugid\Staurie\Component\Character;

class Statistics {

    private array $statistics = [];

    public static function default() {
        $default_stats = new Statistics();
        return $default_stats->addDefault('chance', 0)
                             ->addDefault('ability', 0)
                             ->addDefault('wisdom', 0);
    }

    public function addDefault(string $name, int $default_value) : self {
        $this->statistics[$name] = $default_value;
        return $this;
    }

    public function asArray() : array {
        return $this->statistics;
    }
}