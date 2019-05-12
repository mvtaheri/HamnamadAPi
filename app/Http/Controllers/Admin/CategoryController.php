<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/28/17
 * Time: 12:24 AM
 */

namespace App\Controllers\Admin;


use App\Controllers\Controller;
use App\Helpers\JWT;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Parents;
use App\Requests\Admin\Category\AddCategoryRequest;
use App\Requests\Admin\Category\CategoryRequest;
use App\Requests\Admin\Category\UpdateCategoryRequest;
use App\Traits\Admin\AdminValidate;
use App\Transformers\Admin\Category\GetCategoryTransformer;
use App\Transformers\Category\AllCategories;
use App\Transformers\Parent\ParentTransformer;

/**
 * Class CategoryController
 * @package App\Controllers
 */
class CategoryController extends Controller
{
    use AdminValidate;
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var mixed
     */
    protected $request;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->userId = JWT::parse(request('token'))->user_id;
        $this->AdminUserValidation($this->userId);
        $this->request = request();

    }

    /**
     * @return string
     */
    public function getCategories()
    {
        $categories = Category::findAll()->all();
        $data = [];
        foreach ($categories as $key => $category) {
            $data[$key] = $category;
            $data[$key]->parent = Parents::getParentOfCategory($category->id);
        }

        return $this->respond(AllCategories::transformArray($categories))->success();
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        CategoryRequest::validate();
        $category = Category::findById($this->request['id']);
        $category->parent_title = Category::findById($category->parent_id)->title ?? 'Main Menu';
        $category->parents = ParentTransformer::transformArray(Parents::findAll()->all());

        return $this->respond(GetCategoryTransformer::transform($category))->success();
    }

    /**
     *
     */
    public function addCategory()
    {
        AddCategoryRequest::validate();
        $category = Category::insert([
            'title' => $this->request['title'],
            'description' => $this->request['description'],
            'parent_id' => $this->request['type']
        ]);

        if (!$category)
            return $this->respond('insert action is failed')->fail();

        CategoryParent::insert([
            'parent_id' => (int)$this->request['parent_id'],
            'category_id' => $category
        ]);

        return $this->respond("Category with id :{$category} inserted ")->success();
    }

    public function removeCategory()
    {
        CategoryRequest::validate();

        $id = (int)$this->request['id'];
        CategoryParent::deleteWhere("category_id", [$id]);

        if ($category = Category::deleteById($id) < 1)
            return $this->respond('delete action is failed')->fail();


        return $this->respond("Category with id :{$this->request['category_id']} deleted ")->success();

    }


    public function updateCategory()
    {
        UpdateCategoryRequest::validate();
        $category = Category::updateOneById((int)$this->request['id'],
            [
                'title' => $this->request['title'],
                'description' => $this->request['description'],
                'parent_id' => (int)$this->request['type']
            ]
        );
        $parent = CategoryParent::updateOne(
            'category_id',
            $this->request['id'],
            ['parent_id' => $this->request['parent_id']]
        );

        if ($category < 1 && $parent < 1)
            return $this->respond('update action is failed')->fail();


        return $this->respond("category with id :{$this->request['id']} updated ")->success();
    }

}