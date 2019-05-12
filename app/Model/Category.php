<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/18/17
 * Time: 12:07 PM
 */

namespace App\Model;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Category extends Eloquent
{ use HybridRelations;

    protected $table='categories';

    protected $connection='mysql';


    public function child(){
        return $this->hasMany(Category::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(Category::class,'id','parent_id');
    }

    public function main_parent(){
        return $this->belongsToMany(Parents::class,'category_parent','category_id');
    }

    public function market(){
        return $this->hasMany(Market::class);
    }


    public static function getCategoryOfMarket($marketId)
    {
       return DB::table('categories')
            ->join('market_category',function ($join) use ($marketId){
                $join->on('categories.id','=' ,'market_category.category_id')
                    ->where('market_category.market_id','=', $marketId);
            })->get();
    }

}
