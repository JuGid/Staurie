<?php

namespace Jugid\Staurie;

class GameState {
    
    private bool $running;

    private bool $devmode;

    private string $gamename;

    public function __construct()
    {
        $this->running = true;
        $this->devmode = false;
        $this->gamename = '';
    }

    public function getGameName() : string {
        return $this->gamename;
    }

    public function setGameName(string $name) : void {
        $this->gamename = $name;
    }

    public function isRunning() : bool {
        return $this->running;
    }

    public function stop() : void {
        $this->running = false;
    }

    public function devmode(bool $mode) : void {
        $this->devmode = $mode;
    }

    public function isDevmode() : bool {
        return $this->devmode;
    }
}