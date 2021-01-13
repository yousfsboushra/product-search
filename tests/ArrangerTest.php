<?php

use App\Services\DataArranger\PriceArranger;

class ArrangerTest extends TestCase{

    public function testAscPriceSort(){
        $priceArranger = new PriceArranger();
        $items = array(array('price' => 100), array('price' => 50), array('price' => 75));
        $sorteditems = $priceArranger->sort($items, 'asc');
        $this->assertEquals([['price'=>50],['price'=>75],['price'=>100]], $sorteditems);
    }

    public function testDescPriceSort(){
        $priceArranger = new PriceArranger();
        $items = array(array('price' => 100), array('price' => 50), array('price' => 75));
        $sorteditems = $priceArranger->sort($items, 'desc');
        $this->assertEquals([['price'=>100],['price'=>75],['price'=>50]], $sorteditems);
    }
}