<?php

namespace Jugid\Staurie\Component\Map;

use LogicException;
use Jugid\Staurie\Game\Position;
use Symfony\Component\Finder\Finder;
use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Map\CoreFunctions\MoveFunction;
use Jugid\Staurie\Component\Map\CoreFunctions\ViewFunction;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

class Map extends AbstractComponent {

    private const CONTAINER_BLUEPRINT = 'blueprint';
    private const CHAR_PER_LINE = 100;

    private Position $current_position;

    final public function name() : string {
        return 'map';
    }

    final public function getEventName() : array {
        $events = ['map.view'];
        
        if($this->config['navigation']) {
            $events[] = 'map.move';
        }

        return $events;
    }

    final public function require() : array {
        return [PrettyPrinter::class];
    }
    
    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new MoveFunction());
        $console->addFunction(new ViewFunction());

        $this->initializeBlueprintsFromFiles();
        $this->current_position = new Position($this->config['x_start'],$this->config['y_start']);
        
        if(null === $this->getBlueprint($this->current_position)) {
            throw new LogicException('There is no map at start position which is '. $this->current_position);
        }
    }

    final protected function action(string $event, array $arguments) : void {
        switch($event) {
            case 'map.view':
                $this->view();
                break;
            case 'map.move':
                $this->move($arguments['direction']);
                break;
        }
    }

    private function view() : void {
        $pp = $this->container->getPrettyPrinter();
        $current_blueprint = $this->getBlueprint($this->current_position);
        if($current_blueprint === null) {
            throw new LogicException('No map at position '. $this->current_position . '. You should think about creating one.');
        }

        $pp->writeUnder('Map view', 'green');
        $description = str_split($current_blueprint->description(), self::CHAR_PER_LINE);
        $description_rows = [];
        foreach($description as $desc) {
            $description_rows[] = [$desc];
        }

        $pp->writeTable(
            [$current_blueprint->name() . ' ' . $this->current_position],
            $description_rows
        );

        $npcs = [];
        foreach($current_blueprint->getNpcs() as $npc) {
            $npcs[] = [$npc->name(), $npc->description()];
        }

        if(!empty($npcs)) {
            $pp->writeLn('There are npcs to speak with', null, 'green');
            $pp->writeTable(['Name', 'Description'], $npcs);
        }

        $items = [];
        foreach($current_blueprint->getItems() as $item) {
            $stats = [];
            foreach($item->statistics() as $stat=>$value) {
                $stats[] = $stat.' : '.$value;
            }

            $items[] = [$item->name(), $item->description(), implode(', ', $stats) ];
        }

        if(!empty($items)) {
            $pp->writeLn('There are items to take', null, 'green');
            $pp->writeTable(['Name', 'Description', 'Statistics'], $items);
        }
        
    }

    private function move(string $direction) : void {
        $prettyprinter = $this->container->getPrettyPrinter();
        $previousPosition = clone $this->current_position;
        switch($direction) {
            case 'north':
                $this->current_position->goNorth();
                break;
            case 'south':
                $this->current_position->goSouth();
                break;
            case 'west':
                $this->current_position->goWest();
                break;
            case 'east':
                $this->current_position->goEast();
                break;
        }

        if(null === $this->getBlueprint($this->current_position)) {
            $prettyprinter->writeLn('Something prevents you to go to the '.$direction, null, 'red');
            $this->current_position = $previousPosition;
        } else {
            $this->view();
        }

    }

    final public function getCurrentBlueprint() : Blueprint {
        return $this->getBlueprint($this->current_position);
    }

    final public function getBlueprint(Position $position) : ?Blueprint {
        $blueprints = $this->container->gets(self::CONTAINER_BLUEPRINT);

        foreach($blueprints as $bp) {
            if($bp->position()->isSame($position)) {
                return $bp;
            }
        }
        return null;
    }

    final public function addBlueprint(string $bp_class) : self {
        $blueprint_verif = new $bp_class();
        
        if(!$blueprint_verif instanceof Blueprint) {
            throw new LogicException('Blueprint for map component should extends ' . Blueprint::class);
        }

        unset($blueprint_verif);

        $this->container->register(self::CONTAINER_BLUEPRINT, $bp_class);
        return $this;
    }

    private function initializeBlueprintsFromFiles() : void {
        $directory = $this->config['directory'];
        
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php');

        foreach ($finder as $file) {
            $bp_file = str_replace('.php', '', $file->getRelativePathname());
            $bp_class = $this->fileDirectoryToNamespace($bp_file);

            $this->addBlueprint($bp_class);
        }

        $this->initializeBlueprints();
    }

    private function fileDirectoryToNamespace(string $file) : string {

        $file[0] = ucfirst($file[0]);

        for($i = 1; $i < strlen($file)-1; $i++) {
            if($file[$i] === '/' && isset($file[$i+1]) && ctype_alpha($file[$i+1])) {
                $file[$i+1] = ucfirst($file[$i+1]);
                $i++;
            }
        }
        $namespace = $this->config['namespace'];
        return $namespace . '\\'.str_replace('/', '\\', $file);
    }

    private function initializeBlueprints() : void {
        $blueprints = $this->container->gets(self::CONTAINER_BLUEPRINT);
        foreach($blueprints as $blueprint) {
            $blueprint->initialize();
        }
    }

    final public function defaultConfig() : array {
        return [
            'directory'=>null,
            'namespace'=>null,
            'navigation'=>true,
            'x_start'=>0,
            'y_start'=>0
        ];
    }

    
}