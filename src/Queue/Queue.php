<?php

namespace Lavoaster\LaravelTactician\Queue;

trait Queue
{
    public $inQueue = false;

    public function setInQueue()
    {
        $this->inQueue = true;
    }
}
