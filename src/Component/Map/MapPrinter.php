<?php

namespace Jugid\Staurie\Component\Map;

use Jugid\Staurie\Container;
use Jugid\Staurie\Game\Position\Position;
use Jugid\Staurie\Game\Position\PositionCalculator;
use Jugid\Staurie\Interface\Containerable;
use Jugid\Staurie\Interface\Printer;
use LogicException;

/**
 * Print a map where all possible positions.
 * X for an existing map
 * P for the player position
 * Empty for a non existing position
 * The map is printed with DESC numbers as Y and ASC numbers as X.
 */
class MapPrinter implements Printer, Containerable {

    private Container $container;

    public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    public function name() : string {
        return 'MapPrinter';
    }

    public function print() {
        $map = $this->container->getMap();
        $pp = $this->container->getPrettyPrinter();
        $current_player_position = $map->getCurrentBlueprint()->position();

        $blueprints = $map->getBlueprints();

        if(null === $pp) {
            throw new LogicException('You must register the PrettyPrinter to use MapPrinter');
        }

        $positions_calculation = new PositionCalculator();
        $positions_calculation->with($blueprints);
        
        $max_position = $positions_calculation->max();
        $min_position = $positions_calculation->min();

        $header = [' '];
        $lines = [];

        for($x = $min_position->x; $x <= $max_position->x; $x++) {
            $header[] = $x;
        }
        
        for($y = $max_position->y; $y >= $min_position->y; $y--) {
            $line = [$y];
            for($x = $min_position->x; $x <= $max_position->x; $x++) {
                $blueprint = $map->getBlueprint(new Position($x,$y));
            
                if(null !== $blueprint) {
                    if($current_player_position->isSame($blueprint->position())) {
                        $line[] = 'P';
                    } else {
                        $line[] = 'X';
                    }
                } else {
                    $line[] = ' ';
                }
            }
            $lines[] = $line;
        }

        $pp->writeTable($header, $lines);
    }
}