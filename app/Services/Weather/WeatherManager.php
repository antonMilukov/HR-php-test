<?php
namespace App\Services\Weather;

use App\Entities\Weather;
use App\Services\Weather\Adapters\WeatherAdapterYandex;
class WeatherManager {

    protected $service;

    public function __construct(WeatherInterface $service = null)
    {
        $this->setService($service);
    }

    public function setService(WeatherInterface $service = null)
    {
        if (is_null($service)){
            $service = new WeatherAdapterYandex();
        }
        $this->service = $service;
    }

    /**
     * @return Weather
     */
    public function getWeatherData()
    {
        return $this->service->getWeatherData();
    }

}
