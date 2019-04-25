<?php

namespace App\Services\Weather\Adapters;
use App\Entities\Weather;

interface WeatherInterface {

    /**
     * @return Weather
     */
    public function getWeatherData();
}