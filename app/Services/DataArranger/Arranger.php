<?php
namespace App\Services\DataArranger;

interface Arranger{
    public function sort($items, $dir);
}