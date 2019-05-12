<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/8/2018
 * Time: 9:35 AM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class IntradayTrades extends Eloquent
{

    protected $collection="intraday_trades";

    protected $connection ='mongodb';

}
