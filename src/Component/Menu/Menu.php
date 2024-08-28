<?php

namespace Jugid\Staurie\Component\Menu;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use LogicException;

class Menu extends AbstractComponent {

    private const MENU_OPTIONS_ORDER = ['new_game', 'quit'];

    private $menu_options = [];

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

        foreach(self::MENU_OPTIONS_ORDER as $option) {
            if(!in_array($option, array_keys($this->config['labels']))) {
                throw new LogicException('You MUST set all the labels for you menu labels if you change one');
            }

            $this->menu_options[] = $this->config['labels'][$option];
        }
    }

    final public function defaultConfiguration() : array {
        return [
            'labels'=> [
                'new_game'=> 'New game',
                //'continue' => 'Continue',
                'quit'=>'Quit'
            ],
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

        foreach($this->menu_options as $index=>$option) {
            $pp->writeLn('['.$index.'] '.$option, null, null, true);
        }

        $choice = readline('>> ');

        switch($choice) {
            case '0':
                $this->newgame();
                break;
            //case '1':
            //    $this->continue();
            //    break;
            case '1':
                $this->container->state()->stop();
                break;
            default:
                $pp->writeLn('Not a valid answer', 'red');
                $this->show();
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