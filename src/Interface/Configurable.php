<?php

namespace Jugid\Staurie\Interface;

interface Configurable {
    public function configuration(array $config);
    public function defaultConfiguration() : array;
}