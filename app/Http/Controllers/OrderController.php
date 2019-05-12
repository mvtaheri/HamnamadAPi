<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/25/17
 * Time: 1:54 PM
 */

namespace App\Http\Controllers;


use App\Helpers\JWT;
use App\Jobs\Order\Buy\SetBuyOrder;
use App\Jobs\Order\Buy\setFollowerBuyOrder;
use App\Jobs\Order\Sell\setFollowerSellOrder;
use App\Jobs\Order\Sell\SetSellOrder;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\User;
use App\Models\Wallet;
use App\Requests\Order\BuyRequest;
use App\Requests\Order\CancelOrderRequest;
use App\Requests\Order\SellRequest;
use App\Requests\Order\UpdateOrderRequest;
use Carbon\Carbon;
use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;

/**
 * Class OrderController
 * @package App\Controllers
 */
class OrderController extends Controller
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
     * OrderController constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $token=$container['request']->getHeader("Authorization");
        $this->userId = (int)JWT::parse($token[0])->user_id;
        $this->request = request();
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function buyOrder()
    {
        BuyRequest::validate();
        $this->walletTypeValidate(request('type'));

        $this->setOrderForSelf($this->dispatch(new SetBuyOrder($this->request, $this->userId)));
        $this->SetBuyOrderForFollowers();

        return $this->respond()->success();
    }

    /**
     * @param $type
     * @return bool|string
     */
    private function walletTypeValidate($type)
    {
        if ($type !== 'real' || $type !== 'virtual')
            return $this->respond('')->fail('wallet type is wrong');

        return true;
    }

    /**
     * @param $result
     * @return string
     */
    private function setOrderForSelf($result)
    {
        if (!is_object($result))
            return $this->respond('')->fail('');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function SetBuyOrderForFollowers()
    {
        if (User::findById((int)$this->userId)->type == 2)
            return $this->dispatch(new setFollowerBuyOrder($this->request, $this->userId));
    }

    /**
     * @return string
     */
    public function cancelOrder()
    {
        CancelOrderRequest::validate();
        $market = Wallet::deleteOne(['_id' => new ObjectId($this->request['order_id'])]);

        if ($market->getDeletedCount() > 0)
            return $this->respond("Order with id :{$this->request['order_id']} deleted ")->success();

        return $this->respond('delete action is failed')->fail();
    }

    public function updateBuyOrder()
    {
        UpdateOrderRequest::validate();
        $order = OrderBook::updateOneById($this->request['order_id'], [
            'take_profit' => (int)$this->request['take_profit'],
            'stop_loss' => (int)$this->request['stop_loss'],
        ]);
        $this->setOrderForSelf($order);

        return $this->respond('order updated')->success();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function sellOrder()
    {
        SellRequest::validate();
        $this->walletTypeValidate(request('type'));
        $this->setOrderForSelf($this->dispatch(new SetSellOrder($this->request, $this->userId)));
        $this->SetSellOrderForFollowers();

        return $this->respond()->success();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function SetSellOrderForFollowers()
    {
        if (User::findById((int)$this->userId)->type == 2)
            return $this->dispatch(new setFollowerSellOrder($this->request, $this->userId));
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function setBuyOrder()
    {
        BuyRequest::validate();
        $this->walletTypeValidate(request('type'));
        $order = OrderBook::insertOne([
            'market_id' => $this->request['market_id'],
            'user_id' => $this->userId,
            'count' => $this->request['count'],
            'wallet_type' => $this->request['type'],
            'type' => 'buy',
            'take_profit' => $this->request['take_profit'] ?? 0,
            'stop_loss' => $this->request['stop_loss'] ?? 0,
            'price' => $this->request['price'],
            'created_date' => time()
        ]);

        $this->setOrderForSelf($order);

        return $this->respond('order submitted')->success();
    }

    /**
     *
     */
    public function setSellOrder()
    {
        SellRequest::validate();
        $this->walletTypeValidate(request('type'));
        $order = OrderBook::insertOne([
            'market_id' => $this->request['market_id'],
            'user_id' => $this->userId,
            'count' => $this->request['count'],
            'wallet_type' => $this->request['type'],
            'type' => 'sell',
            'take_profit' => $this->request['take_profit'] ?? 0,
            'stop_loss' => $this->request['stop_loss'] ?? 0,
            'price' => $this->request['price'],
            'created_date' => time()
        ]);

        $this->setOrderForSelf($order);

        return $this->respond('order submitted')->success();
    }

}
