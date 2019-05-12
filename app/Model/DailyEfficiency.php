<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/25/2018
 * Time: 6:59 AM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;

/**
 * Class DailyEfficiency
 * @package App\Models
 */
class DailyEfficiency extends Eloquent
{
    /**
     * @var string
     */
    protected $collection ="daily_efficiency";

    protected $connection ="mongodb";

    /**
     * @param $userId
     * @param $walletType
     * @param $value
     */
    public static function add($userId, $walletType, $value)
    {
        self::create([
            'user_id' => (int)$userId,
            'wallet_type' => $walletType,
            'value' => $value,
            'date' => [
                "year" => Carbon::now()->year,
                "month" => Carbon::now()->month,
                "day" => Carbon::now()->day
            ],
            'created_at' => time()
        ]);
    }

    /**
     *
     */
    public static function removeDublicateEntityOnToday($user_id ){
        self::deleteOne(
            ['user_id' =>$user_id,
              'date.year' => Carbon::now()->year,
              'date.month' => Carbon::now()->month ,
              'date.day'  => Carbon::now()->day
            ]
        );
    }

    /**
     * @param $userId
     * @param $walletType
     * @param $timestamp
     * @return mixed
     */
    public static function getByDateLimited($userId, $walletType, $timestamp)
    {
        return self::find([
                'user_id' => $userId,
                'wallet_type' => $walletType,
                'created_at' => ['$gte' => $timestamp],
                'value' => ['$gt' => 0]
            ]
        );
    }
}
