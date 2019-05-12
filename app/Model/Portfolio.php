<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/25/17
 * Time: 12:46 AM
 */

namespace App\Model;


use App\Components\UserWallet;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;

class Portfolio extends Eloquent
{
    protected $connection ="mongodb";
    protected $collection = "portfolios";

    protected $gurded=['_id'];


    /**
     * @param UserWallet $userWallet
     * @return mixed
     */
    public static function updateUser(UserWallet $userWallet)
    {
        return self::updateOne(
            [
                'market_id' => $userWallet->getMarketId(),
                'user_id' => $userWallet->getUserId(),
                'wallet_type' => $userWallet->getWalletType(),
            ],
            [
                '$set' => [
                    'market_id' => $userWallet->getMarketId(),
                    'order_id'  =>$userWallet->getOrderId(),
                    'user_id' => $userWallet->getUserId(),
                    'created_at' => time(),
                    'date' => Carbon::createFromTimestamp(time())->toDateString(),
                    'wallet_type' => $userWallet->getWalletType(),
                    'stop_less' => $userWallet->getStopLess() ?? 0,
                    'take_profit' => $userWallet->getTakeProfit() ?? 0,
                ],
                '$inc' => [
                    'total' => $userWallet->getTotal(),
                ],

            ], ['upsert' => true], null);
    }

    public static function insertTradeInfo(UserWallet $userWallet){
        self::insertOne([
            'market_id' =>$userWallet->getMarketId(),
            'order_id' =>$userWallet->getOrderId(),
            'user_id' => $userWallet->getUserId(),
            'created_at' =>time(),
            'date'    => Carbon::now()->toDateString(),
            'wallet_type' => $userWallet->getWalletType(),
            'stop_less' =>$userWallet->getStopLess(),
            'take_profit' =>$userWallet->getTakeProfit(),
            'total' =>$userWallet->getTotal()
        ]);

    }

    public function market()
    {
        return $this->belongsTo(Market::class, 'market_id', '_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
