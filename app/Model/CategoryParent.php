<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/18/17
 * Time: 12:15 PM
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CategoryParent extends Eloquent
{
    protected  $table = 'category_parent';

//    public static function getParentOfCategory()
//    {
//        return self::leftJoin(
//            'parent', 'category_parent.category_id', '=', 'parent.id'
//        )->get();
//    }

}
