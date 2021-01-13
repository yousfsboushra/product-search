<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\Feed\Feed;

class ProductsController extends BaseController
{
    public function search(Request $request){
        $params = $this->validateParameters($request);
        if(!is_array($params)){
            return $params;
        }

        $products = array();
        $feeds = app()->tagged(Feed::class);
        foreach ($feeds as $feed) {
            $feedJson = $feed->read();
            $feedProducts = json_decode($feedJson);
            $products = array_merge($products, $feedProducts);
        }

        return response()->json(["products" => $products]);
    }

    private function validateParameters($request){
        $input = $request->all();
        if(empty($input)){
            return response()->json(["error" => "Missing parameters"], 400);
        }

        $keywords = '';
        if(isset($input['keywords'])){
            $keywords = filter_var($input['keywords'], FILTER_SANITIZE_STRING);
            if(empty($keywords)){
                return response()->json(["error" => "Keywords parameter can not be empty"], 400);
            }
        }

        $maxPrice = 999999999;
        if(isset($input['price_max'])){
            if(!is_numeric($input['price_max']) || $input['price_max'] <= 0){
                return response()->json(["error" => "Max price must be numeric and greater than 0"], 400);
            }
            $maxPrice = $input['price_max'];
        }

        $minPrice = 0;
        if(isset($input['price_min'])){
            if(!is_numeric($input['price_min']) || $input['price_min'] < 0){
                return response()->json(["error" => "Min price must be numeric and greater than or equal to 0"], 400);
            }
            $minPrice = $input['price_min'];
        }

        if($minPrice >= $maxPrice){
            return response()->json(["error" => "Max price Must be greater than min price"], 400);
        }

        $sorting = "default";
        if(!empty($input['sorting'])){
            if(!in_array($input['sorting'], array('default', 'price_asc'))){
                return response()->json(["error" => "Sorting allowed values are (default and price_asc)"], 400);
            }
        }

        return array(
            'keywords' => $keywords,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sorting' => $sorting
        );
    }
}
