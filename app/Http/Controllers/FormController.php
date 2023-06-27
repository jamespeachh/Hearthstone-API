<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FormController extends Controller
{
    public function submit(Request $request){
        $sliderTest = $request->input('sliderTest');

        //Find the percentile the player is in
        $playerPercFinal = $this->findPercentiles($sliderTest);

        return redirect('/FinalMessage')->with([
            'response'=>$playerPercFinal,
            'sliderTest'=>$sliderTest,
        ]);
    }

    public function index(){
        $sliderTest = session('sliderTest');
        $response = session('response', 'Error, no response');

        return view('FinalMessage', ['sliderTest'=>$sliderTest, 'response'=>$response]);
    }
    // called from submit or HearthstoneCommand
    public function findPercentiles($sliderValue) : string {
        if($this->testCache()){
            $this->httpDataCall(1,'1'); // getting the 'rating' and 'totalPages'

            $percentilesArr = $this->percTest(); // dividing up the total pages to get an index of pages needed
            $this->ratingGet($percentilesArr); // uses the index to get the ratings of each pages
        }
        return $this->findPlayerPercentile($sliderValue); //submit()
    }
    //gets the rating for each page
    private function ratingGet($index) {
        $jArr = array('1', '25', '50', '75');
        $j=0;
        foreach ($index as $i) {
            error_log("ratingGet " . $jArr[$j] . " " . $i);
            $this->httpDataCall($i, $jArr[$j]);
            $j++;
        }
    }
//   ('rating' and 'totalPages')
    private function httpDataCall($index,$key) {
        $URL = "https://hearthstone.blizzard.com/en-us/api/community/leaderboardsData?region=US&leaderboardId=battlegrounds&page={$index}";
        $response = Http::get($URL);
        $data = $response->json();
        Cache::put('rating'.$key, $data['leaderboard']['rows'][0]['rating'], 600);
        Cache::put('totalPages', $data['leaderboard']['pagination']['totalPages'], 600);
    }
//gets a string of the response.
    private function findPlayerPercentile($sliderValue) : string
    {
        $numList = [
            '1' => Cache::get('rating1'),
            '25'=> Cache::get('rating25'),
            '50' => Cache::get('rating50'),
            '75' => Cache::get('rating75')
        ];
        $OR = $this->ordinalRank($sliderValue, $numList);
        $temp = $this->findP($OR);

        return 'WOW! You are in the top ' . (100-$temp) . '% of players!';
    }
    private function ordinalRank($userInput, $numList): int
    {
        asort($numList);
        $i = 0;
        foreach ($numList as $key => $value) {
            if ($userInput <= $value) {
                return $i;
            }
            $i++;
        }
        return $i;
    }

    private function findP($ordinalRank) : int
    {
        if($ordinalRank == 0)
            $val=1;
        else
            $val = $ordinalRank*25;
        return $val;
    }


    //divides total pages into an index of pages needed
    private function percTest() : array {
        $totalPages=Cache::get('totalPages');

        $p1 = 1;
        $p25 = ceil($totalPages/(4));
        $p50 = ceil($p25*2);
        $p75 = ceil($p25 * 3);
        return ['1'=>$p1,'25'=>$p25,'50'=>$p50,'75'=>$p75];
    }
    private function testCache() : bool {
        $keys = ['rating1', 'rating25', 'rating50', 'rating75', 'totalPages'];
        foreach ($keys as $key) {
            if (!Cache::has($key)) {
                return true;
            }
        }
        return false;
    }
}
