<?php

use Jugid\Staurie\Component\Console\Console;
use Jugid\Staurie\Component\Inventory\Inventory;
use Jugid\Staurie\Component\Map\Map;
use Jugid\Staurie\Component\PrettyPrinter\PrettyPrinter;
use Jugid\Staurie\Component\Save\Save;
use Jugid\Staurie\Staurie;
use Jugid\Staurie\Tests\Player;

require_once __DIR__.'/../../vendor/autoload.php';

$staurie = new Staurie();

$console = $staurie->getContainer()->registerComponent(Console::class);
$character = $staurie->getContainer()->registerComponent(Player::class);
$save = $staurie->getContainer()->registerComponent(Save::class);
$map = $staurie->getContainer()->registerComponent(Map::class);
$prettyprinter = $staurie->getContainer()->registerComponent(PrettyPrinter::class);
$inventory = $staurie->getContainer()->registerComponent(Inventory::class);

$map->config([
    'directory'=>__DIR__.'/maps',
    'namespace'=>'Jugid\Staurie\Tests\Maps'
]);

$staurie->devmode();
$staurie->run();
