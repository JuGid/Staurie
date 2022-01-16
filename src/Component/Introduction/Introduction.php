<?php

namespace Jugid\Staurie\Component\Introduction;

use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;

class Introduction extends AbstractComponent {

    public function name() : string {
        return 'introduction';
    }

    final public function require() : array {
        return [PrettyPrinter::class];
    }

    public function initialize(): void
    {
        
    }

    public function getEventName() : array {
        return ['introduction.show'];
    }

    protected function action(string $event, array $arguments) : void {
        switch($event){
            case 'introduction.show':
                $this->show();
                break;
        }
    }

    private function show() {
        $text = $this->config['text'];
        $scrolling = $this->config['scrolling'];
        $scrolling_speed = $this->config['scrolling_speed'];

        $pp = $this->container->getPrettyPrinter();
        
        $pp->writeUnder(strtoupper($this->config['title']), null, null, true);
        $this->printIntroduction($text, $scrolling, $scrolling_speed, $pp);
    }

    private function printIntroduction(string|array $text, bool $scrolling, int $scrolling_speed, PrettyPrinter $pp) {
        if(is_array($text)) {
            foreach($text as $line) {
                $this->printIntroduction($line, $scrolling, $scrolling_speed, $pp);
            }
        }

        if(is_string($text)) {
            if($scrolling) {
                $pp->writeScroll($text, $scrolling_speed, true);
            } else {
                $pp->writeLn($text, null, null, true);
            }
        }
    }

    public function defaultConfiguration(): array
    {
        return [
            'title' => $this->container->state()->getGameName(),
            'text' => ['Your game introduction'],
            'scrolling' => true,
            'scrolling_speed' => 5
        ];
    }
}