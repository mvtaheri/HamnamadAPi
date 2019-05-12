<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 10/1/17
 * Time: 5:31 PM
 */

namespace App\Jobs\Order\Sell;


use App\Contract\Job\Job;
use App\Jobs\Order\SetOrder;
use App\Models\People;
use App\Models\Wallet;
use App\Traits\Order\UserWallet;
use MongoDB\BSON\ObjectID;

class setFollowerSellOrder implements Job
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
     * setFollowerBuyOrder constructor.
     * @param $request
     * @param $userId
     */
    public function __construct($request, $userId)
    {
        $this->request = $request;
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $user = People::findByUserId((int)$this->userId);
        $costOfOrder = $this->CalculateCostOfAOrder($this->request['price'], $this->request['count']);

        foreach ($user->follower as $follower) {
            $wallet = $this->getWalletAUserAccordingWalletType($follower->user_id);
            if (!$wallet) continue;
            if ($wallet->total < (int)$this->request['count']) continue;

            $this->setOrderForAUser($follower->user_id);
            $this->incrementAvailableWalletAccordingType($follower, $this->request['type'], $costOfOrder);
        }

    }

    /**
     * @param $followerId
     * @return mixed
     */
    private function getWalletAUserAccordingWalletType(int $followerId)
    {
        return Wallet::findOne([
            'user_id' => $followerId,
            'market_id' => new ObjectID($this->request['market_id']),
            'wallet_type' => $this->request['type']
        ]);
    }

    private function setOrderForAUser($followerId)
    {
        $this->request['order_type'] = 'sell';

        return dispatch(new SetOrder($followerId, $this->request));
    }

    private function incrementAvailableWalletAccordingType($followerId, $type, $costOfOrder)
    {
        return People::incrementAvailableWallet(
            $followerId,
            $type,
            $costOfOrder
        );
    }
}