<?php

namespace Jugid\Staurie\Component\Map;

use LogicException;
use Jugid\Staurie\Game\Position\Position;
use Symfony\Component\Finder\Finder;
use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Map\CoreFunctions\MoveFunction;
use Jugid\Staurie\Component\Map\CoreFunctions\CompassFunction;
use Jugid\Staurie\Component\Map\CoreFunctions\MapFunction;
use Jugid\Staurie\Component\Map\CoreFunctions\ViewFunction;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Game\Item;
use Jugid\Staurie\Game\Monster;
use Jugid\Staurie\Game\Npc;

class Map extends AbstractComponent {

    private const CONTAINER_BLUEPRINT = 'blueprint';
    private const CHAR_PER_LINE = 100;

    public Position $current_position;

    private MapPrinter $printer;

    final public function name() : string {
        return 'map';
    }

    final public function getEventName() : array {
        return ['map.view', 'map.move', 'map.compass', 'map.map'];
    }

    final public function require() : array {
        return [PrettyPrinter::class];
    }
    
    final public function initialize() : void {

        if(null === $this->config['directory'] || null === $this->config['namespace']) {
            throw new LogicException('Option directory or namespace is no configured. Please use the configuration().');
        }

        $console = $this->container->getConsole();
        $console->addFunction(new ViewFunction());

        if($this->config['navigation']) {
            $console->addFunction(new MoveFunction());
            
            if($this->config['compass_enable']) {
                $console->addFunction(new CompassFunction());
            }

            if($this->config['map_enable']) {
                $console->addFunction(new MapFunction());
            }
        }
        
        $this->initializeBlueprintsFromFiles();
        $this->current_position = new Position($this->config['x_start'],$this->config['y_start']);
        
        if(null === $this->getBlueprint($this->current_position)) {
            throw new LogicException('There is no map at start position which is '. $this->current_position);
        }

        $this->container->register('printer', MapPrinter::class);
    }

    final protected function action(string $event, array $arguments) : void {
        switch($event) {
            case 'map.move':
                $this->move($arguments['direction']);
                break;
            default:
                $this->eventToAction($event);
                break;
        }
    }

    protected function compass() : void {
        
        $map_names = [];
        $directions = ['north'=> ['x' => 0,  'y' => 1],'south'=> ['x' => 0,  'y' => -1],
                       'west' => ['x' => -1, 'y' => 0],'east' => ['x' => 1,  'y' => 0]];

        foreach($directions as $direction => $adds) {
            $positionToBlueprint = Position::get($this->current_position->x + $adds['x'], $this->current_position->y + $adds['y']);
            $blueprint = $this->getBlueprint($positionToBlueprint);

            $map_names[] = [$direction, $blueprint !== null ? $blueprint->name() : '---'];
        }

        $this->container->getPrettyPrinter()->writeTable(['Direction', 'Map'], $map_names);
    }

    protected function view() : void {
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

        $pp->writeTable([$current_blueprint->name() . ' ' . $this->current_position],$description_rows);

        if(!empty($current_blueprint->getNpcs())) {
            $npcs = [];
            foreach($current_blueprint->getNpcs() as $npc) {
                if(!$npc instanceof Npc) {
                   continue; 
                }

                $npcs[] = [$npc->name(), $npc->description()];
            }
    
            $pp->writeLn('There are npcs to speak with', 'green');
            $pp->writeTable(['Name', 'Description'], $npcs);
        }
        
        if(!empty($current_blueprint->getItems())) {
            $items = [];
            foreach($current_blueprint->getItems() as $item) {
                $stats = [];
                if(!$item instanceof Item) {
                    continue; 
                 }
                 
                foreach($item->statistics() as $stat=>$value) {
                    $stats[] = $stat.' : '.$value;
                }

                $items[] = [$item->name(), $item->description(), implode(', ', $stats) ];
            }

            $pp->writeLn('There are items to take', 'green');
            $pp->writeTable(['Name', 'Description', 'Statistics'], $items);
        }

        if(!empty($current_blueprint->getMonsters())) {
            $monsters = [];
            foreach($current_blueprint->getMonsters() as $monster) {
                if(!$monster instanceof Monster) {
                   continue; 
                }

                $monsters[] = [$monster->name(), $monster->description(), $monster->level()];
            }
    
            $pp->writeLn('There are monsters to fight with', 'green');
            $pp->writeTable(['Name', 'Description', 'Level'], $monsters);
        }
    }

    protected function move(string $direction) : void {
        $pp = $this->container->getPrettyPrinter();
        $previousPosition = clone $this->current_position;
        $go_function = 'go'. ucfirst($direction);
        $this->current_position->$go_function();

        if(null === $this->getBlueprint($this->current_position)) {
            $pp->writeLn('Something prevents you to go to the '.$direction, null, 'red');
            $this->current_position = $previousPosition;
        } 
    }

    protected function map() : void {
        $mapPrinter = $this->container->get('printer','MapPrinter');
        $mapPrinter->print();
    }

    public function teleport(Position $position) : void {
        $this->current_position = $position;
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

        if(!is_subclass_of($bp_class, Blueprint::class)) {
            throw new LogicException('Blueprint for map component should extends ' . Blueprint::class);
        }

        $this->container->register(self::CONTAINER_BLUEPRINT, $bp_class);
        return $this;
    }

    private function initializeBlueprintsFromFiles() : void {
        $finder = new Finder();
        $finder->files()->in($this->config['directory'])->name('*.php');

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

    public function getBlueprints() : array {
        return $this->container->gets(self::CONTAINER_BLUEPRINT);
    }

    final public function defaultConfiguration() : array {
        return [
            'directory'=> null,
            'namespace'=> null,
            'navigation'=> true,
            'map_enable'=> true,
            'compass_enable'=> true,
            'x_start'=> 0,
            'y_start'=> 0
        ];
    }

    
}