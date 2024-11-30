<?php

namespace Ledgerchain\Blockchain;

use DateTime;

class ProofOfWork
{
    public function hashMatchesDifficulty($hash, $difficulty): bool
    {
        $hashInBinary = $this->hexToBinary($hash);

        $requiredPrefix = str_repeat("0", $difficulty);

        return str_starts_with($hashInBinary, $requiredPrefix);
    }

    private function hexToBinary($hash)
    {
        $binary = '';
        for ($i = 0; $i < strlen($hash); $i++) {
            $binary .= str_pad(base_convert(ord($hash[$i]), 10, 16), 4, '0', STR_PAD_LEFT);
        }
        return $binary;
    }


    public function findBlock(int $index, string $previousHash, DateTime $timestamp, string $data, int $difficulty): Block
    {
        $nonce = 0;

        while (true) {
            $hash = $this->calculateHash($index, $previousHash, $timestamp, $data, $difficulty, $nonce);
            if ($this->hashMatchesDifficulty($hash, $difficulty)) {
                return new Block($index, $data, $timestamp, $hash, $previousHash, $difficulty, $nonce);
            }
            $nonce++;
        }
    }

    private function calculateHash(int $index, string $previousHash, DateTime $timestamp, string $data, int $difficulty, int $nonce): string
    {
        $input = $index . $previousHash . $timestamp->getTimestamp() . $data . $difficulty . $nonce;

        return hash('sha256', $input);
    }

    public function getDifficulty(array $aBlockchain): int
    {
        $latestBlock = $aBlockchain[count($aBlockchain) - 1];

        if ($latestBlock['index'] % Config::DIFFICULTY_ADJUSTMENT_INTERVAL === 0 && $latestBlock['index'] !== 0) {
            return $this->getAdjustedDifficulty($latestBlock, $aBlockchain);
        } else {
            return $latestBlock['difficulty'];
        }
    }

    public function getAdjustedDifficulty(array $latestBlock, array $aBlockchain): int
    {
        $prevAdjustmentBlock = $aBlockchain[count($aBlockchain) - Config::DIFFICULTY_ADJUSTMENT_INTERVAL];
        $timeExpected = Config::BLOCK_GENERATION_INTERVAL * Config::DIFFICULTY_ADJUSTMENT_INTERVAL;
        $timeTaken = $latestBlock['timestamp'] - $prevAdjustmentBlock['timestamp'];

        if ($timeTaken < $timeExpected / 2) {
            return $prevAdjustmentBlock['difficulty'] + 1;
        } elseif ($timeTaken > $timeExpected * 2) {
            return $prevAdjustmentBlock['difficulty'] - 1;
        } else {
            return $prevAdjustmentBlock['difficulty'];
        }
    }

    public function isValidTimestamp(array $newBlock, array $previousBlock): bool
    {
        return ($previousBlock['timestamp'] - 60 < $newBlock['timestamp']) && ($newBlock['timestamp'] -  60 < $this->getCorrentTimestamp());
    }

    private function getCorrentTimestamp()
    {
        return time();
    }
}
