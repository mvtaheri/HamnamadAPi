<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/10/17
 * Time: 12:33 AM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class OrderBook extends Eloquent
{

    protected $connection ="mongodb";

    protected $collection = "order_book";

}
