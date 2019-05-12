<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 3/26/2019
 * Time: 5:52 PM
 */

namespace App\Jobs\Market;

use App\Models\People;
use App\Contract\job\Job;
use MongoDB\BSON\ObjectId;


class RemoveWatchlistItem implements Job
{

    protected $userId;
    protected $request;
    protected $type; // User Or Market

    public function __construct($userId, $request, $type)
    {
        $this->request = $request;
        $this->type = $type;
        $this->userId = $userId;
    }

    public function handle()
    {
        $people = People::findByUserId((int)$this->userId);
        if (!is_null($people->watchlist)) {
            foreach ($people->watchlist as $key => $watchlist) {
                switch ($this->type) {
                    case 'user':
                        foreach ($watchlist as $item => $list) {
                            if ($item == 'user') {
                                foreach ($list as $user)
                                    People::updateOne(
                                        ['user_id' => (int)$this->userId],
                                        [
                                            '$pull' => [
                                                "watchlist.{$key}.{$item}" => ['user_id' => (int)$this->request['item_id']]
                                            ]
                                        ],
                                        ['multi' => true],
                                        '$pull'
                                    );
                            }

                        }
                        break;
                    case
                    'market':
                        foreach ($watchlist as $item => $list) {
                            if ($item == 'markets') {
                                foreach ($list as $market) {
                                          People::updateOne(
                                              ['user_id'=> (int)$this->userId],
                                            [
                                                '$pull' => [
                                                    "watchlist.{$key}.{$item}" => ['market_id' => new ObjectID($this->request['item_id'])]
                                                ]
                                            ],
                                            ['multi' => true],
                                            '$pull'
                                        );

                                }
                            }
                        }

                        break;
                }
            }
        }

    }


}