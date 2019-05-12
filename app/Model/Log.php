<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/25/17
 * Time: 8:47 PM
 */

namespace App\Model;


use App\Components\UserOrder;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;

/**
 * Class Order
 * @package App\Models
 */
class Log extends Eloquent
{

    /**
     * @var string
     */
    protected $collection = "log";

    protected $connection ='mongodb';

}
