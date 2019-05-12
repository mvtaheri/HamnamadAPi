<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/26/17
 * Time: 12:12 AM
 */

namespace App\Jobs\Order\Sell;


use App\Contract\Job\Job;
use App\Jobs\Order\SetOrder;
use App\Models\Category;
use App\Models\Efficiency;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\People;
use App\Models\Wallet;
use App\Traits\Order\UserWallet;
use Carbon\Carbon;
use MongoDB\BSON\ObjectID;

/**
 * Class SetSellOrder
 * @package App\Jobs\Order
 */
class SetSellOrder implements Job
{
    use UserWallet;

    /**
     * @var array
     */
    protected $request;
    /**
     * @var int
     */
    protected $userId;

    /**
     * SetSellOrder constructor.
     * @param $request
     * @param $userId
     */
    public function __construct($request, $userId)
    {
        $this->request = $request;
        $this->userId = (int)$userId;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $this->checkAvailableOnMyWallet($this->request, $this->userId);
        $this->request['order_type'] = 'sell';
        $this->setSellOrderForAMarketByAUser();
        $this->SetEfficiencyIfBuyOrderExist();

        return People::incrementAvailableWallet(
            $this->userId,
            $this->request['type'],
            $this->CalculateCostOfAOrder((int)$this->request['price'], (int)$this->request['count'])
        );
    }


    private function checkAvailableOnMyWallet($request, $userId)
    {
        $wallet = Order::find([
            'user_id'     => $userId,
            'market_id'   => new ObjectID($request['market_id']),
            'wallet_type' => $request['type'],
            'type'        => 'buy'
        ]);
        $count = array_sum(array_column($wallet, 'count_unit'));


        if (!$wallet)
            die(respond()->fail('user has not this market id'));

        if ($count < (int)$request['count'])
            die(respond()->fail('user wallet available not enough'));

        return $wallet;
    }


    private function setSellOrderForAMarketByAUser()
    {
        $setOrder = dispatch(new SetOrder($this->userId, $this->request));
        if (!is_object($setOrder))
            die(respond('')->fail('Set Sell Order Not Complete'));

        return true;
    }

    private function SetEfficiencyIfBuyOrderExist()
    {
        if ($buyOrder = Order::findOne([
            'market_id'   => new ObjectID($this->request['market_id']),
            'user_id'     => $this->userId,
            'wallet_type' => $this->request['type'],
            'type'        => 'buy',
            'created_at'  => ['$lt' => time()]
        ], ['sort' => ['created_at' => -1]])) {
            $efficiency = (((int)$this->request['price'] - $buyOrder->price) / $buyOrder->price);
            $category = Category::getCategoryOfMarket($this->request['market_id']);

            return Efficiency::insertOne([
                'market_id'      => new ObjectID($this->request['market_id']),
                'user_id'        => $this->userId,
                'category_id'    =>(isset($category[0]->id))? $category[0]->id :'',
                'value'          => $efficiency,
                'date'           => Carbon::createFromTimestamp(time())->toDateString(),
                'buy_created_at' => $buyOrder->created_at,
                'created_at'     => time()
            ]);
        }
    }


}