<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/2/2018
 * Time: 1:12 AM
 */

namespace App\Controllers\Admin;


use App\Controllers\Controller;
use App\Helpers\JWT;
use App\Models\CategoryParent;
use App\Models\Parents;
use App\Requests\Admin\Parent\AddParentRequest;
use App\Requests\Admin\Parent\DeleteParentRequest;
use App\Requests\Admin\Parent\GetParentRequest;
use App\Requests\Admin\Parent\UpdateParentRequest;
use App\Traits\Admin\AdminValidate;
use App\Transformers\Parent\ParentTransformer;

/**
 * Class ParentController
 * @package App\Controllers\Admin
 */
class ParentController extends Controller
{
    use AdminValidate;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var array
     */
    protected $request;

    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $this->userId = (int)JWT::parse(request('token'))->user_id;
        $this->AdminUserValidation($this->userId);
        $this->request = request();
    }

    public function getLatestParent()
    {
        $parents = Parents::findAll()->all();

        return $this->respond(ParentTransformer::transformArray($parents))->success();
    }

    /**
     *
     */
    public function getParent()
    {
        GetParentRequest::validate();
        $parent = Parents::findById((int)$this->request['id']);

        return $this->respond($parent)->success();
    }

    /**
     *
     */
    public function addParent()
    {
        AddParentRequest::validate();

        if (!Parents::insert(['title' => $this->request['title']])) {
            return $this->respond()->fail('action is failed');
        }

        return $this->respond('action is success')->success();
    }

    /**
     *
     */
    public function updateParent()
    {
        UpdateParentRequest::validate();

        if (!Parents::updateOneById((int)$this->request['id'], ['title' => $this->request['title']])) {
            return $this->respond()->fail('action is failed');
        }

        return $this->respond('action is success')->success();
    }

    /**
     *
     */
    public function removeParent()
    {
        DeleteParentRequest::validate();
        $id = (int)$this->request['id'];
        CategoryParent::deleteWhere("parent_id", [$id]);

        if (Parents::deleteById($id) < 1)
            return $this->respond('delete action is failed')->fail();

        return $this->respond("Parent with id :{$this->request['id']} deleted ")->success();

    }
}