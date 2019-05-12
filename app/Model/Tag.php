<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/8/17
 * Time: 6:51 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Tag
 * @package App\Models
 */
class Tag extends Eloquent
{
    /**
     * @var string
     */

    protected $table ="tag";

    protected $guarded=[];

    public $timestamps=false;

    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $market_id;
    /**
     * @var int
     */
    protected $feed_id;

}
