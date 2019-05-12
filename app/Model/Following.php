<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/28/2019
 * Time: 12:01 PM
 */

namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Following extends Eloquent
{

    protected $connection='mongodb';

    protected $collection="following";

    protected $guarded=['_id'];
}
