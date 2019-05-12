<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/27/2018
 * Time: 6:28 AM
 */

namespace App\Jobs\Efficiency;


use App\Contract\Job\Job;
use App\Models\DailyEfficiency;
use Carbon\Carbon;

/**
 * Class UserEfficiency
 * @package App\Jobs\Efficiency
 */
class UserEfficiencySync implements Job
{
    /**
     * @var
     */
    private $request;

    /**
     * UserEfficiencySync constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return array|mixed
     */
    public function handle()
    {

        $daily = DailyEfficiency::find(
            ['wallet_type' => $this->request['wallet_type']],
            ['$sort' => ['created_at' => 1]]
        );

        foreach ($daily as $day) {
            $result[] = $this->updateUserEfficiency($day);
        }

        return $result;
    }

    /**
     * @param $daily
     * @return array
     */
    private function updateUserEfficiency($daily): array
    {
        $years = $this->generateYearsOfActivity($daily->created_at);
        foreach ($years as $year) {
            $efficiency = DailyEfficiency::find([
                'user_id'     => $daily->user_id,
                'wallet_type' => $this->request['wallet_type'],
                'date.year'   => $year
            ]);

            $yearlyEfficiency[] = $this->calculateLimitationEfficiency($efficiency);
        }

        \App\Models\UserEfficiency::updateOne(
            [
                'user_id' => $daily->user_id
            ],
            [
                $yearlyEfficiency
            ], ['upsert' => true]
        );
        return $yearlyEfficiency;
    }

    /**
     * @param $timestamp
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
            $sumYearlyEfficiency[] = $efficiency->value + 1;
            foreach ($months as $key => $month) {
                if ($efficiency->date->month == $key + 1) {
                    $monthlyEfficiencies[$month][] = $efficiency->value + 1;
                } else {
                    $monthlyEfficiencies[$month] = 0;
                }
            }
        }

        foreach ($monthlyEfficiencies as $key => $monthlyEfficiency) {
            if ($monthlyEfficiency == 0) {
                continue;
            }
            $monthlyEfficiencies[$key] = array_product($monthlyEfficiency) - 1;
        }
        $year = $efficiencies[0]->date->year;
        $result['year'][$year] = array_product($sumYearlyEfficiency) - 1;
        $result['month'] = $monthlyEfficiencies;

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