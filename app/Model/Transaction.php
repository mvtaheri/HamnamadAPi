<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/10/2019
 * Time: 10:45 AM
 */

namespace App\Model;

use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Transaction extends Eloquent
{

    protected $connection ="mongodb";

    protected $collection = 'transaction';


    /**
     * get Transaction For cash deposit And withdraw for user on today
     * @param $user
     * @param $type
     * @return mixed
     */
    public static function findTodayUserTransaction($user, $type)
    {
        return $deposit = Transaction::find([
            'user_id' => $user->id,
            'type' => $type,
            'description.' . $type => 'cash',
            'date' =>Carbon::now('Asia/Tehran')->toDateString()
        ]);
    }

}
