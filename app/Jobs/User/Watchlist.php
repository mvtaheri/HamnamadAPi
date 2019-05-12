<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 4/4/2018
 * Time: 11:19 AM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Models\People;

/**
 * Class Watchlist
 * @package App\Jobs\User
 */
class Watchlist implements Job
{
    /**
     * @var
     */
    protected $userId;

    /**
     * @var
     */
    protected $request;

    /**
     * @var
     */
    protected $type;

    /**
     * Watchlist constructor.
     * @param $userId
     * @param $request
     * @param $type
     */
    public function __construct($userId, $request, $type)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        switch ($this->type) {
            case "remove":
                $this->removeWatchlist();
                break;

            case "update":
                $user = People::findByUserId((int)$this->userId);
                $this->checkExistWatchlist($watchlist = $user->watchlist->{$this->request['category']});

                $update = People::updateOne(
                    ['user_id' => (int)$this->userId],
                    [
                        '$unset' => [
                            "watchlist.{$this->request['category']}" => true
                        ],
                        '$set'   => [
                            "watchlist.{$this->request['new_name']}" => $watchlist
                        ]
                    ],
                    [],
                    '$unset'
                );
                break;
        }

        return $update;
    }

    /**
     * @return mixed
     */
    private function removeWatchlist()
    {
        return People::updateOne(
            ['user_id' => (int)$this->userId],
            [
                '$unset' => [
                    "watchlist.{$this->request['category']}" => true
                ]
            ],
            [],
            '$unset'
        );
    }

    /**
     * @param $watchlist
     */
    private function checkExistWatchlist($watchlist)
    {
        if (is_null($watchlist)) {
            die(respond()->fail('watchlist not exist'));
        }
    }
}