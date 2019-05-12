<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/18/17
 * Time: 12:15 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;

use Jenssegers\Mongodb\Eloquent\HybridRelations;

class MarketCategory extends Eloquent
{ use HybridRelations;

    protected $table='market_category';


    public function categories() {

        return $this->belongsTo(Category::class);

    }

    public function market(){
        return $this->belongsTo(Market::class);
    }


//    public static function getCategoryOfMarket()
//    {
//        return self::leftJoin(
//            'categories', 'market_category.category_id', '=', 'categories.id'
//        )->get();
//    }

}
