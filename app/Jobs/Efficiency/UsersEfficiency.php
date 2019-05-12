<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/28/2018
 * Time: 8:46 AM
 */

namespace App\Jobs\Efficiency;


use App\Contract\Job\Job;
use App\Models\DailyEfficiency;
use App\Models\Order;
use App\Models\People;
use App\Models\User;

/**
 * Class UsersEfficiency
 * @package App\Jobs\Efficiency
 */
class UsersEfficiency implements Job
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
        $this->userId = $userId;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $users = User::ActiveUsers();

        foreach ($users as $user) {
            $this->CalculateEfficiencyByWalletType($user, 'virtual');
            $this->CalculateEfficiencyByWalletType($user, 'real');
        }
    }


    /**
     * @param $user
     * calculaet efficiency based on (E1+D)-(E0+W)\(E0+W)
     * @param $walletType
     */
    private function CalculateEfficiencyByWalletType($user, $walletType)
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;

        $people = People::findOne(['user_id' => $user->id]);
        $deposit = Order::TodaySells($user->id, $walletType);
        $withdrawal = Order::TodayBuys($user->id, $walletType);

        if ($people && count($deposit) > 0 && count($withdrawal) > 0) {
            $available = ((int)$people->wallet->{$walletType}->available) ?? 0;
            $morningAvailable = ((int)$people->wallet->{$walletType}->morning_available) ?? 0;
            $sumDeposit = $this->calculateSumDeposit($deposit);
            $sumWithdrawal = $this->calculateSumDeposit($withdrawal);

            $dailyEfficiency = (($available + $sumWithdrawal) - ($morningAvailable + $sumDeposit))
                / ($morningAvailable + $sumDeposit);

            DailyEfficiency::add($user->id, $walletType, $dailyEfficiency);
        }

    }

    /**
     * @param $data
     * @return float|int
     */
    private function calculateSumDeposit($data)
    {
        $sumDeposit = 0;
        foreach ($data as $item) {
            $value = $item->count_unit * $item->price;
            $sumDeposit += $value;
        }
        return $sumDeposit;
    }
}