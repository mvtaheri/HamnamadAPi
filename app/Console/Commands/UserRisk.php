<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/17/2019
 * Time: 5:18 PM
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;

class UserRisk extends Command
{
    protected $signature = 'UserRisk';

    protected $description = 'Calculate User Risk';

    public function handle()
    {

        $logger = $this->container->get('logger');

        $monthName = array(
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december'
        );
        // Access items in container
        $settings = $this->container->get('settings');
        $loger = $this->container->get('logger');
        $loger->debug("hello Word");
        // Throw if no arguments provided
        if (empty($args)) {
            throw new RuntimeException("No arguments passed to command");
        }
        try {
            $user_has_risk = DailyEfficiency::aggregate([
                ['$group' => ['_id' => '$user_id', 'WalletType' => ['$addToSet' => '$wallet_type'], 'FirstDateActivite' => ['$max' => '$created_at']]]
            ])->toArray();

            $date = explode('-', Carbon::yesterday('Asia/Tehran')->toDateString());
            $year = (int)$date[0];
            $month = (int)$date[1];
            $day = (int)$date[2];

            foreach ($user_has_risk as $userid) {

                $user_daily_efficiency = DailyEfficiency::find([
                    'user_id' => $userid->_id,
                    'wallet_type' => 'virtual',
                    'date.year' => $year,
                    'date.month' => $month
                ]);
                $average = array_sum(array_column((array)$user_daily_efficiency, 'value')) / $day;
                $sum_value = 0;
                foreach ($user_daily_efficiency as $ude) {
                    $sum_value += pow(($ude->value - $average), 2);
                }
                $user_month_risk = sqrt(($sum_value / $day));
                \App\Models\UserRisk::updateOne(
                    [
                        'user_id' => $userid->_id
                    ],
                    [
                        'yearlyEfficiency.' . $year . '.month.' . $monthName[$month] => [
                            'risk' => $user_month_risk,
                            'update_at' => Carbon::now('Asia/Tehran')->toDateString()
                        ]
                    ], ['upsert' => true]
                );
            }
            $logger->info('Success Run User Risk');

            return "success Run User Risk !";
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        }
    }

}
