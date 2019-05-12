<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/1/17
 * Time: 7:33 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Setting
 * @package App\Models
 */
class Setting extends Eloquent
{

    /**
     * @var string
     */
    protected $table = "setting";

    /**
     * get setting for given language
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $lang
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithLang($query ,$lang){
       return $query->where('lang',$lang);
    }

}
