<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/16/17
 * Time: 1:09 AM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Models\Market;
use App\Models\Order;
use App\Models\People;
use App\Transformers\User\UserWalletTransformer;

/**
 * Class UserWallet
 * @package App\Jobs\User
 */
class UserWallet implements Job
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
     * UserWallet constructor.
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
        $type = $this->request['type'];
        $marketDeferencePrice=[];

        if ($type == "real" || $type == "virtual") {
            $people = People::findOne(['user_id' => (int)$this->userId]);
            $transaction = Order::find(['user_id' => (int)$this->userId, 'type' => 'buy']);
            foreach ($transaction as $item) {
                $currentSellPrice = (Market::findOne(['_id' => $item->market_id]))->sell->price;
                $marketDeferencePrice[] = $currentSellPrice - $item->price;
            }

            $profit = array_sum($marketDeferencePrice);
            $allocate = $people->wallet->{$type};

            $wallet = [
                'available' => $allocate->available,
                'allocate' => $allocate->allocated,
                'profit' => $profit,
                'equity' => $allocate->available + $allocate->allocated + $profit
            ];

            return respond(UserWalletTransformer::transform($wallet))->success();
        }
        return respond()->fail();
    }
}