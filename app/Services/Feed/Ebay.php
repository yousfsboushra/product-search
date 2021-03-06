<?php

namespace App\Services\Feed;
use Illuminate\Support\Facades\Cache;

Class Ebay implements Feed{
    private $endpoint;
    private $appid;
    private $cacheTime;

    public function __construct($endpoint, $appid, $cacheTime){
        $this->endpoint = $endpoint;
        $this->appid = $appid;
        $this->cacheTime = $cacheTime;
    }

    public function getProducts($keywords, $minPrice, $maxPrice){
        $cacheKey = 'ebay-products-' . $minPrice . '-' . $maxPrice . '-' . str_replace(" ", "-", $keywords);
        $products = Cache::remember($cacheKey, $this->cacheTime, function () use ($keywords, $minPrice, $maxPrice){
            $json = $this->readFeed($keywords, $minPrice, $maxPrice);
            $products = $this->formatProducts($json);
            return $products;
        });
        if(empty($products)){
            Cache::forget($cacheKey);
        }
        return $products;
    }

    private function formatProducts($ebayJson){
        $products = array();
        $ebayProducts = json_decode($ebayJson);
        $count = (isset($ebayProducts->findItemsByKeywordsResponse[0]->searchResult))? $ebayProducts->findItemsByKeywordsResponse[0]->searchResult[0]->{"@count"} : 0;
        if($count > 0){
            $items = $ebayProducts->findItemsByKeywordsResponse[0]->searchResult[0]->item;
            foreach($items as $item){
                $itemId = $item->itemId[0] ?? null;
                if($itemId !== null && !isset($products[$itemId])){
                    $formattedItem = $this->convertEbayProductToCompadoProduct($item);
                    $products[$itemId] = $formattedItem;
                }
            }
        }
        return $products;
    }

    private function convertEbayProductToCompadoProduct($item){
        $product = array(
            'provider' => 'ebay',
            'item_id' => 'EBAY-' . $item->itemId[0],
            'click_out_link' => $item->viewItemURL[0] ?? null,
            'main_photo_url' => $this->extractPhotoUrl($item),
            'price' => $item->sellingStatus[0]->currentPrice[0]->{"__value__"} ?? null,
            'price_currency' => $item->sellingStatus[0]->currentPrice[0]->{"@currencyId"} ?? null,
            'shipping_price' => $item->shippingInfo[0]->shippingServiceCost[0]->{"__value__"} ?? null,
            'title' => $item->title[0] ?? null,
            'description' => $item->subtitle[0] ?? null,
            'valid_until' => $item->listingInfo[0]->endTime[0] ?? null,
            'brand' => null
        );
        return $product;
    }

    private function extractPhotoUrl($item){
        $photoUrl = null;
        if(isset($item->pictureURLSuperSize[0])){
            $photoUrl = $item->pictureURLSuperSize[0];
        }else if(isset($item->pictureURLLarge[0])){
            $photoUrl = $item->pictureURLLarge[0];
        }else if(isset($item->galleryURL[0])){
            $photoUrl = $item->galleryURL[0];
        }
        return $photoUrl;
    }

    private function readFeed($keywords, $minPrice, $maxPrice){
        $url = $this->endpoint . '?' . $this->getUrlQueryParams($keywords, $minPrice, $maxPrice);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    private function getUrlQueryParams($keywords, $minPrice, $maxPrice){
        $urlQueryParams = array(
            'OPERATION-NAME' => 'findItemsByKeywords',
            'SECURITY-APPNAME' => $this->appid,
            'RESPONSE-DATA-FORMAT' => 'JSON',
            'paginationInput.entriesPerPage' => 100,
            'paginationInput.pageNumber' => 1,
            'outputSelector' => array(
                'PictureURLLarge'
            ),
            'keywords' => $keywords,
            'itemFilter' => array(
                array(
                    'name' => 'MinPrice',
                    'value' => $minPrice
                ),
                array(
                    'name' => 'MaxPrice',
                    'value' => $maxPrice
                )
            )
        );

        $urlParams = $this->convertArrayToUrlParams($urlQueryParams);
        return implode('&', $urlParams);
    }


    private function convertArrayToUrlParams($urlQueryParams, $prefix = ''){
        $urlQueryArray = array();
        foreach($urlQueryParams as $key => $value){
            $name = $this->getParameterName($key, $prefix);
            if(is_array($value)){
                $subUrlQueryArray = $this->convertArrayToUrlParams($value, $name);
                $urlQueryArray = array_merge($urlQueryArray, $subUrlQueryArray);
            }else{
                $urlQueryArray[] = $name . '=' . $value;
            }
        }
        return $urlQueryArray;
    }

    private function getParameterName($key, $prefix){
        $name = '';
        if($prefix !== ''){
            $name .= $prefix;
            if(is_numeric($key)){
                $name .= '(' . $key . ')';
            }else{
                $name .= '.' . $key;
            }
        }else{
            $name = $key;
        }
        return $name;
    }
}