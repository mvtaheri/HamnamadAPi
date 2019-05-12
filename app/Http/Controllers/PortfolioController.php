<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/12/2019
 * Time: 9:57 AM
 */

namespace App\Http\Controllers;


use App\Model\Market;
use App\Model\Order;
use Carbon\Carbon;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    /**
     * @return string
     */
    public function getPortfolioHistory(Request $request,Order $order)
    {
        $this->validate($request, [
            'wallet_type' => 'required|in:real,virtual',
            'limit' => 'sometimes|required'
        ]);
        $query=$order->newQuery();
        if ($request->has('limit')) {
            switch ($request->limit) {
                case '1d':
                    $query->where('created_at','>',Carbon::now()->subDays(1)->timestamp);
                    break;
                case '7d':
                    $query->where('created_at' ,'>' ,Carbon::now()->subDays(7)->timestamp);
                    break;
                case '30d':
                    $query->where('created_at','>' ,Carbon::now()->subDays(30)->timestamp);
                    break;
                case '1w':
                    $query->where('created_at' ,'>' ,Carbon::now()->subWeek(1)->timestamp);
                    break;
                case '1m':
                    $query->where('created_at','>' ,Carbon::now()->subMonth(1)->timestamp);
                    break;
                case '3m':
                    $query->where('created_at','>',Carbon::now()->subMonth(3)->timestamp);
                    break;
                case '6m':
                    $query->where('created_at' ,'>' ,Carbon::now()->subMonth(6)->timestamp);
                    break;
                case '1y':
                    $query->where('created_at','>' ,Carbon::now()->subYear(1)->timestamp);
                    break;
            }
        }
        $orders=$query->whereNotNull('market_id')->get();
        $finalOrders = $this->addDetailToMarket($orders);

        return $this->respond(OrderTransformer::transformArray($finalOrders))->success();
    }

    /**
     * @param $orders
     * @return mixed
     */
    private function addDetailToMarket($orders)
    {
        $markets=$orders->map(function ($order){
           return $order->market;
        });
        foreach ($orders as $key => $order) {
            $market = Market::findById($order['market_id']);
            $orders[$key]['market'] = $market;
            $orders[$key]['market']->category = Category::getCategoryOfMarket((string)$market->_id)[0];
            $orders[$key]['market']->alert = Alert::has($this->userId, (string)$market->_id);
            $orders[$key]['market']->change = ($market->sell->price - $market->sell->last_price) * 0.01;
        }

        return $orders;
    }

    /**
     * @return string
     */
    public function portfolioPage()
    {
        PortfolioRequest::validate();
        $query = [
            'user_id' => $this->userId,
            'wallet_type' => $this->request['type'],
            'type' => 'sell'
        ];
        $orders = Order::find($query);
        $finalOrders = $this->addDetailToMarket($orders);

        return $this->respond(OrderTransformer::transformArray($finalOrders))->success();
    }

    /**
     * @return string
     */
    public function getPortfolioOrders()
    {
        PortfolioRequest::validate();
        $orders = Wallet::find(['user_id' => $this->userId, 'wallet_type' => $this->request['type']]);
        $finalOrders = $this->addDetailToMarket($orders);

        return $this->respond(WalletTransformer::transformArray($finalOrders))->success();
    }


}
