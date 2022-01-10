<?php

namespace Jugid\Staurie\Interface;

interface Configurable {
    public function config(array $config);
    public function defaultConfig() : array;
}