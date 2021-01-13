<?php

namespace App\Services\Feed;

Class Amazon implements Feed{
    public function __construct(){
    }

    public function getProducts($keywords, $minPrice, $maxPrice){
        return array();
    }
}