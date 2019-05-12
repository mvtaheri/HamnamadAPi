<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 10/1/17
 * Time: 3:21 PM
 */

namespace App\Traits\Order;


use App\Models\Market;
use App\Models\People;

/**
 * Trait UserWallet
 * @package App\Traits\Order
 */
trait UserWallet
{

    /**
     * @param $walletType
     * @param $userId
     * @return int
     */
    protected function getUserWalletDetailByWalletType($walletType, $userId) :int
    {
        $user = People::findOne(['user_id' => $userId]);

        return $user->wallet->{$walletType}->available;
    }

    /**
     * @param $marketId
     * @param null $price
     * @return int
     */
    protected function getUnitPriceOfMarket($marketId , $price = null) :int
    {
        $market = Market::findById($marketId);

        return $price ?? (int)$market->sell->price;
    }

    /**
     * @param int $price
     * @param int $count
     * @return int
     */
    protected function CalculateCostOfAOrder(int $price, int $count) :int
    {
        return $price * $count;
    }
}