<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HearthstoneController extends controller
{
    public function sendData(){
        if(!$this->testCache()) {
            $this->getData();
            $this->getMin(Cache::get('totalPages'));
        }

        return view('hearthstoneView')->with(['max' => Cache::get('maxRating'), 'min' => Cache::get('minRating')]);
    }
    private function getData() {
        $URL = "https://hearthstone.blizzard.com/en-us/api/community/leaderboardsData?region=US&leaderboardId=battlegrounds&page=1";
        $response = Http::get($URL);
        $data = json_decode($response, true);
        Cache::put('totalPages', $data['leaderboard']['pagination']['totalPages'], 600);
        Cache::put('totalSize', $data['leaderboard']['pagination']['totalSize'], 600);
        Cache::put('maxRating', $data['leaderboard']['rows'][0]['rating'], 600);

    }

    private function testCache() : bool {
        $keys = [
            'totalPages', 'totalSize', 'maxRating', 'minRating'
        ];
        foreach ($keys as $key) {
            if (!Cache::has($key)) {
                return false;
            }
        }
        return true;
    }
    private function getMin($i) {
        $URL = "https://hearthstone.blizzard.com/en-us/api/community/leaderboardsData?region=US&leaderboardId=battlegrounds&page={$i}";
        $response = Http::get($URL);
        $data = json_decode($response, true);
        Cache::put('minRating', $data['leaderboard']['rows'][0]['rating'], 600);
    }
}
