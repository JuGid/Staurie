<?php

use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Introduction\Introduction;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Level\Level;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\Menu\Menu;
use Jugid\Staurie\Component\Money\Money;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Component\Save\Save;
use Jugid\Staurie\Staurie;
use Jugid\Staurie\Example\Player;

require_once __DIR__.'/../vendor/autoload.php';

$staurie = new Staurie('My test game');
$staurie->register([
    Console::class, 
    PrettyPrinter::class, 
    Player::class, 
    Save::class,
    Inventory::class, 
    Level::class, 
    Money::class
]);

$menu = $staurie->getContainer()->registerComponent(Menu::class);
$menu->configuration([
    'text'=> 'Welcome to this awesome test adventure'
]);

$map = $staurie->getContainer()->registerComponent(Map::class);
$map->configuration([
    'directory'=>__DIR__.'/maps',
    'namespace'=>'Jugid\Staurie\Example\Maps', 
    'navigation'=>true
]);

$introduction = $staurie->getContainer()->registerComponent(Introduction::class);
$introduction->configuration([
    'text'=>[
        'This is an introduction to test the introduction component',
        'You can use it multiline by using an array in configuration'
    ],
    'title'=>'Chapter 1 : The new game',
    'scrolling'=>false
]);

$staurie->devmode();
$staurie->run();
