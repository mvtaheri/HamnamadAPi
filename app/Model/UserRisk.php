<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/18/2018
 * Time: 7:42 AM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserRisk extends Eloquent
{

    protected $connection ="mongodb";

    protected  $collection = "user_efficiency";
}
