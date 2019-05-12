<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/20/2018
 * Time: 9:01 AM
 */

namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * Class Efficiency
 * @package App\Models
 */
class Efficiency extends Eloquent
{
    /**
     * @var string
     */

    protected $collection ='efficiency';

    protected $connection ='mongodb';

    /**
     * @param $userId
     * @return mixed
     */
    public static function byCategories($userId)
    {
        return self::aggregate([
            [
                '$match' => [
                    'user_id' => $userId
                ]
            ],
            [
                '$group' =>
                    ["_id" => '$category_id', 'value' => ['$push' => '$value']]
            ]
        ])->toArray();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public static function byMarket($userId)
    {
        return self::aggregate([
            [
                '$match' => [
                    'user_id' => $userId
                ]
            ],
            [
                '$group' =>
                    ["_id" => '$market_id', 'value' => ['$push' => '$value']]
            ]
        ])->toArray();
    }

}
