<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/28/2018
 * Time: 8:50 AM
 */

namespace App\Jobs\Efficiency;


use App\Contract\Job\Job;
use App\Models\Efficiency;
use App\Models\Order;

class UserTrades implements Job
{
    /**
     * @var
     */
    private $userId;

    private $request;

    /**
     * UsersEfficiency constructor.
     * @param $userId
     * @param $request
     */
    public function __construct($userId, $request)
    {
        $this->userId = (int)$userId;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function handle()
    {

        $walletType = $this->request['wallet_type'];
        $totalTrades = Order::count(['user_id' => $this->userId, 'wallet_type' => $walletType]);


        $this->checkCountOfUserTrades($totalTrades);
        $positiveTrades = (array)Efficiency::find(['user_id' => $this->userId, 'value' => ['$gt' => 0]]);
        $negativeTrades = (array)Efficiency::find(['user_id' => $this->userId, 'value' => ['$lt' => 0]]);

        $positiveTradesAverage = $this->calculateTradesAverage($positiveTrades);
        $negativeTradesAverage = $this->calculateTradesAverage($negativeTrades);

        $positiveTradesPercent = (100 * count($positiveTrades)) / $totalTrades;


        return [
            'total_trades'            => $totalTrades,
            'positive_trades_average' => $positiveTradesAverage,
            'negative_trades_average' => $negativeTradesAverage,
            'positive_trades_percent' => $positiveTradesPercent
        ];
    }

    /**
     * @param $totalTrades
     */
    private function checkCountOfUserTrades($totalTrades): void
    {
        if ($totalTrades == 0) {
            die(respond()->fail('count of trades is zero'));
        }
    }

    /**
     * @param $positiveTrades
     * @return float|int
     */
    private function calculateTradesAverage($positiveTrades)
    {
        if (count($positiveTrades) < 1)
            return 0;

        return ((array_sum(array_column($positiveTrades, 'value'))) / count($positiveTrades));
    }
}