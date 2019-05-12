<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/21/2018
 * Time: 8:29 PM
 */

namespace App\Helpers;


use App\Models\CategoryParent;
use App\Models\Market;
use App\Models\MarketCategory;
use MongoDB\BSON\ObjectId;

/**
 * Class Parents
 * @package App\Helpers
 */
class Parents
{
    /**
     * @param int $parentId
     * @param null $limit
     * @return array|mixed
     */
    public static function markets(int $parentId, $limit = null)
    {
        $parents = CategoryParent::whereIn('parent_id', [$parentId]);
        $categoryIds = array_column($parents, 'category_id');
        $markets = MarketCategory::whereIn('category_id', $categoryIds);
        $marketIds = array_column($markets, 'market_id');

        foreach ($marketIds as $marketId) {
            $allMarkets[] = new ObjectId($marketId);
        }

        if (!isset($allMarkets) || count($allMarkets) < 1) {
            return [];
        }
        $query['_id'] = ['$in' => $allMarkets];
        if ($limit) {
            return Market::find($query, ['limit' => $limit]);
        }

        return Market::find($query);
    }

}