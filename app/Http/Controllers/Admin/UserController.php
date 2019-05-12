<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/27/2017
 * Time: 12:06 AM
 */

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\JWT;
use App\Jobs\Admin\Auth\UserAuthentication;
use App\Models\User;
use App\Requests\Admin\User\UpdateUserRequest;
use App\Requests\Admin\User\UserRequest;
use App\Requests\Auth\AuthRequest;
//use App\Traits\Admin\AdminValidate;
use App\Transformers\Admin\User\UserTransformer;


/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends Controller
{
//    use AdminValidate;

    /**
     * @var mixed
     */
    protected $request;

    /**
     * @var int
     */
    protected $userId;


    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->request = request();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function authentication()
    {
        AuthRequest::validate();

        return $this->dispatch(new UserAuthentication($this->request));
    }

    /**
     *
     */
    public function userList()
    {
//        $this->AdminValidateByToken();
        $users = User::findAll()->all();

        return $this->respond(UserTransformer::transformArray($users))->success();
    }

    /**
     * @return string
     */
    public function getUser()
    {
//        $this->AdminValidateByToken();
        UserRequest::validate();
        $user = User::findById($this->request['user_id']);

        return $this->respond(UserTransformer::transform($user))->success();
    }

    /**
     * @return string
     */
    public function removeUser()
    {
        $this->AdminValidateByToken();
        UserRequest::validate();
        $user = User::deleteById($this->request['user_id']);
        if ($user > 0)
            return $this->respond("User with id :{$this->request['user_id']} deleted ")->success();

        return $this->respond('delete action is failed')->fail();
    }

    public function updateUser()
    {
        $this->AdminValidateByToken();
        UpdateUserRequest::validate();
        $user = User::updateOneById($this->request['user_id'],
            [
                'is_admin' => $this->request['admin'],
                'enabled' => $this->request['user_status']
            ]
        );

        if ($user > 0)
            return $this->respond("user with id :{$this->request['user_id']} updated ")->success();

        return $this->respond('update action is failed')->fail();
    }

    public function AdminValidateByToken()
    {
        $this->userId = (int)JWT::parse(request('token'))->user_id;
        $this->AdminUserValidation($this->userId);
    }


}