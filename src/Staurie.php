<?php

namespace Jugid\Staurie;

use ErrorException;

class Staurie {

    private Container $container;

    public function __construct(string $name)
    {
        $this->container = new Container();
        $this->container->state()->setGameName($name);
    }

    public function devmode() {
        $this->getContainer()->state()->devmode(true);
    }

    public function run(callable $post_init_action = null) : void {
        $this->initialize();

        if($post_init_action !== null) {
            call_user_func($post_init_action, $this->container);
        }

        $this->container->dispatcher()->dispatch('menu.show');
        
        while($this->container->state()->isRunning()) {
            $this->container->dispatcher()->dispatch('console.console');
        }

        echo "Exiting, bye\n";
    }

    public function getContainer() : Container {
        return $this->container;
    }

    public function register(array $component_classes) {
        foreach($component_classes as $component_class) {
            $this->container->registerComponent($component_class);
        }
    }

    private function initialize() {
        $components = $this->container->gets(Container::CONTAINER_COMPONENTS);

        foreach($components as $name => $component) {
            foreach($component->require() as $requirement) {
                if(!$this->container->isComponentRegistered($requirement)) {
                    throw new ErrorException('The component '. $name . ' require ' . $requirement . ' but it is not registered');
                }
            }
        }

        foreach($components as $component) {
            $this->container->dispatcher()->addListener($component->getSystemEventNames(), $component);
            $this->container->dispatcher()->addListener($component->getEventName(), $component);
        }

        $this->container->dispatcher()->dispatch('staurie.initialize');
    }
}