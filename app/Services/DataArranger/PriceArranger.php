<?php

namespace App\Services\DataArranger;

Class PriceArranger implements Arranger{

    public function sort($items, $dir){
        $yes = 1;
        if($dir === 'asc'){
            $yes = -1;
        }
        $no = -1 * $yes;
        usort($items, function($item1, $item2) use ($yes, $no){
            return ($item1['price'] < $item2['price']) ? $yes : $no;
        });
        return $items;
    }
}