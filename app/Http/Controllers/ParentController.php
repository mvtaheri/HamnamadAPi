<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/28/17
 * Time: 12:24 AM
 */

namespace App\Http\Controllers;


use App\Helpers\JWT;
use App\Helpers\Parents as ParentsHelper;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Parents;
use App\Requests\Parent\GetCategoryOfParentRequest;
use App\Requests\Parent\GetParentRequest;
use App\Transformers\Market\MarketTransformer;
use App\Transformers\Parent\CategoryTransformer;
use App\Transformers\Parent\ParentTransformer;
use Psr\Container\ContainerInterface;

/**
 * Class CategoryController
 * @package App\Controllers
 */
class ParentController extends Controller
{
    /**
     * @var
     */
    protected $user_id;

    private $request;

    /**
     * CategoryController constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $token = $container['request']->getHeader('Authorization');
        $this->user_id = JWT::parse($token[0])->user_id;
        $this->request = request();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCategoryOfParent()
    {
        GetCategoryOfParentRequest::validate();
        $parentCategory = CategoryParent::whereIn('parent_id', [(int)request('id')]);

        foreach ($parentCategory as $category)
            $categories[] = Category::findById($category->category_id);


        return $this->respond(CategoryTransformer::transformArray($categories))->success();
    }

    public function getParents()
    {
        $parents = Parents::findAll()->all();

        return $this->respond(ParentTransformer::transformArray($parents))->success();
    }

    public function getMarketsOfParent()
    {
        GetParentRequest::validate();
        $markets = ParentsHelper::markets((int)$this->request['parent_id']);

        return $this->respond(MarketTransformer::transformArray($markets))->success();
    }

}
