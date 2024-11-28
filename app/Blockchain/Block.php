<?php

namespace Ledgerchain\Blockchain;

use DateTime;

class Block
{
    public int $index;
    public string $data;
    public DateTime $timestamp;
    public string $hash;
    public string | null $previousHash;

    public function __construct(int $index, string $data, DateTime $timestamp, string $hash, string | null $previousHash)
    {
        $this->index = $index;
        $this->data = $data;
        $this->timestamp = $timestamp;
        $this->hash = $hash;
        $this->previousHash = $previousHash;
    }
}
