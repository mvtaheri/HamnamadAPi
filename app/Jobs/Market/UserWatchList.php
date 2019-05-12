<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/15/17
 * Time: 1:37 PM
 */

namespace App\Jobs\Market;


use App\Contract\Job\Job;
use App\Models\Log;
use App\Models\Market;
use App\Models\People;
use App\Models\User;
use MongoDB\BSON\ObjectID;

/**
 * Class AddToUserWatchList
 * @package App\Jobs\Market
 */
class UserWatchList implements Job
{

    /**
     * @var int
     */
    protected $userId;
    /**
     * @var array
     */
    protected $request;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var
     */
    protected $watchlistType;

    /**
     * UserWatchList constructor.
     * @param $userId
     * @param $request
     * @param $type
     * @param $watchlistType
     */
    public function __construct($userId, $request, $type, $watchlistType)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->type = $type;
        $this->watchlistType = $watchlistType;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $type = ($this->type == 'add') ? '$addToSet' : '$pull';
        if ($this->watchlistType == 'user') {
            $field = 'user_id';
            if (!$user = User::findById($id = (int)$this->request['user_id'])) {
                die(respond()->fail('user not exist'));
            }
        } else {
            $field = 'market_id';
            if (!$market = Market::findById($id = new ObjectID($this->request['market_id']))) {
                die(respond()->fail('market not exist'));
            }
        }

        $people = People::findByUserId($this->userId);
        $watchlist = [];
        if (isset($people->watchlist->{$this->request['category']}->{$this->watchlistType}))
            $watchlist = (array)$people->watchlist->{$this->request['category']}->{$this->watchlistType};
        $orders = array_column($watchlist, 'order');

        $order = (sizeof($orders) > 0) ? (max($orders)) + 1 : 1;
        $query = [$field => $id];

        if ($this->type == 'add') {
            $query = [$field => $id, 'order' => $order ?? 1];
        }
        return People::updateOne(
            ['user_id' => (int)$this->userId],
            [
                $type => [
                    "watchlist.{$this->request['category']}.{$this->watchlistType}" => $query
                ]
            ],
            ['upsert' => true],
            $type
        );
    }
}