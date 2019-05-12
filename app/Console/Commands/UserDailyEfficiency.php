<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/17/2019
 * Time: 5:11 PM
 */

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;


class UserDailyEfficiency extends Command
{
    protected $signature = 'UserDailyEfficiency';

    protected $description = 'Calculate User Daily Efficiency';


    public function handle()
    {
        try {
            $users = User::ActiveUsers();
            foreach ($users as $user) {
                $people = People::findOne(['user_id' => $user->id]);
                if (!$people)
                    continue;
                $d = Transaction::findTodayUserTransaction($user, 'deposit');
                $deposit=array_sum(array_column($d,'amount'));
                $w = Transaction::findTodayUserTransaction($user, 'withdraw');
                $withdrawal=array_sum(array_column($w,'amount'));
                $available = ((int)$people->wallet->virtual->available) ?? 0;
                $morningAvailable = (array_key_exists('morning_available',$people->wallet->virtual))? (int)$people->wallet->virtual->morning_available : 0;
                $dailyEfficiency=($morningAvailable!=0)?(($available-$morningAvailable) -($deposit-$withdrawal))/$morningAvailable :0 ;
                DailyEfficiency::removeDublicateEntityOnToday($user->id);
                $daily_efficiency[$user->id]=DailyEfficiency::add($user->id, 'virtual', $dailyEfficiency);
                People::setMorningAvailableAfterCalc_DailyEfficiency($user->id,$available,'virtual');
            }
            $logger->info('Successfully Update Daily Efficiency Document');
            return "Successfully Update Daily Efficiency Document";


        }catch(\Exception $runtimeException){
            $logger->info($runtimeException->getMessage());
            return $runtimeException->getMessage();
        }

    }
}
