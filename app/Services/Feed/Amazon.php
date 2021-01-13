<?php

namespace App\Services\Feed;

Class Amazon implements Feed{
    public function __construct(){
    }

    public function read(){
        return '[{"id": 2, "name": "Mac book pro", "provider": "amazon"}]';
    }
}