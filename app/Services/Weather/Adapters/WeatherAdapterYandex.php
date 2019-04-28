<?php

namespace App\Services\Weather\Adapters;
use App\Entities\Weather;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 * Adapter for yandex api - weather
 * Class WeatherAdapterYandex
 * @package App\Services\Weather\Adapters
 */
class WeatherAdapterYandex implements WeatherInterface {

    /**
     * @return Weather
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWeatherData()
    {
        $data = $this->getData();
        $input = [
            'temp' => isset($data['fact']['temp']) ? $data['fact']['temp'] : 'Ошибка получения сведений о температуре'
        ];
        return new Weather($input);
    }

    /**
     * Method as controller between cache and request to api
     * - return new or cached data from api
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getData()
    {
        $endpoint = $cacheKey = "https://api.weather.yandex.ru/v1/informers/?lat=53.270955&lon=34.360938";
        $content = null;
        if (Cache::has($endpoint)) {
            $content = Cache::get($cacheKey);
        } else {
            $content = $this->getRequestData($endpoint);
            Cache::put($cacheKey, $content, now()->addMinutes(30));
        }

        $r = null;
        if (is_string($content)){
            $r = @json_decode($content, true);
        }

        return $r;
    }

    /**
     * Method for request data from api
     * @param $endpoint
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getRequestData($endpoint)
    {
        $client = new Client();
        $response = $client->request('GET', $endpoint,
            [
                'headers' => [
                    'X-Yandex-API-Key' => env('X_YANDEX_API_KEY')
                ],
                'http_errors' => false,
            ]
        );

        $statusCode = $response->getStatusCode();

        $r = null;
        if ($statusCode == 200){
            $r = $response->getBody()->getContents();
        }
        return $r;
    }
}