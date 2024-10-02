<?php

namespace Jugid\Staurie\Component\Character;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\CoreFunctions\EquipFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\FightFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\MainCharacterFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\SpeakFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\StatsFunction;
use Jugid\Staurie\Component\Character\CoreFunctions\UnequipFunction;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Level\Level;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Game\Item_Equippable;
use Jugid\Staurie\Game\Npc;
use Jugid\Staurie\Game\Position\Position;
use LogicException;

class MainCharacter extends AbstractComponent {

    public Statistics $statistics;

    public string $name;

    public string $gender;

    public array $equipment;

    final public function name() : string {
        return 'character';
    }

    final public function getEventName() : array {
        $events = ['character.me', 'character.new'];

        if($this->container->isComponentRegistered(Map::class)) {
            array_push($events, 'character.speak');
            array_push($events, 'character.fight');
        }

        if($this->container->isComponentRegistered(Inventory::class)) {
            array_push($events, 'character.equip');
            array_push($events, 'character.unequip');
        }

        if($this->container->isComponentRegistered(Level::class)) {
            array_push($events, 'character.stats');
        }

        return $events;
    }

    final public function require() : array {
        return [PrettyPrinter::class];
    }

    final public function initialize() : void {
        $console = $this->container->getConsole();
        $console->addFunction(new MainCharacterFunction());
        
        if($this->container->isComponentRegistered(Map::class)) {
            $console->addFunction(new SpeakFunction());
            $console->addFunction(new FightFunction());
        }

        if($this->container->isComponentRegistered(Inventory::class)) {
            $console->addFunction(new EquipFunction());
            $console->addFunction(new UnequipFunction());
        }

        if($this->container->isComponentRegistered(Level::class)) {
            $console->addFunction(new StatsFunction());
        }

        $this->statistics = $this->config['statistics'];
        $this->name = $this->config['name'];
        $this->gender = $this->config['gender'];
        $this->equipment = $this->config['equipment'];
    }

    final protected function action(string $event, array $arguments) : void {
        $pp = $this->container->getPrettyPrinter();

        switch($event) {
            case 'character.speak':
                $this->speak($arguments['to']);
                break;
            case 'character.equip':
                $this->equip($arguments['item'], $arguments['body_part']);
                break;
            case 'character.unequip':
                $this->unequip($arguments['item'], $arguments['body_part']);
                break;
            case 'character.stats':
                $this->stats($arguments['type'], $arguments['stat']);
                break;
            case 'character.fight':
                $this->fight($arguments['monster']);
                break;
            default:
                $this->eventToAction($event);
                break;
        }
    }

    final protected function new() {
        if($this->config['ask_name']) {
            $this->name = readline('Character name : ');
        }
        
        if($this->config['ask_gender']) {
            $this->gender = readline('Character gender : ');
        }
        
        $this->container->dispatcher()->dispatch('race.ask');
        $this->container->dispatcher()->dispatch('tribe.ask');

        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn('Welcome ' . $this->name, 'green');
    }

    final protected function me() {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeUnder('Details', 'green');

        if($this->config['character_has_name']) {
            $pp->writeLn('Name : ' . $this->name);
        }
        
        if($this->config['character_has_gender']) {
            $pp->writeLn('Gender : ' . $this->gender);
        }
        
        $this->container->dispatcher()->dispatch('race.view');
        $this->container->dispatcher()->dispatch('tribe.view');
        $this->container->dispatcher()->dispatch('level.view');

        $pp->writeUnder("\nYour equipment", 'green');
        $header = ['Body part', 'Name', 'Statistics'];
        $line = [];
        foreach($this->equipment as $body_part => $equipment) {
            $stats = array_map(function(string $type, string $value) {
                return "$type : $value";
            }, array_keys($equipment?->statistics() ?? []), array_values($equipment?->statistics() ?? []));

            $line[] = [$body_part, $equipment?->name() ?? '---',implode(', ', $stats)];
        }
        $pp->writeTable($header, $line);

        $pp->writeUnder("\nYour statistics", 'green');
        $header = ['Attribute', 'Value'];
        $line = [];

        foreach($this->statistics->asArray() as $name=>$value) {
            $line[] = [ucfirst($name), $value];
        }
        $pp->writeTable($header, $line);
    }

    private function speak(string $npc_name) {
        $pp = $this->container->getPrettyPrinter();
        $npc = $this->container->getMap()->getCurrentBlueprint()->getNpc($npc_name);

        if(null !== $npc && $npc instanceof Npc) {
            $dialog = $npc->speak();
            $this->printNpcDialog($npc_name, $dialog);
        } else {
            $pp->writeLn('You are probably talking to a ghost', 'red');
        }
    }

    private function equip(string $item_name, string $body_part) {
        $pp = $this->container->getPrettyPrinter();
        $inventory = $this->container->getInventory();

        $item = $inventory->getItem($item_name);

        if($item === null) {
            $pp->writeLn('Item not found', null, 'red');
            return;
        }

        if(!in_array($body_part, array_keys($this->equipment))) {
            $format = 'Body part does not exist. Should be in %s';
            $pp->writeLn(sprintf($format, implode(',', array_keys($this->equipment))), null, 'red');
            return;
        }

        if(!$item instanceof Item_Equippable) {
            $pp->writeLn('This item is not equippable', null, 'red');
            return;
        }

        if($body_part !== $item->body_part()) {
            $pp->writeLn("This item cannot be on your $body_part", null, 'red');
            return;
        }

        if($this->equipment[$body_part] !== null) {
            $this->unequip(($this->equipment[$body_part])->name(), $body_part);
        }

        $this->equipment[$body_part] = clone $item;

        foreach($item->statistics() as $type => $value) {
            $this->statistics->add($type, $value);
        }

        $inventory->removeItem($item_name);
        $pp->writeLn("Item $item_name is yours !");
    }

    private function unequip(string $item_name, string $body_part) {
        $pp = $this->container->getPrettyPrinter();
        $inventory = $this->container->getInventory();

        if(!in_array($body_part, array_keys($this->equipment))) {
            $format = 'Body part does not exist. Should be in %s';
            $pp->writeLn(sprintf($format, implode(',', array_keys($this->equipment))), null, 'red');
            return;
        }

        $item = $this->equipment[$body_part];

        if($item === null || $item->name() !== $item_name) {
            $pp->writeLn('Item not found', null, 'red');
            return;
        }

        $inventory->addItem(clone $item);

        foreach($item->statistics() as $type => $value) {
            $this->statistics->sub($type, $value);
        }

        $this->equipment[$body_part] = null;
        $pp->writeLn("This $item_name was not worthy !");
    }

    private function stats(string $type, string $stat) : void {
        $pp = $this->container->getPrettyPrinter();
        $level = $this->container->getComponent('level');

        if(!in_array($stat, array_keys($this->statistics->asArray()))) {
            $pp->writeLn("Stat $stat does not exist.", 'red');
        }

        switch($type) {
            case 'add' :
                if($level->points > 0) {
                    $this->statistics->add($stat, 1);
                    $level->points -= 1;
                    $pp->writeLn("One point added to $stat", 'green');
                    break;
                }
                $pp->writeLn("You don't have enough points", 'red');
                break;
            default:
                $pp->writeLn("You can only use function add", 'red');
        }
    }

    private function fight(string $monster_name) : void {
        $pp = $this->container->getPrettyPrinter();
        $blueprint = $this->container->getMap()->getCurrentBlueprint();
        $monster = $blueprint->getMonster($monster_name);

        if($monster === null) {
            $pp->writeLn('This monster is not accessible', 'red');
            return;
        }

        $fight_config = $this->config['fight'];

        $player_health = $this->statistics->value($fight_config['health']);
        $player_defense = $this->statistics->value($fight_config['defense']);
        $player_attack = $this->statistics->value($fight_config['attack']);

        $monster_health = $monster->health_points();

        $pp->writeLn("Fight starts against $monster_name !", 'green');
        $round = 1;
        while($monster_health > 0 && $player_health > 0) {
            $pp->writeUnder("Round $round starts", "green");
            $pp->writeLn("[0] Attack");
            $pp->writeLn("[1] Defend");
            $pp->writeLn("[2] Escape\n");

            do {
                $fight_choice = readline('What would you do ? ');
            } while (!in_array($fight_choice, ['0', '1', '2']));

            switch($fight_choice) {
                case '0':
                    $monster_damages = $player_defense >= $monster->attack() ? 0 : ($player_defense - $monster->attack());
                    $player_damages = $monster->defense() >= $player_attack ? 0 : ($monster->defense() - $player_attack);

                    $monster_health -= abs($player_damages);
                    $player_health -= abs($monster_damages);

                    $pp->writeLn("$monster_name attacks. You loose $monster_damages in the process", 'red');
                    $pp->writeLn("$monster_name looses $player_damages", 'red');
                    break;
                case '1':
                    $monster_damages = ($player_defense*2) >= $monster->attack() ? 0 : (($player_defense * 2) - $monster->attack());
                    $player_health -= $monster_damages;

                    $pp->writeLn("$monster_name attacks. You loose $monster_damages in the process", 'red');
                    break;
                case '2':
                    $health_diff = $this->statistics->value($fight_config['health'])/2;
                    $this->statistics->sub($fight_config['health'], $health_diff);
                    $pp->writeLn("You choose to escape, loosing 50% of your health ($health_diff).", 'red');
                    return;
            }

            $pp->writeUnder("You : $player_health, $monster_name: $monster_health");
            $round++;
        }

        if($monster_health <= 0) {
            $pp->writeLn("$monster_name is dead. Get ready for the next one !");
            $blueprint->killMonster($monster_name);
            $level = $this->container->getComponent('level');
            $level->experience += $monster->experience();
            $level->verify();
        } else if($player_health <= 0) {
            $pp->writeLn('You are dead. Not ready for that...');
            $action_at_death = $this->config['action_at_death'];
            $map = $this->container->getMap();

            match(true) {
                $action_at_death instanceof Position => $map->teleport($action_at_death),
                $action_at_death === null => exit('...You loose')
            };
        }

        if(!$this->config['health_reset_after_fight']) {
            $health_diff = $this->statistics->value($fight_config['health']) - $player_health;
            $this->statistics->sub($fight_config['health'], $health_diff);
            $pp->writeLn("You loose $health_diff in the fight.", 'red');
        }

    }

    private function printNpcDialog(string $npc_name, string|array $dialog) : void {
        if(is_string($dialog)) {
            $this->printNpcSingleDial($npc_name, $dialog);
            return;
        }

        foreach($dialog as $dial) {
            $this->printNpcSingleDial($npc_name, $dial);
        }
    }

    private function printNpcSingleDial(string $npc_name, string $dial) : void {
        $pp = $this->container->getPrettyPrinter();
        $pp->write($npc_name . ' : ', 'green');
        $pp->writeScroll($dial, 20);
    }

    final public function defaultConfiguration() : array {
        return [
            'name'=>'Unknown',
            'gender'=>'Unknown',
            'ask_name' => true,
            'ask_gender' => true,
            'character_has_name' => true,
            'character_has_gender' => true,
            'statistics'=>Statistics::default(),
            'fight' => [
                'health' => 'health',
                'attack' => 'ability',
                'defense' => 'defense'
            ],
            'health_reset_after_fight' => true,
            'equipment' => [
                'head' => null,
                'hand' => null,
                'shield' => null,
                'feet' => null,
                'shoulders' => null,
            ],
            'action_at_death' => null
        ];
    }

    final public function hasEnoughStats(string $stat_name, int $value) : bool {
        if(!$this->statistics->has($stat_name)) {
            throw new LogicException("Stat $stat_name does not exist");
        }

        return $this->statistics->value($stat_name) >= $value;
    }
}