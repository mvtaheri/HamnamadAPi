<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/12/17
 * Time: 1:22 AM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Alert extends Eloquent
{

      protected $connection='mongodb';
      protected  $collection = "alert";
      protected $guarded =['_id'];


    public static function marketHasAlertbyUser($userId, $marketId)
    {
        return self::where('user_id',$userId)->where('market_id',$marketId)->first();
    }

    public function market(){
        return $this->belongsTo(Market::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
