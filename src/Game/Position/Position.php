<?php

namespace Jugid\Staurie\Game\Position;

class Position {

    public int $x;

    public int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function get(int $x, int $y) : self {
        return new self($x, $y);
    }

    public function isSame(Position $position) : bool {
        return $position->x === $this->x && $position->y === $this->y;
    }

    public function goNorth() : void {
        $this->y += 1;
    }

    public function goSouth() : void {
        $this->y -= 1;
    }

    public function goEast() : void {
        $this->x += 1;
    }

    public function goWest() : void {
        $this->x -= 1;
    }

    public function __toString()
    {
        return '(' . $this->x . ';' . $this->y . ')';
    }
}