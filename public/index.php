<?php
require __DIR__ . '/../vendor/autoload.php';

use Ledgerchain\Blockchain\Chain;

$chain = new Chain();

$chain->generateGenesisBlock();

for($i = 0; $i < 10; $i++) {
    $chain->generateNextBlock('Block ' . $i);
}

try {
    dd($chain->chain);
} catch (Exception $e) {
    dd($e->getMessage());
}