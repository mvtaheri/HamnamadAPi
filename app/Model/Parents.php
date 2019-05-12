<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/18/17
 * Time: 12:07 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;

class Parents extends Eloquent
{

    protected $table ='parents';


    public function category(){

        return $this->belongsToMany(Category::class,'category_parent','parent_id');
    }

//    public static function getParentOfCategory($categoryId)
//    {
//        return self::leftJoinWhere(
//            'category_parent', 'parents.id', '=', 'category_parent.parent_id'
//            , ['column' => 'category_parent.category_id', 'operator' => '=', 'value' => $categoryId])->get()->toArray();
//    }

}
