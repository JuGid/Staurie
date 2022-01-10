<?php

namespace Jugid\Staurie\Component\Level;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Character\MainCharacter;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

class Level extends AbstractComponent {

    private $level;

    private $experience;

    public function name() : string {
        return 'level';
    }

    final public function require() : array {
        return [PrettyPrinter::class, MainCharacter::class];
    }

    public function initialize(): void
    {
        $this->level = 1;
        $this->experience = 0;
    }

    public function getEventName() : array {
        return ['level.up', 'level.ask', 'level.gain'];
    }

    protected function action(string $event, array $arguments) : void {
        switch($event){
            case 'level.up':
                $this->up();
                break;
            case 'level.ask':
                $this->ask();
                break;
            case 'level.gain':
                break;
        }
    }

    private function up() {
        if($this->level < $this->config['max_level']) {
            $this->level++;
        }
    }

    private function ask() {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn('Level : ' . $this->level . '/' . $this->config['max_level']);
        $pp->writeProgressbar($this->experience, 0, $this->getExperienceForCurrentLevel());
    }

    private function getExperienceForCurrentLevel() : int {
        $formula = preg_replace('/\{level\}/', $this->level, $this->config['formula']);
        return eval('return '.$formula.';');
    }

    public function defaultConfig(): array
    {
        return [
            'formula'=> '{level}*{level}+110',
            'max_level'=>50
        ];
    }
}