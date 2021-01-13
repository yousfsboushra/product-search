<?php

namespace App\Services\Feed;

Class Ebay implements Feed{
    private $endpoint;
    private $appid;

    public function __construct($endpoint, $appid){
        $this->endpoint = $endpoint;
        $this->appid = $appid;
    }

    public function read(){
        return '[{"id": 1, "name": "Mac mini", "provider": "ebay"}]';
    }
}