<?php

namespace Jugid\Staurie\Component\Console\CoreFunctions;

use Jugid\Staurie\Component\Console\AbstractConsoleFunction;

class DebugFunction extends AbstractConsoleFunction {

    private const PROPERTY_TYPE = 'property';
    private const CONTAINER_TYPE = 'container';
    private const COMPONENT_TYPE = 'component';
    private const EVENTS_TYPE = 'events';
    
    public function action(array $args) : void {

        $type = $args[0] ?? '';
        $element = $args[1] ?? '';

        if($this->getContainer()->state()->isDevmode()) {
            switch($type) {
                case self::CONTAINER_TYPE:
                    var_dump($this->getContainer()->gets($element));
                    break;
                case self::COMPONENT_TYPE:
                    var_dump($this->getContainer()->get('components', $element));
                    break;
                case self::EVENTS_TYPE:
                    if($element === 'show') {
                        var_dump($this->getContainer()->dispatcher()->getRegisteredEvents());
                    } else if($element === 'all') {
                        $components = $this->getContainer()->getComponents();
                        foreach($components as $component) {
                            echo $component->name() . " : \n\t" . implode("\n\t", $component->getEventName()) . "\n";
                        }
                    }
            }
        }
    }

    public function name() : string {
        return 'debug';
    }

    public function description() : string {
        return 'Help dev to debug';
    }

    public function getArgs() : int|array {
        return 2;
    }
}