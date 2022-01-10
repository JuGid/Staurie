<?php

namespace Jugid\Staurie\Interface;

interface ContainerInterface {
    public function gets(string $type) : array;
    public function get(string $type, string $name) : mixed ;
    public function remove(string $type, Containerable $element) : void;
    public function register(string $type, string $class_name) : mixed;
    
}