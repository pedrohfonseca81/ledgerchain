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
    public int $difficulty;
    public int $nonce;

    public function __construct(int $index, string $data, DateTime $timestamp, string $hash, string | null $previousHash, int $difficulty = 0, int $nonce = 0)
    {
        $this->index = $index;
        $this->previousHash = $previousHash;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->hash = $hash;
        $this->difficulty = $difficulty;
        $this->nonce = $nonce;
    }
}
