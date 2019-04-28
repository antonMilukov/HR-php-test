<?php

namespace App\Entities;

/**
 * Weather model entity

 * Class Weather
 * @package App\Entities
 */
class Weather {

    public function __construct(array $input)
    {
        foreach ($input as $key => $val){
            if (property_exists(self::class, $key)){
                $this->$key = $val;
            }
        }
    }

    /**
     * Temperature in celsius
     * @var
     */
    public $temp;
}