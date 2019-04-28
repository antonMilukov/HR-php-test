<?php

namespace App\Services\Weather\Adapters;
use App\Entities\Weather;

/**
 * Interface for weather adapters
 * Interface WeatherInterface
 * @package App\Services\Weather\Adapters
 */
interface WeatherInterface {

    /**
     * Return constant data
     * @return Weather
     */
    public function getWeatherData();
}