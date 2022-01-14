<?php

use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Introduction\Introduction;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Level\Level;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Component\Save\Save;
use Jugid\Staurie\Staurie;
use Jugid\Staurie\Example\Player;

require_once __DIR__.'/../vendor/autoload.php';

$staurie = new Staurie();
$staurie->register([
    Console::class, Player::class, Save::class, PrettyPrinter::class, Inventory::class, Level::class
]);

$map = $staurie->getContainer()->registerComponent(Map::class);
$map->config(['directory'=>__DIR__.'/maps','namespace'=>'Jugid\Staurie\Example\Maps', 'navigation'=>false]);

$introduction = $staurie->getContainer()->registerComponent(Introduction::class);
$introduction->config([
    'text'=>[
        'This is an introduction to test the introduction component',
        'You can use it multiline by using an array in configuration'
]]);

$staurie->devmode();
$staurie->run();
