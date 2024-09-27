<?php

namespace Jugid\Staurie\Game\Position;

use Jugid\Staurie\Component\Map\Blueprint;

class PositionCalculator {

    private array $blueprints;

    private string $first_name;

    private Position $max;

    private Position $min;

    public function with(array $blueprints) {
        
        $this->blueprints = $blueprints;
        $blueprints_names = array_keys($this->blueprints);
        $this->first_name = array_shift($blueprints_names);
        return $this;
    }

    public function max() : Position {
        $this->max = clone $this->getFirstBlueprint()->position();

        foreach($this->blueprints as $blueprint) {
            $current_position = $blueprint->position();

            if($this->max->x <= $current_position->x) {
                $this->max->x = $current_position->x;
            }
            
            if($this->max->y <= $current_position->y) {
                $this->max->y = $current_position->y;
            }
        }

        return $this->max;
    }

    public function min() : Position {
        $this->min = clone $this->getFirstBlueprint()->position();

        foreach($this->blueprints as $blueprint) {
            $current_position = $blueprint->position();
            if($this->min->x >= $current_position->x && $this->min->y >= $current_position->y) {
                $this->min = $current_position;
            }
        }

        return $this->min;
    }

    private function getFirstBlueprint() : Blueprint {
        return $this->blueprints[$this->first_name];
    }
}