<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/9/17
 * Time: 7:13 PM
 */

namespace App\Jobs\Order;


use App\Components\UserOrder;
use App\Components\UserWallet;
use App\Contract\Job\Job;
use App\Models\Order;
use App\Models\Wallet;

/**
 * Class SetOrder
 * @package App\Jobs\Order
 */
class SetOrder implements Job
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
     * @var array
     */
    protected $walletDetail;

    /**
     * SetOrder constructor.
     * @param $userId
     * @param $request
     */
    public function __construct($userId, $request)
    {
        $this->userId = $userId;
        $this->request = $request;
    }


    /**
     * @return mixed
     */
    public function handle()
    {
        $orderId=$this->addToOrderList($this->request, (int)$this->userId);
        $orderId->getInsertedId();
        $this->request['order_id']=(string)$orderId->getInsertedId();
        $this->registerTradeInfo($this->request, (int)$this->userId);




        return $orderId;
    }

    private function registerTradeInfo($request, $userId)
    {
        return Wallet::insertTradeInfo(
            (new UserWallet())->setMarketId($request['market_id'])
                ->setUserId($userId)
                ->setWalletType($request['type'])
                ->setStopLess(array_key_exists('stop_less',$request) ? $request['stop_less'] : 0)
                ->setTakeProfit(array_key_exists('take_profit',$request) ? (int)$request['take_profit'] : 0 )
                ->setTotal(($request['order_type'] == 'sell') ? (int)$request['count'] * -1 : (int)$request['count'])
                 ->setOrderID($request['order_id'])
        );
    }

    private function addToOrderList($request, $userId)
    {
        return Order::add((new UserOrder())->setMarketId($request['market_id'])
            ->setUserId($userId)
            ->setWalletType($request['type'])
            ->setOrderType($request['order_type'])
            ->setCountOfUnit((int)$request['count'])
            ->setPrice($request['price']));
    }
}