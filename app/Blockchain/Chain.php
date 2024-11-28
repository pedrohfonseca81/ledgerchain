<?php

namespace Ledgerchain\Blockchain;

use DateTime;
use Exception;

class Chain
{
    public array $chain = [];

    public function generateGenesisBlock(): array
    {
        if (count($this->chain) == 0) {
            $index = 0;
            $previousHash = null;
            $timestamp = date_create();
            $data = 'Genesis Block';

            $hash = $this->calculateHash($index, $previousHash, $timestamp, $data);

            $genesisBlock = new Block($index, $data, $timestamp, $hash, $previousHash);

            $this->chain[$index] = $genesisBlock;
        }

        return $this->chain;
    }

    public function calculateHash(int $index, string | null $previousHash, DateTime $timestamp, string $data): string
    {
        return hash('sha256', $index . (string) $previousHash . $timestamp->getTimestamp() . $data);
    }

    private function calculateHashForBlock(Block $block): string
    {
        return $this->calculateHash(
            $block->index,
            $block->previousHash,
            $block->timestamp,
            $block->data
        );
    }

    public function generateNextBlock(string $blockData): Block
    {
        $previousBlock = $this->getLatestBlock();
        $nextIndex = $previousBlock->index + 1;
        $nextTimestemp = date_create();
        $nextHash = $this->calculateHash($nextIndex, $previousBlock->hash, $nextTimestemp, $blockData);

        $newBlock = new Block($nextIndex, $blockData, $nextTimestemp, $nextHash, $previousBlock->hash);

        $this->chain[] = $newBlock;

        return $newBlock;
    }

    public function getLatestBlock(): Block
    {
        return $this->chain[count($this->chain) - 1];
    }

    private function isValidGenesis()
    {
        $genesisBlock = $this->chain[0];

        return $genesisBlock == $this->generateGenesisBlock()[0];
    }

    private function isValidNewBlock(Block $previousBlock, Block $newBlock): bool
    {
        if ($previousBlock->index + 1 !== $newBlock->index) {
            throw new Exception("Invalid chain");
            return false;
        } elseif ($previousBlock->hash !== $newBlock->previousHash) {
            throw new Exception("Invalid previous hash");
            return false;
        } elseif ($this->calculateHashForBlock($newBlock) !== $newBlock->hash) {
            throw new Exception("Invalid Hash");
            return false;
        }

        return true;
    }

    public function isValidChain()
    {
        if (!$this->isValidGenesis()) throw new Exception("Invalid Genesis Block");
        foreach ($this->chain as $block) {
            if ($block->index === 0) continue;
            if (!$this->isValidNewBlock($this->chain[$block->index - 1], $block)) continue;
            if ($block->hash === $this->calculateHash($block->index, $block->previousHash, $block->timestamp, $block->data)) continue;

            throw new Exception("Invalid chain");
        }

        return true;
    }
}
