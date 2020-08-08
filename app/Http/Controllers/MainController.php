<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\WeatherService;

class MainController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
          'type' => 'required|string|in:confirmation,message_new',
          'group_id' => 'required|in:' . env('VK_GROUP_ID'),
          'secret' => 'required|in:' . env('VK_SECRET_KEY'),
        ]);

        switch ($request->type) {
          case 'confirmation':
            return response(env('VK_API_CONFIRMATION_TOKEN'), 200);
            break;
          case 'message_new':
            if (!empty($request->object['message']['text'])) {
              if (($request->object['message']['from_id'] === $request->object['message']['peer_id'] && stripos(mb_strtolower($request->object['message']['text']), trans('weather ')) === 0) || ($request->object['message']['from_id'] !== $request->object['message']['peer_id'] && stripos(mb_strtolower(@explode(' ', $request->object['message']['text'])[1]), trim(trans('weather '))) === 0 && stripos(mb_strtolower(@explode(' ', $request->object['message']['text'])[0]), '[club' . env('VK_GROUP_ID')) === 0)) {
                $city = trim(preg_replace([
                  '/' . trim(trans('weather ')) . '/u',
                  '/\[[a-zA-Z0-9@\|]{1,}\]/u'
                ], '', mb_strtolower($request->object['message']['text'])));

                $weather = new WeatherService;
                return $weather->getWeatherByCity($request, $city);
              }
            } elseif (!empty($request->object['message']['geo'])) {
              if (($request->object['message']['from_id'] === $request->object['message']['peer_id']) && (!empty($request->object['message']['geo']['coordinates']['latitude']) && !empty($request->object['message']['geo']['coordinates']['longitude']))) {
                $latitude = $request->object['message']['geo']['coordinates']['latitude'];
                $longitude = $request->object['message']['geo']['coordinates']['longitude'];

                $weather = new WeatherService;
                return $weather->getWeatherByCoord($request, $latitude, $longitude);
              }
            }
            break;
        }
    }
}
