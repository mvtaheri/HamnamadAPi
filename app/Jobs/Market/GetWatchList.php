<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/28/2018
 * Time: 9:21 AM
 */

namespace App\Jobs\Market;


use App\Contract\Job\Job;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Market;
use App\Models\People;
use App\Models\User;
use App\Transformers\Market\LatestTransformer;
use App\Transformers\User\UserTransformer;

/**
 * Class GetWatchList
 * @package App\Jobs\Market
 */
class GetWatchList implements Job
{
    /**
     * @var
     */
    protected $userId;

    /**
     * @var
     */
    protected $type;

    /**
     * GetWatchList constructor.
     * @param $userId
     * @param $type
     */
    public function __construct($userId, $type)
    {
        $this->userId = $userId;
        $this->type = $type;
    }


    /**
     * @return mixed
     */
    public function handle()
    {

        return $this->getUserWatchlist();

    }

    /**
     * @return array|mixed
     */
    private function getUserWatchlist()
    {

        $people = People::findByUserId($this->userId);
        $allElements = [];

        if (!is_null($people->watchlist)) {
            foreach ($people->watchlist as $key => $watchlist) {

                $currentMarket = [];

                foreach ($watchlist as $k => $list) {

                    switch ($k) {
                        case "user":
                            foreach ($list as $user) {
                                $watchlistUser = User::findById($user->user_id);
                                $watchlistUser->order = $user->order;
                                $allElements[$key]['user'][] = UserTransformer::transform($watchlistUser);
                            }
                            break;
                        case "markets":
                            foreach ($list as $market) {
                                $watchlistMarket = Market::findById($market->market_id);
                                $watchlistMarket->order = $market->order;
                                $currentMarket[] = $watchlistMarket;

                            }
                            break;
                    }
                }

                $allElements[$key]['markets'] = LatestTransformer::transformArray(
                    $this->addChangePriceAndSentimentToMarket($currentMarket)
                );
            }
        }

        return $allElements;
    }

    /**
     * @param $markets
     * @return mixed
     */
    private function addChangePriceAndSentimentToMarket($markets)
    {
        foreach ($markets as $key => $market) {
            $markets[$key]->category = Category::getCategoryOfMarket((string)$market->_id);
            $markets[$key]->alert = Alert::has($this->userId, (string)$market->_id);
            $markets[$key]->change = ($market->sell->price - $market->sell->last_price) * 0.01;
            $markets[$key]->sentiment = Market::calculateSentimentToday($market->_id);
        }

        return $markets;
    }
}