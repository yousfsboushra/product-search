<?php

namespace App\Services\Feed;

interface Feed{
    public function getProducts($keywords, $minPrice, $maxPrice);
}