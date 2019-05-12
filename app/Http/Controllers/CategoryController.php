<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/28/17
 * Time: 12:24 AM
 */

namespace App\Http\Controllers;

use App\Jobs\Market\GetMarket;
use App\Models\Category;
use App\Models\Market;
use App\Models\MarketCategory;
use App\Requests\Category\GetMarketOfCategoryRequest;
use App\Transformers\Category\AllCategories;
use App\Transformers\Market\MarketTransformer;
use MongoDB\BSON\ObjectID;

/**
 * Class CategoryController
 * @package App\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var
     */
    protected $user_id;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->user_id = JWT::parse(request('token'))->user_id;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMarketsOfCategory()
    {
        GetMarketOfCategoryRequest::validate();

        $marketOfCategory = MarketCategory::whereIn('category_id', [(int)request('id')]);

        foreach ($marketOfCategory as $market)
            $markets[] = $this->dispatch(new GetMarket($this->user_id, ['id' => $market->market_id]));


        return $this->respond(MarketTransformer::transformArray($markets))->success();
    }

    public function getCategories()
    {
        $categories = Category::findAll()->all();

        return $this->respond(AllCategories::transformArray($categories))->success();
    }

}
