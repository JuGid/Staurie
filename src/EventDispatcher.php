<?php

namespace Jugid\Staurie;

use Jugid\Staurie\Interface\ListenerInterface;

class EventDispatcher {
    
    private array $listeners = [];

    public function dispatch(string $event, array $arguments = [])
    {
        $listeners = $this->getListenersForEvent($event);

        foreach($listeners as $listener) {
            $listener->notify($event, $arguments);
        }

        return $event;
    }

    public function getRegisteredEvents() : array {
        return array_keys($this->listeners);
    }

    public function getListenersForEvent(string $event): iterable
    {
        return $this->listeners[$event] ?? [];
    }

    public function addListener(string|array $event, ListenerInterface $listener) {
        if(is_array($event)) {
            foreach($event as $ev) {
                $this->addListener($ev, $listener, $listener->getPriority());
            }
        } elseif(is_string($event)) {
            $this->listeners[$event][] = $listener;
        }
    }

}