<?php
namespace App\Services\Weather;

use App\Entities\Weather;
use App\Services\Weather\Adapters\WeatherAdapterYandex;
class WeatherManager {

    protected $service;

    /**
     *
     * WeatherManager constructor.
     * @param WeatherInterface|null $service
     */
    public function __construct(WeatherInterface $service = null)
    {
        $this->setService($service);
    }

    /**
     * Set adapter
     * @param WeatherInterface|null $service
     */
    public function setService(WeatherInterface $service = null)
    {
        if (is_null($service)){
            $service = new WeatherAdapterYandex();
        }
        $this->service = $service;
    }

    /**
     * Return response from adapter
     * @return Weather
     */
    public function getWeatherData()
    {
        return $this->service->getWeatherData();
    }

}
