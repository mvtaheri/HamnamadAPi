<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/26/17
 * Time: 12:12 AM
 */

namespace App\Jobs\Order\Buy;


use App\Contract\Job\Job;
use App\Jobs\Order\SetOrder;
use App\Models\People;
use App\Traits\Order\UserWallet;

/**
 * Class SetBuyOrder
 * @package App\Jobs\Order
 */
class SetBuyOrder implements Job
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
     * SetBuyOrder constructor.
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
        $costOfOrder = $this->CalculateCostOfAOrder($this->request['price'], $this->request['count']);
        $this->checkAvailableInMyWallet(
            $this->getUserWalletDetailByWalletType($this->request['type'], $this->userId),
            $this->getUnitPriceOfMarket($this->request['market_id'], $this->request['price']),
            $costOfOrder
        );
        $this->request['order_type'] = 'buy';
        $this->setBuyOrderForAMarketByAUser();

        return People::decrementAvailableWallet(
            $this->userId,
            $this->request['type'],
            $costOfOrder
        );
    }


    private function checkAvailableInMyWallet($availableOfUserWallet, $priceOfUnitMarket, $numberOfUnitMarket)
    {
        if ($availableOfUserWallet < $priceOfUnitMarket ||
            $availableOfUserWallet < $numberOfUnitMarket)
            die(respond('')->fail('Your available is lower than price of a market unit'));

        return true;
    }

    private function setBuyOrderForAMarketByAUser()
    {
        $setOrder = dispatch(new SetOrder($this->userId, $this->request));
        if (!is_object($setOrder))
            die(respond('')->fail('Set Buy Order Not Complete'));

        return true;
    }

}