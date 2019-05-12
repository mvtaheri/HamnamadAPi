<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/3/2018
 * Time: 11:47 PM
 */

namespace App\Jobs\Efficiency;


use App\Contract\Job\Job;
use App\Models\DailyEfficiency;
use App\Requests\Efficiency\UserEfficiencyRequest;
use Illuminate\Support\Carbon;

class UserRiskDetail implements Job
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
        $limit = (int)$this->request['limit'];

        $limitTimestamp = Carbon::now()->subDays($limit)->timestamp;
        $this->checkDateLimitationInserted($limitTimestamp, $limit);
        $walletType = $this->request['wallet_type'];
        $daily = DailyEfficiency::getByDateLimited($this->userId, $walletType, $limitTimestamp);
        $this->checkCountOfDailyEfficiencyIsZero($daily);

        if (count($daily) > 0) {
            return $this->calculateLimitationEfficiency($daily);
        }
        return 0;
    }

    /**
     * @param $limitTimestamp
     * @param $limit
     * @return mixed
     */
    private function checkDateLimitationInserted($limitTimestamp, $limit)
    {
        $daily = DailyEfficiency::findOne([
            'user_id' => $this->userId,
            'wallet_type' => $this->request['wallet_type'],
        ], ['sort' => ['created_at' => 1]]);

        $limitDate = Carbon::now()->subDays($limit)->day;
        if (is_null($daily))
            return 0;
        if ($daily->created_at > $limitTimestamp && $daily->date->day !== $limitDate) {
            return 0;
        }

        return $daily;
    }

    /**
     * @param $daily
     * @return int
     */
    private function checkCountOfDailyEfficiencyIsZero($daily)
    {
        if (count($daily) < 1) {
            return 0;
        }
    }

    /**
     * @param $efficiencies
     * @return array
     */
    private function calculateLimitationEfficiency($efficiencies)
    {

        foreach ($efficiencies as $efficiency) {

            $average = (array_sum(array_column($efficiency, 'value'))) / 30;
            $limitEfficiencies[] = ($efficiency->value - (pow($average, 2)));
        }

        foreach ($limitEfficiencies as $key => $limitEfficiency) {
            if ($limitEfficiencies == 0) {
                continue;
            }
            $limitEfficiencies[$key] = sqrt((array_sum($limitEfficiencies)) / 30);
        }

        return $limitEfficiencies;
    }
}
