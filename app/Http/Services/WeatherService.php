<?php

namespace App\Http\Services;

use VK\Client\VKApiClient;
use Illuminate\Http\Request;
use Storage;
use Curl;

class WeatherService
{
    public function getWeatherByCity($request, $city)
    {
        $array = [
          'q' => $city,
          'units' => 'metric',
          'lang' => env('APP_LOCALE'),
          'appid' => env('OPENWEATHERMAP_API_ACCESS_TOKEN')
        ];

        return $this->sendRequest($request, $array);
    }

    public function getWeatherByCoord($request, $latitude, $longitude)
    {
        $array = [
          'lat' => $latitude,
          'lon' => $longitude,
          'units' => 'metric',
          'lang' => env('APP_LOCALE'),
          'appid' => env('OPENWEATHERMAP_API_ACCESS_TOKEN')
        ];

        return $this->sendRequest($request, $array);
    }

    public function sendRequest($request, $array)
    {
      $response = Curl::to(env('OPENWEATHERMAP_API_URL') . env('OPENWEATHERMAP_API_VERSION') . env('OPENWEATHERMAP_API_ENDPOINT'))
        ->withData($array)
        ->asJsonResponse()
        ->get();

      return $this->outputWeather($request, $response);
    }

    public function outputWeather($request, $response)
    {
      if (!empty($response->weather[0]->description) && !empty($response->weather[0]->icon) && !empty($response->main->temp) && !empty($response->main->feels_like) && !empty($response->main->pressure) && !empty($response->main->humidity) && !empty($response->wind->speed) && !empty($response->dt) && !empty($response->sys->country) && !empty($response->sys->sunrise) && !empty($response->sys->sunset) && !empty($response->name)) {
        $description = $response->weather[0]->description;

        $icons = [
          '01d' => '&#9728;',   '01n' => '&#127769;',
          '02d' => '&#127781;', '02n' => '&#127769; &#9729;',
          '03d' => '&#9729;',   '03n' => '&#9729;',
          '04d' => '&#9729;',   '04n' => '&#9729;',
          '09d' => '&#127783;', '09n' => '&#127783;',
          '10d' => '&#127782;', '10n' => '&#127783;',
          '11d' => '&#127785;', '11n' => '&#127785;',
          '13d' => '&#127784;', '13n' => '&#127784;',
          '50d' => '&#127787;', '50n' => '&#127787;'
        ];

        $icon = $icons[$response->weather[0]->icon];
        $temp = (int)round($response->main->temp, 0) . ' &#176;C';
        $feels_like = (int)round($response->main->feels_like, 0) . ' &#176;C';
        $pressure = (int)round($response->main->pressure * 0.750063, 0) . ' ' . trans('mm Hg');
        $humidity = $response->main->humidity . '%';

        if (!empty($response->visibility)) {
          if ($response->visibility >= 1000) {
            $visibility = $response->visibility / 1000 . ' ' . trans('km');
          } else {
            $visibility = $response->visibility . ' ' . trans('m');
          }
        } else {
          $visibility = trans('unavailable');
        }

        $speed = (int)round($response->wind->speed, 0) . ' ' . trans('m/sec');

        if (!empty($response->wind->deg)) {
          switch ($response->wind->deg) {
            case $response->wind->deg >= 0 && $response->wind->deg <= 22:
              $deg = trans('northern');
              break;
            case $response->wind->deg > 22 && $response->wind->deg <= 67:
              $deg = trans('north east');
              break;
            case $response->wind->deg > 67 && $response->wind->deg <= 112:
              $deg = trans('eastern');
              break;
            case $response->wind->deg > 112 && $response->wind->deg <= 157:
              $deg = trans('south-eastern');
              break;
            case $response->wind->deg > 157 && $response->wind->deg <= 202:
              $deg = trans('south');
              break;
            case $response->wind->deg > 202 && $response->wind->deg <= 247:
              $deg = trans('south west');
              break;
            case $response->wind->deg > 247 && $response->wind->deg <= 292:
              $deg = trans('western');
              break;
            case $response->wind->deg > 292 && $response->wind->deg <= 337:
              $deg = trans('northwest');
              break;
            case $response->wind->deg > 337 && $response->wind->deg <= 360:
              $deg = trans('northern');
              break;
            default:
              $deg = trans('unavailable');
          }
        } else {
          $deg = trans('unavailable');
        }

        $dt = date('d.m.Y H:i', $response->dt);
        $country = $response->sys->country;
        $sunrise = date('H:i', $response->sys->sunrise);
        $sunset = date('H:i', $response->sys->sunset);
        $city = $response->name;

        $output = [
          'description' => $description,
          'icon' => $icon,
          'temp' => $temp,
          'feels_like' => $feels_like,
          'pressure' => $pressure,
          'humidity' => $humidity,
          'visibility' => $visibility,
          'speed' => $speed,
          'deg' => $deg,
          'dt' => $dt,
          'country' => $country,
          'sunrise' => $sunrise,
          'sunset' => $sunset,
          'city' => $city,
        ];
        $message = trans('messages.weather', $output);
      } else {
        $message = trans('An error occurred when searching for weather in the specified place... &#128530;');
      }

      return $this->sendMessage($request, $message);
    }

    public function sendMessage($request, $message)
    {
      $request = $request->all();

      $data = [
        'peer_id' => $request['object']['message']['peer_id'],
        'random_id' => hrtime(true),
        'message' => $message,
      ];

      $vk = new VKApiClient(env('VK_API_VERSION'));
      $vk->messages()->send(env('VK_API_ACCESS_TOKEN'), $data);

      return response('ok', 200);
    }
}
