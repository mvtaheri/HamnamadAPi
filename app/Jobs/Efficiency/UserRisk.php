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
use Illuminate\Support\Carbon;

class UserRisk implements Job
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
     * @return array|mixed
     */
    public function handle()
    {
        $daily = DailyEfficiency::find([
            'wallet_type' => $this->request['wallet_type'],
        ], ['sort' => ['created_at' => 1]]);

        foreach ($daily as $day) {
            $result[] = $this->updateUserRisk($day);
        }

        return $result;
    }

    /**
     * @param $daily
     * @return array
     */
    private function updateUserRisk($daily)
    {

        $years = $this->generateYearsOfActivity($daily->created_at);
        $yearlyEfficiency = [];

        foreach ($years as $year) {
            $efficiency = DailyEfficiency::find([
                'user_id' => $daily->user_id,
                'wallet_type' => $this->request['wallet_type'],
                'date.year' => $year,
//                'value' => ['$gt' => 0]
            ]);
            if (!is_null($data = $this->calculateLimitationEfficiency($efficiency))) {
                $yearlyEfficiency[] = $data;
            }
        }

        \App\Models\UserRisk::updateOne(
            [
                'user_id' => $daily->user_id
            ],
            [
                'yearlyEfficiency' => $yearlyEfficiency
            ], ['upsert' => true]
        );

        return $yearlyEfficiency;
    }

    /**
     * @param $timestamp
     * return from years of timestamp to years of now EX:
     * @return array
     */
    private function generateYearsOfActivity($timestamp): array
    {
        $now = Carbon::now()->year;
        $firstEfficiencyYear = Carbon::createFromTimestamp($timestamp)->year;
        $years = [];
        for ($x = $firstEfficiencyYear; $x <= $now; $x++) {
            $years[] = $x;
        }
        return $years;
    }

    /**
     * @param $efficiencies
     * @return int
     */
    private function calculateLimitationEfficiency($efficiencies)
    {

        $months = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        ];


        foreach ($efficiencies as $efficiency) {
            foreach ($months as $key => $month) {
                $eff = (array)DailyEfficiency::find([
                    'user_id' => $this->userId,
                    'wallet_type' => $this->request['wallet_type'],
                    'date.year' => $efficiency->date->year,
                    'date.month' => $key + 1,
                ]);

                if (count($eff) > 0) {
                    $average = (array_sum(array_column($eff, 'value'))) / 30;
                    if ($efficiency->date->month == $key + 1) {
                        $monthlyEfficiencies[$month][] = pow(($efficiency->value  -  $average),2);
//                        $monthlyEfficiencies[$month][] = ($efficiency->value - (pow($average, 2)));
                    } else {
                        $monthlyEfficiencies[$month][] = 0;
                    }
                } else {
                    $monthlyEfficiencies[$month][] = 0;
                }


            }
        }

        foreach ($monthlyEfficiencies as $key => $monthlyEfficiency) {
            if (array_sum($monthlyEfficiency) == 0) {
                $monthlyEfficiencies[$key]=0;
                continue;
            }
            $monthlyEfficiencies[$key] = sqrt((array_sum($monthlyEfficiency)) / 30);
        }

        if (!is_null($year = $efficiencies[0]->date->year)) {
            $result[$year]['month'] = $monthlyEfficiencies;
        }

        return $result;
    }

    /**
     * @param $daily
     */
    private function checkCountOfDailyEfficiencyIsZero($daily): void
    {
        if (count($daily) < 1) {
            die(respond()->fail('Efficiency with this wallet type not exist'));
        }
    }

}