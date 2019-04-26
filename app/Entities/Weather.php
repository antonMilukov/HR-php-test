<?php

namespace App\Entities;

class Weather {

    public function __construct(array $input)
    {
        foreach ($input as $key => $val){
            if (property_exists(self::class, $key)){
                $this->$key = $val;
            }
        }
    }

    public $temp;
}