<?php

namespace Jugid\Staurie\Game;

use Jugid\Staurie\Container;
use Jugid\Staurie\Interface\Describable;
use Jugid\Staurie\Interface\Nameable;
use Jugid\Staurie\Interface\Speakable;

abstract class Npc implements Nameable, Describable, Speakable {
    private Container $container;

    final public function setContainer(Container $container) {
        $this->container = $container;
    }

    final protected function playerHasItem(string $name) : bool {
        return null !== ($inventory = $this->container->getComponent('inventory')) && $inventory->hasItem($name);
    }

    final protected function giveItem(Item $item) {
        $this->container->dispatcher()->dispatch('inventory.give', ['item'=>$item]);
        $pp = $this->container->getComponent('prettyprinter');

        $give_sentence = $this->name() . ' give you the ' . $item->name();
        if($pp !== null) {
            $pp->writeLn($give_sentence, null, 'green');
        } else {
            echo $give_sentence, "\n";
        }
    }
}