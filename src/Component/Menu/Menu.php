<?php

namespace Jugid\Staurie\Component\Menu;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

class Menu extends AbstractComponent {

    private const MENU_OPTIONS = ['New game', 'Continue', 'Quit'];

    final public function name() : string {
        return 'menu';
    }

    final public function getEventName() : array {
        return ['menu.show'];
    }

    final public function require() : array {
        return [Console::class, PrettyPrinter::class];
    }
    
    final public function initialize() : void {

    }

    final public function defaultConfiguration() : array {
        return [
            'text'=>null
        ];
    }

    final protected function action(string $event, array $arguments) : void {
        $this->eventToAction($event);
    }

    final protected function show() : void {
        $pp = $this->container->getPrettyPrinter();
        $menu_title = strtoupper($this->container->state()->getGameName() .'\'s menu');

        $pp->writeUnder($menu_title, null, null, true);
        $pp->writeLn('');

        if(null !== $this->config['text']) {
            $pp->writeLn($this->config['text'], null, null, true);
            $pp->writeLn('');
        }

        foreach(self::MENU_OPTIONS as $index=>$option) {
            $pp->writeLn('['.$index.'] '.$option, null, null, true);
        }

        $choice = readline('>> ');

        switch($choice) {
            case '0':
                $this->newgame();
                break;
            case '1':
                $this->continue();
                break;
            case '2':
                $this->container->state()->stop();
                break;
        }
    }

    private function continue() : void {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn('Component Save is not implemented for the moment', null, 'red', true);
        $this->show();
    }

    private function newgame() : void {
        $pp = $this->container->getPrettyPrinter();
        $pp->writeLn("Beginning a new game\n", 'green', null, true);
        $this->container->dispatcher()->dispatch('character.new');
        $this->container->dispatcher()->dispatch('introduction.show');
        $pp->writeLn('');
        
    }
}