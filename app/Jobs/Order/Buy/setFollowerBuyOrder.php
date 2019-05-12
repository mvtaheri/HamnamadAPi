<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 10/1/17
 * Time: 12:23 PM
 */

namespace App\Jobs\Order\Buy;


use App\Contract\Job\Job;
use App\Jobs\Order\SetOrder;
use App\Models\People;
use App\Traits\Order\UserWallet;

class setFollowerBuyOrder implements Job
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
        $costOfOrder = $this->CalculateCostOfAOrder($this->request['price'], $this->request['count']);
        $user = People::findByUserId((int)$this->userId);

        foreach ($user->follower as $follower) {
            if ($follower->status == false || $follower->amount < $costOfOrder) continue;

            $userAvailable = $this->getUserWalletDetailByWalletType($this->request['type'], (int)$this->userId);
            $unitPrice = $this->getUnitPriceOfMarket($this->request['market_id'], (int)$this->request['price']);
            if ($userAvailable < $unitPrice || $userAvailable < $costOfOrder) continue;

            $this->setOrderForAUser($follower->user_id);
            $this->decrementAvailableWalletAccordingType($follower, $this->request['type'], $costOfOrder);
        }

    }


    private function setOrderForAUser($followerId)
    {
        $this->request['order_type'] = 'buy';

        return dispatch(new SetOrder($followerId, $this->request));
    }

    private function decrementAvailableWalletAccordingType($followerId, $type, $costOfOrder)
    {
        return People::decrementAvailableWallet(
            $followerId,
            $type,
            $costOfOrder
        );
    }
}